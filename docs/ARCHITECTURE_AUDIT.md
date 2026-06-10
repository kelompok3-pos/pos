# PHP Native POS Architecture Audit and Documentation

Audit date: 2026-06-10

Scope: current PHP native POS codebase, single MySQL database, session authentication,
and the roles `super_admin`, `admin`, and `kasir`.

## 1. Architecture Audit

### 1.1 Executive finding

The proposed single-database multi-tenant model is appropriate for this system, but
the current codebase does not yet enforce it consistently. The most important rule is:

> Every tenant-owned row must carry its own non-null `store_id`, and every tenant
> read or write must match that `store_id` in the SQL statement itself.

Inferring a transaction's store from its cashier, checking ownership only in PHP, or
allowing an optional `store_id` filter is not sufficient for production isolation.

The codebase currently uses the role name `super_admin`. Keep this spelling everywhere.
Do not mix it with `superadmin`.

### 1.2 What is already implemented well

- Login uses `password_verify()` and user creation uses `password_hash()`.
- Login and logout regenerate the session ID.
- Important POST actions generally use CSRF verification.
- PDO native prepared statements are enabled with emulated prepares disabled.
- Role hierarchy helpers prevent Admin from creating or managing another Admin.
- Admin user-management operations verify that the target cashier is assigned to the
  current Admin.
- Transaction creation uses a database transaction and row locking for stock updates.
- A missing store assignment for Admin/Kasir fails closed in `currentStoreId()` by
  returning `0`.

### 1.3 Critical findings

#### CRITICAL-01: Products are global and have no tenant ownership

Evidence:

- `database/pos_db.sql`: `products` has no `store_id`.
- `app/Models/Product.php`: every list, read, create, update, delete, and stock query
  operates without a store predicate.
- `AdminProductController` permits both Super Admin and Admin to use the same global
  product methods.

Impact:

- An Admin sees and can modify every store's products.
- A Cashier sees products from every store.
- An attacker can change a product ID in a request and modify another store's product.
- Checkout can reduce stock belonging to another store.

Required fix:

- Add `products.store_id NOT NULL`.
- Add `AND store_id = :store_id` to every tenant product read and write.
- During checkout, lock products with `WHERE id = :id AND store_id = :store_id`.
- Do not give Super Admin routine product CRUD unless explicitly using a selected-store
  support mode that is audited.

#### CRITICAL-02: Transactions do not own a `store_id`

Evidence:

- `transactions` has no `store_id`.
- Most report queries infer store ownership through `transactions.cashier_id -> users.store_id`.
- `transaction_items` and `stock_movements` also have no `store_id`.

Impact:

- Moving a cashier to another store changes the apparent historical ownership of all
  of that cashier's transactions.
- Deleting or corrupting a cashier relationship can break tenant isolation/reporting.
- Queries that forget the user join become global. `Transaction::getDashboardStats()`
  already performs global aggregate queries.

Required fix:

- Persist immutable `store_id` directly on `transactions`, `transaction_items`, and
  `stock_movements`.
- Validate that cashier, products, transaction, and stock movements all have the same
  store before writing.
- Scope reports directly on `transactions.store_id`, not indirectly through users.

#### CRITICAL-03: Checkout trusts unscoped product IDs

Evidence:

- `Transaction::create()` locks and updates products by `id` only.
- Cart product lookup uses `Product::getById()` without store scope.

Impact:

- URL/form manipulation can add and sell any product ID in the database.
- Stock can be reduced across tenants.

Required fix:

```sql
SELECT id, name, price, stock
FROM products
WHERE id = :product_id
  AND store_id = :store_id
  AND status = 'active'
  AND deleted_at IS NULL
FOR UPDATE;
```

Server-side checkout must recalculate product name, price, subtotal, tax, and total
from database rows. Never trust cart names, prices, or subtotals stored in the session.

#### CRITICAL-04: Stock movements and settings are global

Evidence:

- `stock_movements` has no `store_id`, and `StockMovement::getAll()` is unscoped.
- `settings` uses globally unique keys and `Setting::all()` reads all settings.

Impact:

- Admin inventory history exposes all stores.
- Store-specific receipt, tax, address, or logo settings cannot be isolated.

Required fix:

