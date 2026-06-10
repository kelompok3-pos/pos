<?php

final class StockRepository extends ScopedRepository
{
    protected string $table = 'stock_movements';

    public function history(int $limit = 100): array
    {
        $scope = $this->actor->isSuperAdmin() ? '' : ' WHERE sm.store_id = ?';
        $params = $this->actor->isSuperAdmin() ? [] : [$this->actor->requireStoreId()];
        $retail = $this->hasColumn('quantity_change');
        $select = $retail
            ? 'sm.*, sm.type AS movement_type, ABS(sm.quantity_change) AS quantity,
               sm.quantity_before AS stock_before, sm.quantity_after AS stock_after,
               sm.reason AS note'
            : 'sm.*';
        $stmt = $this->pdo->prepare(
            "SELECT {$select}, p.name AS product_name, u.name AS user_name
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
        if ($this->hasColumn('quantity_change')) {
            return $this->insert([
                'product_id' => $productId,
                'user_id' => $this->actor->user_id,
                'type' => $type === 'in' ? 'restock' : ($type === 'out' ? 'adjustment' : $type),
                'quantity_before' => $before,
                'quantity_change' => $after - $before,
                'quantity_after' => $after,
                'reason' => $note,
            ]);
        }
        return $this->insert([
            'product_id' => $productId,
            'user_id' => $this->actor->user_id,
            'movement_type' => $type,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'note' => $note,
        ]);
    }
}
