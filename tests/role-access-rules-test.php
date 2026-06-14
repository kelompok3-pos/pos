<?php

/**
 * Lightweight checks for role hierarchy and store-scope helpers.
 */

require_once __DIR__ . '/../helpers/functions.php';

function assertRule(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$_SESSION = [
    'user_id'  => 1,
    'role'     => 'super_admin',
    'name'     => 'Owner',
    'store_id' => null,
];

assertRule(creatableRoles() === ['admin', 'kasir'], 'Super admin must create admin and kasir.');
assertRule(canManageRole('admin'), 'Super admin must manage admin.');
assertRule(!canManageRole('super_admin'), 'Super admin must not manage an equal role.');
assertRule(isWithinCurrentStore(['store_id' => 99]), 'Super admin must see every store.');

$_SESSION = [
    'user_id'  => 2,
    'role'     => 'admin',
    'name'     => 'Store Admin',
    'store_id' => 10,
];

assertRule(creatableRoles() === ['kasir'], 'Admin must create kasir only.');
assertRule(canManageRole('kasir'), 'Admin must manage kasir.');
assertRule(!canManageRole('admin'), 'Admin must not manage an equal role.');
assertRule(isWithinCurrentStore(['store_id' => 10]), 'Admin must manage their own store.');
assertRule(!isWithinCurrentStore(['store_id' => 11]), 'Admin must not manage another store.');
assertRule(canManageUser(['role' => 'kasir', 'store_id' => 10, 'assigned_admin_id' => 2]), 'Admin must manage assigned cashier.');
assertRule(!canManageUser(['role' => 'kasir', 'store_id' => 10, 'assigned_admin_id' => 9]), 'Admin must not manage another admin cashier.');

$_SESSION['store_id'] = null;
assertRule(currentStoreId() === 0, 'Admin without a store must fail closed.');
assertRule(!isWithinCurrentStore(['store_id' => 0]), 'Unscoped admin must not manage users.');

$_SESSION = [
    'user_id'  => 3,
    'role'     => 'kasir',
    'name'     => 'Cashier',
    'store_id' => 10,
];

assertRule(creatableRoles() === [], 'Kasir must not create users.');
assertRule(!canManageRole('kasir'), 'Kasir must not manage users.');
assertRule(!canManageUser(['role' => 'kasir', 'store_id' => 10, 'assigned_admin_id' => 3]), 'Kasir must not manage users.');

$adminRoutes = require __DIR__ . '/../routes/admin.php';
assertRule(
    $adminRoutes['/settings'][2]['roles'] === [ROLE_ADMIN],
    'Store settings page must only be accessible by store admin.'
);
assertRule(
    $adminRoutes['/settings/update'][2]['roles'] === [ROLE_ADMIN],
    'Store settings update must only be accessible by store admin.'
);

echo "PASS: Role access rules passed.\n";
