<?php

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/bootstrap/autoload.php';
require_once BASE_PATH . '/helpers/functions.php';
require_once BASE_PATH . '/bootstrap/middleware.php';

requireMethod('POST');
$actor = requireRole('admin');
verifyCsrf();

$name = trim($_POST['name'] ?? '');
$price = (float) ($_POST['price'] ?? -1);
$stock = (int) ($_POST['stock'] ?? -1);
if ($name === '' || $price < 0 || $stock < 0) {
    http_response_code(422);
    exit('Invalid product data.');
}

try {
    $repository = new ProductRepository(getConnection(), $actor);
    $id = $repository->insert([
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'minimum_stock' => max(0, (int) ($_POST['minimum_stock'] ?? 5)),
        'status' => 'active',
        'description' => trim($_POST['description'] ?? ''),
    ]);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'id' => $id], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    error_log($exception->__toString());
    http_response_code(500);
    echo 'Unable to create product.';
}
