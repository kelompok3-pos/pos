<?php

final class ReportService
{
    public function __construct(private PDO $pdo, private ActorContext $actor)
    {
        $actor->requireRole(ROLE_SUPER_ADMIN, ROLE_ADMIN);
    }

    public function dailySales(string $date, ?int $storeId): array
    {
        $storeId = $this->scopeStoreId($storeId);
        $scope = $storeId === null ? '' : ' AND store_id = ?';
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS transaction_count, COALESCE(SUM(total_price), 0) AS revenue
             FROM transactions WHERE DATE(created_at) = ?{$scope}"
        );
        $stmt->execute($storeId === null ? [$date] : [$date, $storeId]);
        return $stmt->fetch() ?: ['transaction_count' => 0, 'revenue' => 0];
    }

    public function monthlySummary(int $year, int $month): array
    {
        $storeId = $this->scopeStoreId(null);
        $scope = $storeId === null ? '' : ' AND store_id = ?';
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS transaction_count, COALESCE(SUM(total_price), 0) AS revenue
             FROM transactions WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?{$scope}"
        );
        $params = [$year, $month];
        if ($storeId !== null) {
            $params[] = $storeId;
        }
        $stmt->execute($params);
        return $stmt->fetch() ?: ['transaction_count' => 0, 'revenue' => 0];
    }

    public function exportTransactions(string $from, string $to): array
    {
        $storeId = $this->scopeStoreId(null);
        $scope = $storeId === null ? '' : ' AND t.store_id = ?';
        $stmt = $this->pdo->prepare(
            "SELECT t.*, u.name AS cashier_name FROM transactions t
             INNER JOIN users u ON u.id = t.cashier_id
             WHERE DATE(t.created_at) BETWEEN ? AND ?{$scope}
             ORDER BY t.created_at DESC"
        );
        $params = [$from, $to];
        if ($storeId !== null) {
            $params[] = $storeId;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function scopeStoreId(?int $requestedStoreId): ?int
    {
        if (!$this->actor->isSuperAdmin()) {
            return $this->actor->requireStoreId();
        }
        return $requestedStoreId !== null && $requestedStoreId > 0 ? $requestedStoreId : null;
    }
}