- Add `stock_movements.store_id NOT NULL`.
- Store per-store settings with a composite unique key `(store_id, setting_key)`.
- Keep separate `system_settings` for Super Admin-only platform configuration.

### 1.4 High-risk findings

#### HIGH-01: Tenant filtering is optional and easy to omit

`Transaction` captures `currentStoreId()` and repeatedly uses:

```sql
WHERE (:store_id IS NULL OR store_id = :store_id)
```

This is convenient but unsafe as a default. A missed predicate or a `NULL` context
silently becomes a global query. Global access should require an explicit method or
repository, such as `GlobalTransactionReportRepository`, only callable by Super Admin.

#### HIGH-02: Object-level authorization is missing on product and inventory IDs

Controllers validate role, but do not validate that the requested product belongs to
the actor's store. Role checks and tenant ownership checks solve different problems;
both are required.

All update/delete SQL should include ownership:

```sql
UPDATE products
SET name = :name, price = :price
WHERE id = :id AND store_id = :store_id AND deleted_at IS NULL;
```

Treat `rowCount() !== 1` as not found or forbidden.

#### HIGH-03: No centralized HTTP method enforcement

Routes map only a path to a controller method. The front controller does not enforce
GET versus POST. CSRF protects many writes, but every route should declare its method
and allowed roles centrally.

Recommended route definition:

```php
'POST /admin/products/update' => [
    'handler' => [AdminProductController::class, 'update'],
    'roles' => ['admin'],
    'csrf' => true,
];
```

#### HIGH-04: Session authorization can become stale

Role and `store_id` are copied into the session at login and are not revalidated on
later requests. If a user is disabled, moved, or demoted, the existing session can
continue operating with old privileges.

Required controls:

- Add `users.session_version` and store it in the session.
- On protected requests, verify the user is active, the store is active, and the
  session version still matches.
- Increment `session_version` on password reset, role/store change, or deactivation.
- Enforce idle and absolute session expiry.

#### HIGH-05: Store lifecycle is incomplete

The `tenants` table exists but lacks foreign keys from users and operational tables.
Tenant status is not checked during login. A disabled store's Admin/Kasir can therefore
continue logging in unless each account is separately disabled.

Rename `tenants` to `stores` unless the product truly has an organization above stores.
If both concepts are needed, use `tenants` for customer organizations and `stores` for
their branches.

### 1.5 Medium-risk findings

- Session cookie security flags are not set before `session_start()`. Configure
  `HttpOnly`, `Secure` in HTTPS production, `SameSite=Lax` or `Strict`, strict session
  mode, and a non-default cookie name.
- Login has no rate limiting, lockout, or authentication audit trail.
- Logout is a GET route. Make it POST plus CSRF to prevent forced logout.
- CSV exports may allow spreadsheet formula injection. Prefix cells beginning with
  `=`, `+`, `-`, or `@`.
- Store logo upload validates only filename extension. Validate MIME with `finfo`,
  enforce size/dimensions, generate a random filename, and store outside executable
  paths where possible.
- Product CSV import lacks size limits, strict header validation, row limits, and a
  transaction.
- CSRF tokens are session-wide and are not rotated after login. Rotate on login and
  consider per-form/action tokens for sensitive actions.
- Error handling catches database exceptions and often returns only `false`, losing
  auditability. Log internal errors with a request ID while showing generic messages.
- Transaction invoice generation reads the last code and increments it, which can race
  under concurrent checkout. Use a unique random/ULID-style code or retry on a unique
  constraint violation.
- `ON DELETE CASCADE` from cashier to transactions is inappropriate for financial
  records. Users should be soft-deleted; historical transactions must remain.

### 1.6 SQL injection assessment

No direct exploitable SQL injection was found in the reviewed core model paths.
User-provided values are generally passed through prepared statements. Dynamic
`IN (...)` placeholders in `User::countByRoles()` are generated from placeholder
characters and are safe.

Rules to retain:

- Never concatenate request values into SQL identifiers, `ORDER BY`, `LIMIT`, or
  predicates.
- For dynamic sort/filter fields, map input to a fixed allowlist.
- Bind all values and use `PDO::ATTR_EMULATE_PREPARES => false`.
- Validate dates, enum values, numeric ranges, and pagination before binding.

