# Current Architecture

The application is a native PHP multi-tenant POS using this dependency
direction:

```text
public/index.php
  -> bootstrap/router.php + routes/*.php
  -> bootstrap/middleware.php
  -> app/Controllers
  -> src/Services
  -> src/Repositories
  -> src/Core + PDO
  -> app/Views
```

- `ActorContext` revalidates authenticated users and tenant assignments.
- `ScopedRepository` applies `store_id` isolation.
- Services own transactional business workflows.
- Controllers validate HTTP input and delegate business work.
- Views only render prepared data.

## Canonical Database

`database/pos_db.sql` is the single-file fresh-install schema. Schema changes
must be added as ordered files in `database/migrations/`.

```bash
php database/migrate.php
php database/verify.php
```

Runtime schema detection and incompatible prototype schemas are not supported.

## Compatibility Layer

The remaining classes under `app/Models/` are legacy-facing facades still used
by controllers. New persistence code should use repositories and services.

Procedural functions in `helpers/functions.php` remain for existing views and
controllers. Shared flash, CSRF, formatting, redirect, and escaping behavior is
implemented in `src/Support/`.
