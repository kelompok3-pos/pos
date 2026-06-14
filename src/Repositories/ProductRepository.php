<?php

final class ProductRepository extends ScopedRepository
{
    protected string $table = 'products';

    public function active(): array
    {
        return $this->selectProducts("status = 'active'" . $this->notDeleted());
    }

    public function management(): array
    {
        return $this->selectProducts('1 = 1' . $this->notDeleted());
    }

    public function findActiveById(int $id): ?array
    {
        $row = $this->findById($id);
        return $row && $row['deleted_at'] === null ? $row : null;
    }

    public function softDelete(int $id): bool
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function countActiveRecords(): int
    {
        return $this->aggregate('COUNT(*)', '1 = 1' . $this->notDeleted());
    }

    public function totalStock(): int
    {
        return $this->aggregate('COALESCE(SUM(stock), 0)', '1 = 1' . $this->notDeleted());
    }

    public function lowStock(int $threshold): array
    {
        return $this->selectProducts(
            "stock <= CASE WHEN minimum_stock > 0 THEN minimum_stock ELSE ? END
             AND status = 'active'" . $this->notDeleted(),
            [$threshold],
            'stock ASC, name ASC'
        );
    }

    public function adjustStock(int $id, int $delta): bool
    {
        $old = $this->findById($id);
        if ($old === null) {
            return false;
        }
        [$where, $params] = $this->buildWhere(['id' => $id]);
        $stmt = $this->pdo->prepare(
            "UPDATE products SET stock = stock + ?{$where} AND stock + ? >= 0" . $this->notDeleted()
        );
        $stmt->execute([$delta, ...$params, $delta]);
        if ($stmt->rowCount() === 1) {
            AuditLogger::log($this->actor, 'stock_adjust', 'products', $id, $old, ['delta' => $delta], $this->pdo);
            return true;
        }
        return false;
    }

    private function selectProducts(string $condition, array $params = [], string $order = 'id DESC'): array
    {
        $scope = $this->actor->isSuperAdmin() ? '' : 'store_id = ? AND ';
        if (!$this->actor->isSuperAdmin()) {
            array_unshift($params, $this->actor->requireStoreId());
        }
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE {$scope}{$condition} ORDER BY {$order}");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function aggregate(string $expression, string $condition): int
    {
        $scope = $this->actor->isSuperAdmin() ? '' : 'store_id = ? AND ';
        $stmt = $this->pdo->prepare("SELECT {$expression} FROM products WHERE {$scope}{$condition}");
        $stmt->execute($this->actor->isSuperAdmin() ? [] : [$this->actor->requireStoreId()]);
        return (int) $stmt->fetchColumn();
    }

    private function notDeleted(): string
    {
        return ' AND deleted_at IS NULL';
    }
}
