# QA Test Suite - PHP Native POS

This folder contains QA documentation and semi-automated tests for the native PHP POS application.

## What Is Covered

- Login/logout flow
- Session-protected pages
- Admin and cashier role access
- Product validation and stock rules
- Cart and checkout calculations
- Payment and change rules
- Transaction persistence expectations
- Basic security checks such as CSRF, hashed passwords, and direct URL access

## Test Files

- `http-smoke-tests.ps1` - Semi-automated HTTP tests using PowerShell web requests.
- `pos-business-rules-test.php` - Lightweight PHP checks for POS calculation rules.
- `tenant-isolation-test.php` - Store ownership and cross-tenant checks.
- `role-access-rules-test.php` - Route and role authorization checks.

## Requirements

- XAMPP Apache and MySQL running, or PHP built-in server running.
- Database imported from `database/pos_db.sql`.
- Run `php database/migrate.php` and `php database/verify.php`.

## Run PHP Business Rule Tests

```powershell
C:\xampp\php\php.exe tests\pos-business-rules-test.php
```

## Run HTTP Smoke Tests

Set credentials through environment variables so passwords are not stored in the repository:

```powershell
$env:QA_ADMIN_EMAIL="admin@pos.com"
$env:QA_ADMIN_PASSWORD="admin123"
$env:QA_CASHIER_EMAIL="kasir@pos.com"
$env:QA_CASHIER_PASSWORD="admin123"
```

Then run:

```powershell
powershell -ExecutionPolicy Bypass -File tests\http-smoke-tests.ps1 -BaseUrl "http://localhost/pos/public"
```

If your app URL is different, change `-BaseUrl`.

## Notes

- These tests are intentionally lightweight because this is a PHP Native project without an existing test framework.
- The HTTP smoke tests validate major redirects and access-control behavior, but they do not replace manual QA for visual layout and full cashier workflows.
- For full end-to-end coverage, add Playwright later.
