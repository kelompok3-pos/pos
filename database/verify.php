<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$pdo = getConnection();
$failures = [];

$requiredTables = [
    'tenants', 'users', 'products', 'transactions', 'transaction_items',
    'settings', 'stock_movements', 'expenses', 'cashier_shifts', 'audit_logs', 'migrations',
];
foreach ($requiredTables as $table) {
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
    );
    $stmt->execute([$table]);
    if ((int) $stmt->fetchColumn() !== 1) {
        $failures[] = "missing table {$table}";
    }
}

$indexPrefixes = [
    'products' => ['store_id', 'status'],
    'transactions' => ['store_id', 'created_at'],
    'expenses' => ['store_id', 'created_at'],
    'stock_movements' => ['store_id', 'product_id'],
    'cashier_shifts' => ['store_id', 'kasir_id'],
];
foreach ($indexPrefixes as $table => $prefix) {
    $stmt = $pdo->prepare(
        'SELECT index_name, GROUP_CONCAT(column_name ORDER BY seq_in_index) AS columns_list
         FROM information_schema.statistics
         WHERE table_schema = DATABASE() AND table_name = ?
         GROUP BY index_name'
    );
    $stmt->execute([$table]);
    $found = false;
    foreach ($stmt->fetchAll() as $index) {
        $columns = explode(',', (string) $index['columns_list']);
        if (array_slice($columns, 0, count($prefix)) === $prefix) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $failures[] = "missing index prefix {$table}(" . implode(',', $prefix) . ')';
    }
}

$requiredForeignKeys = [
    ['users', ['store_id'], 'tenants', ['id']],
    ['products', ['store_id'], 'tenants', ['id']],
    ['transactions', ['store_id'], 'tenants', ['id']],
    ['transactions', ['store_id', 'cashier_id'], 'users', ['store_id', 'id']],
    ['transaction_items', ['store_id'], 'tenants', ['id']],
    ['transaction_items', ['store_id', 'transaction_id'], 'transactions', ['store_id', 'id']],
    ['stock_movements', ['store_id'], 'tenants', ['id']],
    ['stock_movements', ['store_id', 'product_id'], 'products', ['store_id', 'id']],
    ['stock_movements', ['store_id', 'user_id'], 'users', ['store_id', 'id']],
    ['expenses', ['store_id'], 'tenants', ['id']],
    ['expenses', ['created_by'], 'users', ['id']],
    ['cashier_shifts', ['store_id'], 'tenants', ['id']],
    ['cashier_shifts', ['store_id', 'kasir_id'], 'users', ['store_id', 'id']],
    ['settings', ['store_id'], 'tenants', ['id']],
    ['audit_logs', ['store_id'], 'tenants', ['id']],
    ['audit_logs', ['user_id'], 'users', ['id']],
];
foreach ($requiredForeignKeys as [$table, $columns, $referencedTable, $referencedColumns]) {
    $stmt = $pdo->prepare(
        'SELECT GROUP_CONCAT(column_name ORDER BY ordinal_position) AS columns_list,
                referenced_table_name,
                GROUP_CONCAT(referenced_column_name ORDER BY ordinal_position) AS referenced_columns
         FROM information_schema.key_column_usage
         WHERE table_schema = DATABASE() AND table_name = ? AND referenced_table_name IS NOT NULL
         GROUP BY constraint_name, referenced_table_name'
    );
    $stmt->execute([$table]);
    $found = false;
    foreach ($stmt->fetchAll() as $foreignKey) {
        if (
            explode(',', (string) $foreignKey['columns_list']) === $columns
            && $foreignKey['referenced_table_name'] === $referencedTable
            && explode(',', (string) $foreignKey['referenced_columns']) === $referencedColumns
        ) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $failures[] = "missing FK {$table}(" . implode(',', $columns) . ") -> {$referencedTable}("
            . implode(',', $referencedColumns) . ')';
    }
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        echo "FAIL {$failure}\n";
    }
    exit(1);
}

echo "PASS canonical database schema verified.\n";