### 1.7 Structural anti-patterns

- Tenant isolation is mixed into individual queries instead of enforced by a shared
  data-access boundary.
- Models read global session state directly, which makes authorization implicit and
  difficult to test.
- Super Admin and Admin share controllers/routes for operational tasks. This creates
  accidental overlap and global-operation risk.
- `tenants` currently represents stores, while requirements use the term store. Use one
  domain term consistently.
- Reports are queries, not operational tables. Do not create a generic `reports` table;
  use scoped queries, SQL views, or scheduled aggregate tables.
- Expenses and cashier shifts requested by the target architecture do not exist.
- Route authorization is distributed across controller constructors rather than
  declared centrally.

### 1.8 Remediation order

1. Add and backfill `store_id` on all tenant-owned tables.
2. Rewrite product, checkout, stock, setting, and transaction queries to require scope.
3. Add foreign keys and composite indexes.
4. Introduce central request context, route method/role guards, and scoped repositories.
5. Revalidate active user/store/session version on protected requests.
6. Add audit logs, expenses, and cashier shifts.
7. Add automated cross-tenant IDOR tests before production.

## 2. Super Admin Responsibilities

Super Admin owns the platform, not daily store operations. Super Admin should not
normally create products, adjust stock, enter expenses, or perform checkout.

### 2.1 Store lifecycle

- Create, activate, suspend, archive, and restore stores.
- Assign or replace the primary Store Admin.
- View store metadata, plan/status, timezone, currency, and operational health.
- Initiate controlled data export or store closure.
- Prevent store deletion while financial retention requirements apply.

### 2.2 User lifecycle

- Create and manage Super Admin and Store Admin accounts.
- Globally search users and view account/store assignment.
- Suspend accounts, force logout, reset credentials, and require password reset.
- Move a cashier only through an audited workflow.
- View active sessions and revoke compromised sessions.
- Never manage cashier schedules or routine permissions that belong to Store Admin.

### 2.3 Global reporting and analytics

- Cross-store revenue, transaction count, average order value, and trends.
- Store comparison and ranking.
- Global payment-method distribution.
- Global inventory risk summary without routine stock adjustment capability.
- Suspicious activity, void/refund, and discrepancy reports.
- Export global reports with audit logging and least-privilege controls.

### 2.4 System configuration

- Platform name, default locale/timezone, password/session policy, retention policy.
- Feature flags and maintenance mode.
- Global tax/payment defaults that stores may override where allowed.
- Email/SMS/payment integration credentials stored securely outside source control.
- Backup schedule, restore policy, and data-retention configuration.

### 2.5 Audit and monitoring

- Search immutable audit logs across stores.
- Review authentication failures, privilege changes, exports, and sensitive actions.
- Monitor failed jobs, database health, storage, error rates, and backup status.
- Receive alerts for repeated login failures, cross-store access attempts, and unusual
  refunds/stock changes.

### 2.6 Platform concerns

- Data privacy requests and retention/legal holds.
- Disaster recovery and tested backups.
- Security incident response and session revocation.
- Schema migration/version management.
- Platform-wide integrations and API credentials.

## 3. Multi-Tenancy Best Practice for PHP Native and MySQL

### 3.1 Non-negotiable isolation rules

1. Every tenant-owned row has `store_id NOT NULL`.
2. `store_id` comes from trusted server-side request context, never from form input for
   Admin/Kasir.
3. Every tenant read, update, and delete includes `store_id` in SQL.
4. Every tenant insert writes `store_id` from request context.
5. Cross-store access uses separate explicit Super Admin services.
6. Foreign keys and composite indexes support the same ownership boundary.
7. IDs from URLs/forms are untrusted and must be looked up within the current store.

### 3.2 Request context instead of direct session access in models

```php
final class ActorContext
{
    public function __construct(
        public readonly int $userId,
        public readonly string $role,
        public readonly ?int $storeId,
    ) {}

    public function requireStoreId(): int
    {
        if ($this->role === 'super_admin' || !$this->storeId) {
            throw new LogicException('A store context is required.');
        }
        return $this->storeId;
    }
}
```

Build this context once after session validation and inject it into controllers/services.
Models/repositories should not read `$_SESSION` directly.

