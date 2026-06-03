# QA Testing Report

## Project Overview

This project is a PHP Native POS / cashier system without Laravel, CodeIgniter, Symfony, React, or another application framework.

Detected structure:

| Area | Location | Notes |
|---|---|---|
| Front controller | `public/index.php` | Loads config, database, helpers, routes, then dispatches controllers. |
| Routes | `routes.php` | Maps URL paths to controller classes and methods. |
| Config | `config/config.php` | Defines app constants and starts PHP session. |
| Database connection | `config/database.php` | Creates PDO connection from environment settings. |
| Helpers | `helpers/functions.php` | URL helpers, flash messages, old input, CSRF, authentication, role checks, escaping, formatting. |
| Auth | `app/Controllers/AuthController.php`, `app/Views/auth/login.php`, `app/Models/User.php` | Login/logout, password verification, session user data. |
| Admin product management | `app/Controllers/Admin/AdminProductController.php`, `app/Models/Product.php`, `app/Views/admin/product/*` | Product CRUD and stock validation. |
| Admin user management | `app/Controllers/Admin/AdminUserController.php`, `app/Views/admin/user/*`, `app/Models/User.php` | User creation, role handling, soft delete/deactivation. |
| Cashier product/transaction pages | `app/Controllers/Kasir/*`, `app/Views/kasir/*` | Product lookup, cart, checkout, receipt. |
| Dashboard/reporting | `app/Controllers/HomeController.php`, `app/Views/home/index.php`, `app/Models/Transaction.php` | KPI cards, chart/report data, transaction table. |
| SQL schema | `database/pos_db.sql`, `database/upgrade_*.sql` | Main schema and upgrade scripts. |

The application uses PHP sessions via `session_start()` in `config/config.php`, role gates via `requireAuth()` and `requireRole()` in `helpers/functions.php`, and PDO prepared statements in key models/controllers.

## Testing Tools Used

| Tool | Type | Purpose |
|---|---|---|
| PHP CLI | Automated | Lightweight business-rule checks for totals, payment/change, stock quantity rules. |
| PowerShell `Invoke-WebRequest` | Semi-automated | Login/session/role/request smoke tests against a running local app. |
| Manual QA checklist | Manual/Semi-automated | Workflows that require UI interaction or database verification. |

No PHPUnit or Playwright dependency was added, because the project does not currently include Composer/package setup for testing. The created tests stay inside `tests/`.

## Test Cases

| ID | Feature | Scenario | Test Steps | Expected Result | Priority | Type |
|---|---|---|---|---|---|---|
| TC-AUTH-001 | Login | Valid admin login | Open login, submit valid admin credentials | Redirect to dashboard, admin session exists | High | Semi-automated |
| TC-AUTH-002 | Login | Invalid login | Submit wrong email/password | User stays unauthenticated, error shown | High | Semi-automated |
| TC-AUTH-003 | Logout | Logout clears session | Login, click logout, access dashboard | User redirected to login | High | Manual |
| TC-AUTH-004 | Session | Anonymous protected access | Open `/dashboard` without session | Redirect to `/login` | High | Semi-automated |
| TC-ROLE-001 | Role Access | Cashier accesses admin page | Login cashier, open `/admin/product` | 403 or no admin content | High | Semi-automated |
| TC-ROLE-002 | Role Access | Admin accesses product management | Login admin, open `/admin/product` | Product management visible | High | Semi-automated |
| TC-ROLE-003 | Role Access | Admin creates admin account | Login normal admin, submit role `admin` | Request rejected unless super admin | High | Manual |
| TC-PROD-001 | Product CRUD | Add valid product | Submit valid name, price, stock | Product appears in product list | High | Manual |
| TC-PROD-002 | Product Validation | Empty product name | Submit empty name | Validation error, product not saved | High | Manual |
| TC-PROD-003 | Product Validation | Negative price | Submit price `< 0` | Validation error | High | Manual |
| TC-PROD-004 | Stock Validation | Negative stock | Submit stock `< 0` | Validation error | High | Manual |
| TC-PROD-005 | Product CRUD | Edit product | Change price/stock | Product updates correctly | Medium | Manual |
| TC-PROD-006 | Product CRUD | Delete product | Delete product | Product soft-deleted/hidden | Medium | Manual |
| TC-CART-001 | Cart | Add product to cart | Cashier adds product with qty 1 | Product appears in cart | High | Manual |
| TC-CART-002 | Cart | Add more than available stock | Enter qty above stock | Request rejected | High | Manual |
| TC-CART-003 | Cart | Update quantity | Update cart item quantity | Subtotal recalculates | High | Manual |
| TC-CART-004 | Cart | Remove item | Remove cart item | Item removed, total updates | Medium | Manual |
| TC-CHECKOUT-001 | Checkout | Empty cart checkout | Submit checkout with empty cart | Checkout rejected | High | Semi-automated |
| TC-CHECKOUT-002 | Checkout | Payment less than total | Add item, pay below total | Checkout rejected | High | Manual |
| TC-CHECKOUT-003 | Checkout | Valid payment | Add item, pay enough | Transaction saved, receipt available | High | Manual |
| TC-CHECKOUT-004 | Calculation | Total calculation | Cart has multiple items | Total equals sum of subtotals | High | Automated |
| TC-CHECKOUT-005 | Calculation | Change calculation | Paid > total | Change equals paid minus total | High | Automated |
| TC-CHECKOUT-006 | Stock | Stock decreases after checkout | Complete checkout | Product stock decreases by sold qty | High | Manual |
| TC-TRX-001 | Transaction | Transaction saved | Complete checkout | Transaction row and items row exist | High | Manual |
| TC-TRX-002 | Transaction History | Today transaction visible | Complete checkout, open dashboard | Transaction appears in today's table | Medium | Manual |
| TC-REPORT-001 | Dashboard Charts | Admin chart render | Login admin, open dashboard | Charts render with real transaction data | Medium | Manual |
| TC-SEC-001 | CSRF | Missing CSRF on POST | Submit protected POST without token | Request rejected with 403 | High | Manual |
| TC-SEC-002 | SQL Injection | Login/product inputs | Submit SQL-like strings | No SQL error or bypass | High | Manual |
| TC-SEC-003 | Password Storage | User passwords | Inspect DB password column | Passwords are hashed, not plain text | High | Manual |

