<?php

ini_set('session.save_path', sys_get_temp_dir());

require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/bootstrap/autoload.php';
require_once BASE_PATH . '/helpers/functions.php';
require_once BASE_PATH . '/app/Models/Setting.php';

function assertSettings(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$pdo = getConnection();
assertSettings(
    StoreSettingDefaults::KEYS === [
        'store_name',
        'store_address',
        'store_logo',
        'currency_symbol',
        'tax_percentage',
        'receipt_footer',
    ],
    'Only store-owned POS and receipt settings may be configured per store.'
);
assertSettings(
    (int) $pdo->query(
        "SELECT COUNT(*) FROM (
            SELECT store_id FROM settings
            WHERE setting_key IN ('store_name', 'store_address', 'store_logo', 'currency_symbol', 'tax_percentage', 'receipt_footer')
            GROUP BY store_id HAVING COUNT(DISTINCT setting_key) = 6
        ) complete_settings"
    )->fetchColumn() === (int) $pdo->query('SELECT COUNT(*) FROM tenants')->fetchColumn(),
    'Every existing store must have a complete independent settings set.'
);
assertSettings(
    (int) $pdo->query("SELECT COUNT(*) FROM settings WHERE setting_key = 'system_timezone'")->fetchColumn() === 0,
    'Legacy system timezone must not remain in store-owned settings.'
);

$admins = $pdo->query(
    "SELECT u.id, u.store_id, u.name, t.name AS store_name
     FROM users u JOIN tenants t ON t.id = u.store_id
     WHERE u.role = 'admin' AND u.deleted_at IS NULL
     ORDER BY u.store_id LIMIT 2"
)->fetchAll();

if (count($admins) < 2) {
    echo "SKIP: At least two active store admins are required for settings isolation test.\n";
    exit(0);
}

$firstActor = new ActorContext(
    (int) $admins[0]['id'],
    (int) $admins[0]['store_id'],
    ROLE_ADMIN,
    (string) $admins[0]['name'],
    (string) $admins[0]['store_name']
);
$secondActor = new ActorContext(
    (int) $admins[1]['id'],
    (int) $admins[1]['store_id'],
    ROLE_ADMIN,
    (string) $admins[1]['name'],
    (string) $admins[1]['store_name']
);

$pdo->beginTransaction();
try {
    $firstSettings = new Setting($pdo, $firstActor);
    $secondSettings = new Setting($pdo, $secondActor);
    $secondFooterBefore = $secondSettings->all()['receipt_footer'];

    $firstSettings->updateMany(['receipt_footer' => 'ISOLATION TEST STORE A']);

    assertSettings(
        $firstSettings->all()['receipt_footer'] === 'ISOLATION TEST STORE A',
        'Admin must update settings for their own store.'
    );
    assertSettings(
        $secondSettings->all()['receipt_footer'] === $secondFooterBefore,
        'Updating one store must not change another store settings.'
    );

    try {
        (new Setting($pdo, new ActorContext(1, null, ROLE_SUPER_ADMIN, 'Platform Owner')))->all();
        throw new RuntimeException('Super admin without store context must not read store settings.');
    } catch (UnauthorizedException) {
        // Expected.
    }
} finally {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

echo "PASS: Store settings are isolated and fail closed without store context.\n";