### 3.3 Scoped repository pattern

```php
final class StoreProductRepository
{
    public function __construct(
        private PDO $pdo,
        private int $storeId,
    ) {}

    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM products
             WHERE id = :id AND store_id = :store_id AND deleted_at IS NULL'
        );
        $stmt->execute(['id' => $id, 'store_id' => $this->storeId]);
        return $stmt->fetch();
    }

    public function updatePrice(int $id, float $price): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET price = :price
             WHERE id = :id AND store_id = :store_id AND deleted_at IS NULL'
        );
        $stmt->execute([
            'price' => $price,
            'id' => $id,
            'store_id' => $this->storeId,
        ]);
        return $stmt->rowCount() === 1;
    }
}
```

Do not make `$storeId` optional in a store repository. Create separate global report
repositories for Super Admin.

### 3.4 Middleware-like front controller

Use route metadata to enforce HTTP method, authentication, role, CSRF, and store
context before constructing the controller.

```php
$routes['POST /admin/products/update'] = [
    'handler' => [AdminProductController::class, 'update'],
    'roles' => ['admin'],
    'csrf' => true,
    'store_required' => true,
];
```

Request pipeline:

```text
Resolve route
  -> enforce HTTP method
  -> authenticate and reload active user/store
  -> verify session version and expiry
  -> authorize role/permission
  -> verify CSRF for state changes
  -> build ActorContext
  -> call controller/service
```

### 3.5 Preventing URL/API manipulation

- Never use `SELECT ... WHERE id = ?` for tenant objects.
- Use `WHERE id = ? AND store_id = ?` in the same SQL query.
- Do not accept `store_id` from Admin/Kasir requests.
- Recalculate checkout totals from scoped database products.
- Validate parent-child ownership, for example transaction item to transaction store.
- Return a generic 404 for inaccessible tenant objects to avoid leaking their existence.
- Add tests where Store A users attempt every Store B object ID.

### 3.6 Recommended role-separated folder structure

See section 4.5. The important separation is:

- Super Admin controllers/services call explicit global repositories.
- Admin and Cashier controllers/services receive a mandatory store-scoped context.
- Shared business services contain domain logic, not authorization shortcuts.

## 4. Documentation

### 4.1 Recommended database schema

The following is the target schema. Migration scripts must backfill and validate data
before changing nullable columns to `NOT NULL`.

