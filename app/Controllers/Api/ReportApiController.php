<?php

require_once __DIR__ . '/ApiController.php';

final class ReportApiController extends ApiController
{
    public function chartDaily(): void
    {
        $this->requireRole(ROLE_SUPER_ADMIN, ROLE_ADMIN);
        $days = max(1, min((int) ($_GET['days'] ?? 7), 90));
        $storeId = $this->actor->isSuperAdmin()
            ? ((int) ($_GET['store_id'] ?? 0) ?: null)
            : $this->actor->requireStoreId();
        $scope = $storeId === null ? '' : ' AND store_id = ?';
        $stmt = $this->pdo->prepare(
            "SELECT DATE(created_at) AS sale_date, COUNT(*) AS transaction_count,
                    COALESCE(SUM(total_price), 0) AS revenue
             FROM transactions
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY){$scope}
             GROUP BY DATE(created_at) ORDER BY sale_date ASC"
        );
        $stmt->execute($storeId === null ? [] : [$storeId]);
        $this->jsonSuccess($stmt->fetchAll());
    }
}
