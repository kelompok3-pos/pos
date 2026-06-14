<?php

require_once __DIR__ . '/ApiController.php';

final class ProductApiController extends ApiController
{
    public function productSearch(): void
    {
        $this->requireRole(ROLE_ADMIN, ROLE_KASIR);
        $query = '%' . trim((string) ($_GET['q'] ?? '')) . '%';
        $stmt = $this->pdo->prepare(
            "SELECT id, name, CONCAT('PRD-', id) AS sku, price AS selling_price, stock, 'pcs' AS unit
             FROM products
             WHERE store_id = ? AND status = 'active' AND deleted_at IS NULL AND name LIKE ?
             ORDER BY name ASC LIMIT 20"
        );
        $stmt->execute([$this->actor->requireStoreId(), $query]);
        $this->jsonSuccess($stmt->fetchAll());
    }

    public function productInfo(): void
    {
        $this->requireRole(ROLE_ADMIN, ROLE_KASIR);
        $id = (int) ($_GET['id'] ?? 0);
        $product = assertBelongsToStore($this->pdo, 'products', $id, $this->actor->requireStoreId());
        $this->jsonSuccess([
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'sku' => 'PRD-' . $product['id'],
            'stock' => (int) $product['stock'],
            'min_stock' => (int) $product['minimum_stock'],
            'unit' => 'pcs',
        ]);
    }
}