```sql
CREATE TABLE stores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    status ENUM('active', 'suspended', 'archived') NOT NULL DEFAULT 'active',
    timezone VARCHAR(64) NOT NULL DEFAULT 'Asia/Jakarta',
    currency_code CHAR(3) NOT NULL DEFAULT 'IDR',
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    archived_at DATETIME NULL,
    INDEX stores_status_idx (status)
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'kasir') NOT NULL,
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    session_version INT UNSIGNED NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT users_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT users_created_by_fk FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY users_store_id_id_uk (store_id, id),
    INDEX users_store_role_status_idx (store_id, role, status),
    CHECK (
        (role = 'super_admin' AND store_id IS NULL)
        OR (role IN ('admin', 'kasir') AND store_id IS NOT NULL)
    )
) ENGINE=InnoDB;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NULL,
    price DECIMAL(14,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    description TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT products_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT products_created_by_fk FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY products_store_id_id_uk (store_id, id),
    INDEX products_store_status_idx (store_id, status, deleted_at),
    INDEX products_store_name_idx (store_id, name)
) ENGINE=InnoDB;

CREATE TABLE cashier_shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    cashier_id BIGINT UNSIGNED NOT NULL,
    opened_by BIGINT UNSIGNED NOT NULL,
    closed_by BIGINT UNSIGNED NULL,
    opening_cash DECIMAL(14,2) NOT NULL DEFAULT 0,
    expected_cash DECIMAL(14,2) NULL,
    closing_cash DECIMAL(14,2) NULL,
    discrepancy DECIMAL(14,2) NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    CONSTRAINT shifts_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT shifts_cashier_fk FOREIGN KEY (cashier_id) REFERENCES users(id),
    CONSTRAINT shifts_opened_by_fk FOREIGN KEY (opened_by) REFERENCES users(id),
    CONSTRAINT shifts_closed_by_fk FOREIGN KEY (closed_by) REFERENCES users(id),
    UNIQUE KEY shifts_store_id_id_uk (store_id, id),
    INDEX shifts_store_cashier_status_idx (store_id, cashier_id, status)
) ENGINE=InnoDB;

CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NULL,
    transaction_code VARCHAR(64) NOT NULL,
    cashier_id BIGINT UNSIGNED NOT NULL,
    status ENUM('completed', 'voided', 'refunded') NOT NULL DEFAULT 'completed',
    subtotal DECIMAL(14,2) NOT NULL,
    tax_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(14,2) NOT NULL,
    paid_amount DECIMAL(14,2) NOT NULL,
    change_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    payment_method ENUM('cash', 'qris', 'card') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    voided_at DATETIME NULL,
    voided_by BIGINT UNSIGNED NULL,
    CONSTRAINT transactions_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT transactions_voided_by_fk FOREIGN KEY (voided_by) REFERENCES users(id),
    UNIQUE KEY transactions_store_id_id_uk (store_id, id),
    UNIQUE KEY transactions_store_code_uk (store_id, transaction_code),
    CONSTRAINT transactions_store_cashier_fk
        FOREIGN KEY (store_id, cashier_id) REFERENCES users(store_id, id),
    CONSTRAINT transactions_store_shift_fk
        FOREIGN KEY (store_id, shift_id) REFERENCES cashier_shifts(store_id, id),
    INDEX transactions_store_created_idx (store_id, created_at),
    INDEX transactions_store_cashier_idx (store_id, cashier_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE transaction_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    product_name_snapshot VARCHAR(255) NOT NULL,
    unit_price DECIMAL(14,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT transaction_items_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT transaction_items_store_transaction_fk
        FOREIGN KEY (store_id, transaction_id) REFERENCES transactions(store_id, id),
    CONSTRAINT transaction_items_store_product_fk
        FOREIGN KEY (store_id, product_id) REFERENCES products(store_id, id),
    INDEX transaction_items_store_transaction_idx (store_id, transaction_id),
    INDEX transaction_items_store_product_idx (store_id, product_id)
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    movement_type ENUM('purchase', 'sale', 'adjustment', 'return', 'void') NOT NULL,
    quantity_delta INT NOT NULL,
    stock_before INT NOT NULL,
    stock_after INT NOT NULL,
    note VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT stock_movements_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT stock_movements_store_product_fk
        FOREIGN KEY (store_id, product_id) REFERENCES products(store_id, id),
    CONSTRAINT stock_movements_store_user_fk
        FOREIGN KEY (store_id, user_id) REFERENCES users(store_id, id),
    CONSTRAINT stock_movements_store_transaction_fk
        FOREIGN KEY (store_id, transaction_id) REFERENCES transactions(store_id, id),
    INDEX stock_movements_store_product_idx (store_id, product_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    expense_date DATE NOT NULL,
    receipt_path VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT expenses_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT expenses_created_by_fk FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT expenses_approved_by_fk FOREIGN KEY (approved_by) REFERENCES users(id),
    INDEX expenses_store_date_idx (store_id, expense_date, deleted_at)
) ENGINE=InnoDB;

CREATE TABLE store_settings (
    store_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (store_id, setting_key),
    CONSTRAINT store_settings_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT store_settings_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT system_settings_updated_by_fk FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NULL,
    actor_user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id VARCHAR(100) NULL,
    request_id CHAR(36) NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    before_data JSON NULL,
    after_data JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT audit_logs_store_fk FOREIGN KEY (store_id) REFERENCES stores(id),
    CONSTRAINT audit_logs_actor_fk FOREIGN KEY (actor_user_id) REFERENCES users(id),
    INDEX audit_logs_store_created_idx (store_id, created_at),
    INDEX audit_logs_actor_created_idx (actor_user_id, created_at),
    INDEX audit_logs_action_created_idx (action, created_at)
) ENGINE=InnoDB;

CREATE TABLE login_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    succeeded BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX login_attempts_email_created_idx (email, created_at),
    INDEX login_attempts_ip_created_idx (ip_address, created_at)
) ENGINE=InnoDB;
```

Notes:

- Do not use hard deletes for users, products, financial transactions, stock history,
  expenses, or audit logs.
- Reports should query these scoped tables. Add aggregate tables only when measured
  performance requires them.
- MySQL `CHECK` enforcement depends on version. Enforce the Super Admin/store rule in
  application validation as well.

### 4.2 Role and permission matrix

Legend: `C` create, `R` read, `U` update, `D` archive/soft-delete, `A` approve/action.

| Feature | Super Admin | Admin | Kasir |
|---|---|---|---|
| Stores | CRUD | R own | R own basic identity |
| Super Admin accounts | CRUD | None | None |
| Admin accounts | CRUD | None | None |
| Cashier accounts | R/global support, suspend | CRUD own store | R self |
| Products | R/global analytics/support only | CRUD own store | R active own store |
| Stock adjustments | R/global audit only | C/R own store | None |
| Stock movement history | R all | R own store | R own actions if needed |
| Expenses | R all | CRUD/approve own store | C/R own submitted if policy allows |
| Transactions | R all, void only by exceptional audited support | R own store, controlled void/refund | C and R own shift |
| Cashier shifts | R all | CRUD/A own store | C open/close own shift |
| Store reports | R all stores | R/export own store | R own shift summary |
| Global reports | R/export | None | None |
| Store settings | R/global support | R/U own store | R receipt-related values |
| System settings | CRUD | None | None |
| Audit logs | R all | R own store limited | None |
| Backups/integrations/feature flags | CRUD | None | None |

Super Admin support writes to tenant data should be exceptional, require an explicit
selected-store support mode, and always create an audit log.

### 4.3 Session flow diagram

```text
[POST /login + CSRF]
          |
          v
[Rate limit by email + IP]
          |
          v
[Load active user by email]
          |
          +-- invalid password/status --> [record failure] --> [generic error]
          |
          v
[If Admin/Kasir: load active store]
          |
          +-- missing/suspended store --> [deny login]
          |
          v
[session_regenerate_id(true) + rotate CSRF]
          |
          v
[Store user_id, role, store_id, session_version, issued_at, last_seen_at]
          |
          v
[Redirect by role]
  super_admin -> /superadmin/dashboard
  admin       -> /admin/dashboard
  kasir       -> /kasir/transaction

Every protected request:

[Resolve route + enforce HTTP method]
          |
          v
[Reload/validate active user, store, session_version, expiry]
          |
          v
[Check route role/permission]
          |
          v
[Verify CSRF for POST/PUT/PATCH/DELETE]
          |
          v
[Build ActorContext]
          |
          +-- Admin/Kasir --> require store_id --> scoped repository/service
          |
          +-- Super Admin --> explicit global repository/service
          |
          v
[Execute prepared query with ownership predicate]
```

### 4.4 Security checklist per file

#### `public/index.php`

- [ ] Route declares and enforces HTTP method.
- [ ] Route declares authentication, roles/permissions, CSRF, and store requirement.
- [ ] Protected requests revalidate active user/store and session version.
- [ ] Security headers are set: CSP, frame protection, nosniff, referrer policy.
- [ ] Exceptions are logged; production responses do not expose internals.

#### `config/config.php`

- [ ] Configure session cookie before `session_start()`.
- [ ] Enable `session.use_strict_mode=1`.
- [ ] Set `HttpOnly`, `Secure` under HTTPS, and `SameSite=Lax` or `Strict`.
- [ ] Set idle and absolute session timeouts.
- [ ] Keep secrets outside repository and disable detailed production errors.

#### `config/database.php`

- [ ] Use a least-privilege database account, not root.
- [ ] Keep native prepared statements enabled.
- [ ] Use `utf8mb4`.
- [ ] Log connection errors without exposing credentials.

#### `routes.php`

- [ ] Every state-changing route is POST/PUT/PATCH/DELETE, never GET.
- [ ] Every route declares allowed roles.
- [ ] Super Admin routes use `/superadmin/*`; Admin routes use `/admin/*`; Cashier routes
  use `/kasir/*`.
- [ ] Logout is POST plus CSRF.

#### Controllers