## Automated Tests Created

| File | Purpose |
|---|---|
| `tests/pos-business-rules-test.php` | Automated CLI checks for cart total, payment/change, negative stock, and stock quantity rules. |
| `tests/http-smoke-tests.ps1` | Semi-automated HTTP smoke tests for login, protected access, role access, and empty checkout handling. |
| `tests/README.md` | Setup and execution instructions. |
| `tests/QA_TESTING_REPORT.md` | QA strategy, checklist, bugs, risks, and recommendations. |

## Bugs Found

### Bug 1: Legacy/duplicate controller files can confuse maintenance

- File/location: `app/Controllers/Admin/User.php`, `app/Controllers/Admin/AdminProductController.php` comments/legacy naming, older view path `app/Views/admin/product/user/index.php`
- Steps to reproduce: Inspect controller/view structure.
- Expected result: One clear controller/view path per feature.
- Actual result: Some legacy or duplicate naming exists beside active controllers.
- Severity: Low
- Suggested fix: Keep legacy redirect files only if needed, otherwise document or remove in a future cleanup PR.

### Bug 2: User edit view exposes admin role option without super admin UI context

- File/location: `app/Views/admin/user/edit.php`
- Steps to reproduce: Inspect user edit form.
- Expected result: Admin role changes should follow the same super admin restrictions as create/delete.
- Actual result: The view still presents `admin` and `kasir` role options directly.
- Severity: Medium
- Suggested fix: Align user edit controller/view with `super_admin` restrictions, or hide/admin-lock role controls for non-super admins.

### Bug 3: Duplicate checkout on browser refresh should be tested manually

- File/location: `app/Controllers/Kasir/KasirTransactionController.php`
- Steps to reproduce: Complete checkout, then refresh/return to checkout POST flow depending on browser behavior.
- Expected result: Duplicate transaction is not created.
- Actual result: Needs manual verification. Current redirect-after-POST reduces risk, but should be tested.
- Severity: Medium
- Suggested fix: Add idempotency token/order token for checkout if duplicates are observed.

## Security Risks

| Risk | Status | Notes |
|---|---|---|
| Missing `session_start()` | Not found | Session is started in `config/config.php`. |
| Direct URL access without login | Controlled | `requireAuth()`/`requireRole()` protect main app routes. |
| Role bypass by URL | Mostly controlled | Admin and cashier controllers call `requireRole()`. Continue testing after route additions. |
| SQL injection | Reduced | Important model operations use prepared statements. Some aggregate read queries use static SQL and are low risk. |
| Plain-text passwords | Not found | `password_hash()` and `password_verify()` are used. |
| Missing CSRF | Mostly controlled | Important POST forms/controllers use `csrf_field()` and `verifyCsrf()`. Verify every future POST route. |
| Negative stock | Controlled in product controller/model | Create/edit forms and controller reject stock `< 0`; model has stock decrement guard. |
| Weak session hardening | Improved | Login/logout regenerate session ID. Consider secure cookie flags for production. |
| Secrets exposure | Avoided | `.env` was not printed; tests use environment variables for credentials. |

## How to Run Tests

### 1. Start the app

Use XAMPP Apache/MySQL or PHP built-in server. Example URL:

```text
http://localhost/pos/public
```

### 2. Prepare database

For a fresh database, import:

```sql
SOURCE database/pos_db.sql;
```

For an existing database, run upgrades:

```sql
SOURCE database/upgrade_20260602_payment_change.sql;
SOURCE database/upgrade_20260603_super_admin_role.sql;
```

### 3. Run PHP business-rule tests

```powershell
C:\xampp\php\php.exe tests\pos-business-rules-test.php
```

Expected output:

```text
PASS: POS business-rule tests passed.
```

### 4. Run HTTP smoke tests

Set credentials through environment variables:

```powershell
$env:QA_ADMIN_EMAIL="admin@pos.com"
$env:QA_ADMIN_PASSWORD="<admin-password>"
$env:QA_CASHIER_EMAIL="kasir@pos.com"
$env:QA_CASHIER_PASSWORD="<cashier-password>"
```

Run:

```powershell
powershell -ExecutionPolicy Bypass -File tests\http-smoke-tests.ps1 -BaseUrl "http://localhost/pos/public"
```

## Recommendations

1. Add Composer and PHPUnit for formal unit testing.
2. Move reusable business logic into pure helper/service functions:
   - cart total calculation
   - payment validation
   - change calculation
   - stock validation
3. Add Playwright for end-to-end tests:
   - login
   - product create/edit/delete
   - cashier cart
   - checkout and receipt
   - role access
4. Add a dedicated test database to avoid modifying development data.
5. Add checkout idempotency token to prevent duplicate transaction risk.
6. Align user edit role permissions with `super_admin` rules.
7. Add production cookie flags:
   - `session.cookie_httponly=1`
   - `session.cookie_secure=1` when HTTPS is active
   - `session.cookie_samesite=Lax` or `Strict`
8. Add CI later to run PHP syntax checks and test scripts before merge.
