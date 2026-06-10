<?php

ini_set('session.save_path', sys_get_temp_dir());

require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/bootstrap/autoload.php';
require_once BASE_PATH . '/helpers/functions.php';
require_once BASE_PATH . '/bootstrap/middleware.php';

function assertTenant(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$pdo = getConnection();
$product = $pdo->query("SELECT id, store_id FROM products ORDER BY id LIMIT 1")->fetch();
if (!$product) {
    echo "SKIP: No product available for tenant isolation test.\n";
    exit(0);
}

$foreignStoreId = (int) $product['store_id'] === 1 ? 3 : 1;
$foreignActor = new ActorContext(999999, $foreignStoreId, 'admin', 'Isolation Test');
$repository = new ProductRepository($pdo, $foreignActor);

try {
    $repository->findById((int) $product['id']);
    throw new RuntimeException('A store repository must reject a product from another store.');
} catch (UnauthorizedException) {
    // Expected.
}

$realForeignAdminStmt = $pdo->prepare(
    "SELECT id FROM users WHERE role = 'admin' AND store_id = ? AND deleted_at IS NULL LIMIT 1"
);
$realForeignAdminStmt->execute([$foreignStoreId]);
$realForeignAdminId = (int) $realForeignAdminStmt->fetchColumn();
if ($realForeignAdminId > 0) {
    $_SESSION = ['user_id' => $realForeignAdminId, 'role' => 'admin', 'name' => 'Test', 'store_id' => $foreignStoreId];
    $validatedActor = ActorContext::fromSession();
    $validatedRepository = new ProductRepository($pdo, $validatedActor);
    try {
        $validatedRepository->findById((int) $product['id']);
        throw new RuntimeException('A revalidated real Admin must reject a product from another store.');
    } catch (UnauthorizedException) {
        // Expected.
    }
}

try {
    assertBelongsToStore('products', (int) $product['id'], $foreignStoreId);
    throw new RuntimeException('assertBelongsToStore must reject a foreign product.');
} catch (UnauthorizedException) {
    // Expected.
}

echo "PASS: Tenant isolation checks passed.\n";
