<?php

final class StockRepository extends ScopedRepository
{
    protected string $table = 'stock_movements';

    public function history(int $limit = 100): array
    {
        $scope = $this->actor->isSuperAdmin() ? '' : ' WHERE sm.store_id = ?';
        $params = $this->actor->isSuperAdmin() ? [] : [$this->actor->requireStoreId()];
        $stmt = $this->pdo->prepare(
            "SELECT sm.*, p.name AS product_name, u.name AS user_name
             FROM stock_movements sm
             INNER JOIN products p ON p.id = sm.product_id AND p.store_id = sm.store_id
             LEFT JOIN users u ON u.id = sm.user_id
             {$scope}
             ORDER BY sm.created_at DESC, sm.id DESC LIMIT " . max(1, min($limit, 500))
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function record(
        int $productId,
        string $type,
        int $quantity,
        int $before,
        int $after,
        string $note = ''
    ): int {
        if (!in_array($type, ['in', 'out', 'restock', 'sale', 'void', 'adjustment'], true) || $quantity <= 0) {
            throw new InvalidArgumentException('Invalid stock movement.');
        }
        assertBelongsToStore('products', $productId, $this->actor->requireStoreId());
        $movementType = match ($type) {
            'restock', 'void' => 'in',
            default => $type,
        };
        return $this->insert([
            'product_id' => $productId,
            'user_id' => $this->actor->user_id,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'note' => $note,
        ]);
    }
}