- [ ] Do not trust request `store_id`, user ID, price, subtotal, total, or role.
- [ ] Validate required fields, type, range, enum, date, file size, and MIME.
- [ ] Call a service/repository that enforces ownership in SQL.
- [ ] Verify CSRF on state-changing actions.
- [ ] Use Post/Redirect/Get after writes.
- [ ] Log sensitive actions and failures.
- [ ] Return generic not-found/forbidden responses for inaccessible tenant objects.

#### Models/repositories

- [ ] Tenant repositories require a non-null store ID in the constructor.
- [ ] Every tenant SELECT/UPDATE/DELETE includes `store_id`.
- [ ] Every tenant INSERT writes trusted `store_id`.
- [ ] All values use prepared statements.
- [ ] Dynamic sort/filter fields use allowlists.
- [ ] Sensitive multi-row writes use a database transaction.
- [ ] Stock/financial updates use row locking where needed.
- [ ] Update/delete checks `rowCount() === 1`.

#### Views

- [ ] Escape all untrusted output with `e()`.
- [ ] Every state-changing form contains CSRF.
- [ ] Do not use hidden inputs as authorization controls.
- [ ] Do not expose cross-store IDs/options to Admin/Kasir.

#### Upload/import/export files

- [ ] Validate file size, MIME, extension, and content.
- [ ] Generate random filenames and prevent script execution.
- [ ] Validate CSV headers and row limits; process in a transaction.
- [ ] Neutralize spreadsheet formulas in CSV exports.
- [ ] Audit sensitive exports.

#### Database migrations

- [ ] Add/backfill `store_id` before setting `NOT NULL`.
- [ ] Reject orphaned/cross-store relationships before adding foreign keys.
- [ ] Add composite indexes beginning with `store_id`.
- [ ] Never cascade-delete financial or audit history.
- [ ] Migration is tested against a production-like backup.

#### Tests

- [ ] Store A Admin cannot list/read/update/delete Store B products.
- [ ] Store A Cashier cannot add or sell Store B products.
- [ ] Store A cannot read Store B transaction, receipt, stock, expense, or setting IDs.
- [ ] Disabled user/store and stale session version are rejected.
- [ ] Missing/wrong CSRF and wrong HTTP methods are rejected.
- [ ] Concurrent checkout cannot oversell stock or duplicate invoice codes.

### 4.5 Recommended folder structure

```text
app/
  Auth/
    AuthService.php
    SessionGuard.php
  Context/
    ActorContext.php
    RequestContextFactory.php
  Controllers/
    Auth/
      LoginController.php
      LogoutController.php
    SuperAdmin/
      DashboardController.php
      StoreController.php
      AdminUserController.php
      GlobalReportController.php
      AuditLogController.php
      SystemSettingController.php
    Admin/
      DashboardController.php
      CashierController.php
      ProductController.php
      InventoryController.php
      ExpenseController.php
      StoreReportController.php
      StoreSettingController.php
    Kasir/
      TransactionController.php
      ShiftController.php
      ReceiptController.php
  Services/
    CheckoutService.php
    InventoryService.php
    ExpenseService.php
    ShiftService.php
    AuditService.php
  Repositories/
    StoreScoped/
      ProductRepository.php
      TransactionRepository.php
      StockMovementRepository.php
      ExpenseRepository.php
      StoreSettingRepository.php
    Global/
      StoreRepository.php
      GlobalReportRepository.php
      AuditLogRepository.php
    UserRepository.php
  Views/
    auth/
    superadmin/
    admin/
    kasir/
  Support/
    Csrf.php
    Validator.php
    Response.php
    Upload.php
config/
  app.php
  database.php
  session.php
database/
  migrations/
  seeds/
docs/
public/
  index.php
routes/
  auth.php
  superadmin.php
  admin.php
  kasir.php
storage/
  logs/
  private_uploads/
tests/
  Unit/
  Integration/
  Security/
```

### 4.6 Definition of done for tenant isolation

Tenant isolation is complete only when:

- Every tenant-owned table has a populated, non-null `store_id`.
- Every tenant repository requires a store context.
- Super Admin global reads use separate explicit code paths.
- Cross-store ID manipulation tests pass for every CRUD and checkout endpoint.
- Historical transaction ownership no longer depends on the cashier's current store.
- Audit logs record privileged and sensitive actions.
