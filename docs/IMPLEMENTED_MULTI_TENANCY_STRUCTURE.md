# Implemented Multi-Tenancy Structure

```text
bootstrap/
  autoload.php
  middleware.php
database/
  migrations/
    001_add_store_scope.sql
    002_create_operational_security_tables.sql
    003_scope_settings.sql
    004_add_cross_store_constraints.sql
src/
  Core/
    ActorContext.php
    AuditLogger.php
    ScopedRepository.php
    UnauthorizedException.php
  Repositories/
    AuditLogRepository.php
    ExpenseRepository.php
    ProductRepository.php
    ShiftRepository.php
    StockRepository.php
    TransactionRepository.php
app/
  Controllers/
    Admin/
      AdminExpenseController.php
      AdminProductController.php
      AdminModuleController.php
    Kasir/
      KasirShiftController.php
      KasirTransactionController.php
examples/
  admin/products/store.php
```

The active front controller loads the autoloader and middleware. Route metadata now
enforces HTTP methods and roles. Existing models remain compatibility facades while
tenant-sensitive product and stock operations use scoped repositories.

Migration order:

```text
001_add_store_scope.sql
002_create_operational_security_tables.sql
003_scope_settings.sql
004_add_cross_store_constraints.sql
```

Existing products are assigned to store `1` because historical ownership cannot be
derived from the old schema. Reconcile those products manually if they belong to
another store.
