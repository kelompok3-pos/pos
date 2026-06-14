<?php

function requireAuth(): ActorContext
{
    try {
        return ActorContext::fromSession();
    } catch (UnauthorizedException) {
        if (isApiRequest()) {
            apiError('Authentication required.', 401);
        }
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login');
    }
}

function requireMethod(string ...$methods): void
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $allowed = array_map('strtoupper', $methods);
    if (!in_array($method, $allowed, true)) {
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowed));
        if (isApiRequest()) {
            apiError('Method Not Allowed', 405);
        }
        exit('Method Not Allowed');
    }
}

function requireRole(string ...$roles): ActorContext
{
    $actor = requireAuth();
    try {
        $actor->requireRole(...$roles);
    } catch (UnauthorizedException) {
        if (isApiRequest()) {
            apiError('Access denied.', 403);
        }
        http_response_code(403);
        exit('Access denied.');
    }
    return $actor;
}

function assertBelongsToStore(PDO|string $pdoOrTable, string|int $tableOrId, int $idOrStore, ?int $storeId = null): array
{
    $pdo = $pdoOrTable instanceof PDO ? $pdoOrTable : getConnection();
    $table = $pdoOrTable instanceof PDO ? (string) $tableOrId : $pdoOrTable;
    $id = $pdoOrTable instanceof PDO ? $idOrStore : (int) $tableOrId;
    $scopeId = $pdoOrTable instanceof PDO ? (int) $storeId : $idOrStore;
    $allowed = [
        'products', 'transactions', 'transaction_items', 'stock_movements', 'expenses',
        'cashier_shifts', 'users',
    ];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Unsupported scoped table.');
    }
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? AND store_id = ? LIMIT 1");
    $stmt->execute([$id, $scopeId]);
    $row = $stmt->fetch();
    if (!$row) {
        throw new UnauthorizedException('Record does not belong to the current store.');
    }
    return $row;
}

function isApiRequest(): bool
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    return str_contains($path, '/api/');
}

function apiError(string $message, int $status = 400): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message, 'data' => null], JSON_UNESCAPED_UNICODE);
    exit;
}
