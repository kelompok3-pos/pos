# Database Setup

## Canonical Schema

The supported application schema is `database/pos_db.sql`. It uses:

- `tenants` as the store/tenant table
- `store_id` as the tenant foreign key
- `products.price` and `products.minimum_stock`
- `transactions.total_price` and the `transactions.payment_method` enum
- the legacy-named `cashier_shifts` table

Active PHP code must target this schema directly. Runtime detection between
different schemas is not supported because it hides incomplete migrations and
can make the same code behave differently between environments.

## Fresh Local Install

1. Create/import one database using `database/pos_db.sql`.
2. Run `php database/migrate.php`. The base SQL records bundled migrations, so
   they will normally be reported as `SKIPPED`.
3. Run `php database/verify.php`.
4. Optionally run `database/seed_demo_data.sql` for local development only.

For phpMyAdmin, select the target database, open **Import**, and import
`database/pos_db.sql`. This is the only SQL file required for a fresh install.

## Existing Install

Back up the database first. Run the upgrades in this exact order:

1. `database/upgrade_20260602_payment_change.sql`
2. `database/upgrade_20260603_super_admin_role.sql`
3. `database/upgrade_20260610_store_scope.sql`
4. `database/upgrade_20260610_dashboard_modules.sql`
5. `database/upgrade_20260610_saas_tenants.sql`

Then run:

```bash
php database/migrate.php
php database/verify.php
```

If all current migrations were already applied manually before migration
tracking existed, verify the schema first and then run
`php database/migrate.php --baseline` once. Never baseline a database that has
not already received every pending migration.

Use `php database/migrate.php --status` to list applied and pending files.

## Index Policy

Canonical indexes are checked by useful left-most prefixes rather than by index
name. Existing wider indexes satisfy the required prefixes:

- `products(store_id, status, deleted_at)`
- `transactions(store_id, created_at)`
- `expenses(store_id, created_at)` and `expenses(store_id, expense_date, deleted_at)`
- `stock_movements(store_id, product_id, created_at)`
- `cashier_shifts(store_id, kasir_id, status)` (`kasir_id` is the canonical cashier user column)

## Foreign Key Policy

Tenant-owned relationships use `ON DELETE RESTRICT`. Composite foreign keys,
such as `(store_id, product_id)`, prevent cross-store references. Transaction
records are intentionally immutable and restricted from parent deletion.

Schema prototypes that do not match the canonical application database are not
kept in the active repository.

## Local Demo Data

For local development only:

```bash
mysql -u root pos_db < database/seed_demo_data.sql
```

The seed is idempotent. All demo accounts use password `Demo123!`.

- Super admin: `superadmin@demo.pos`
- Admin: `admin.jakarta@demo.pos`, `admin.bandung@demo.pos`, `admin.surabaya@demo.pos`
- Cashier: `kasir1.jakarta@demo.pos`, `kasir2.jakarta@demo.pos`,
  `kasir.bandung@demo.pos`, `kasir.surabaya@demo.pos`
