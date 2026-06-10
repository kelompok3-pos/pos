<?php

/**
 * =================================================================
 * CLI SUPER ADMIN SEEDER
 * =================================================================
 * Creates a super_admin account. This file cannot run over HTTP.
 *
 * Usage:
 * php database/seed_super_admin.php "Owner Name" owner@example.com "password"
 * =================================================================
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

[$script, $name, $email, $password] = array_pad($argv, 4, null);

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8) {
    fwrite(STDERR, "Usage: php database/seed_super_admin.php \"Owner Name\" owner@example.com \"password-min-8\"\n");
    exit(1);
}

$pdo = getConnection();
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role, store_id) VALUES (?, ?, ?, 'super_admin', NULL)"
);

try {
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    fwrite(STDOUT, "Super admin created: {$email}\n");
} catch (PDOException $exception) {
    fwrite(STDERR, "Failed to create super admin: {$exception->getMessage()}\n");
    exit(1);
}
