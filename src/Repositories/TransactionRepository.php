<?php

final class TransactionRepository extends ScopedRepository
{
    protected string $table = 'transactions';

    public function findWithCashier(int $id): ?array
    {
        $scope = $this->actor->isSuperAdmin() ? '' : ' AND t.store_id = ?';
        $params = [$id];
        if (!$this->actor->isSuperAdmin()) {
            $params[] = $this->actor->requireStoreId();
        }
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name
             FROM transactions t
             INNER JOIN users u ON u.id = t.cashier_id
             WHERE t.id = ?{$scope} LIMIT 1"
        );
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }
}
