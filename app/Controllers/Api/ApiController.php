<?php

require_once __DIR__ . '/../Controller.php';

final class ApiController extends Controller
{
    private ActorContext $actor;
    private PDO $pdo;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->pdo = getConnection();
    }

    public function productSearch(): void
    {
        $this->actor->requireRole('admin', 'kasir');
        $q = '%' . trim((string) ($_GET['q'] ?? '')) . '%';
        $retail = $this->hasColumn('products', 'selling_price');
        $sql = 'SELECT id, name, ' . ($retail ? 'sku, selling_price, stock, unit' : "CONCAT('PRD-', id) AS sku, price AS selling_price, stock, 'pcs' AS unit") .
            " FROM products WHERE store_id = ? AND status = 'active' AND (name LIKE ? " .
            ($retail ? 'OR sku LIKE ?' : '') . ') ORDER BY name ASC LIMIT 20';
        $stmt = $this->pdo->prepare($sql);
        $params = [$this->actor->requireStoreId(), $q];
        if ($retail) {
            $params[] = $q;
        }
        $stmt->execute($params);
        $this->json(true, 'Produk ditemukan.', $stmt->fetchAll());
    }

    public function calculateCart(): void
    {
        $this->actor->requireRole('admin', 'kasir');
        $this->json(true, 'Perhitungan berhasil.', (new RetailTransactionService($this->pdo, $this->actor))->calculate($this->payload()));
    }

    public function submitTransaction(): void
    {
        $this->actor->requireRole('kasir');
        $id = (new RetailTransactionService($this->pdo, $this->actor))->submit($this->payload());
        $this->json(true, 'Transaksi berhasil diproses.', ['transaction_id' => $id]);
    }

    public function productInfo(): void
    {
        $this->actor->requireRole('admin', 'kasir');
        $id = (int) ($_GET['id'] ?? 0);
        $product = assertBelongsToStore($this->pdo, 'products', $id, $this->actor->requireStoreId());
        $this->json(true, 'Produk ditemukan.', [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'sku' => $product['sku'] ?? 'PRD-' . $product['id'],
            'stock' => (int) $product['stock'],
            'min_stock' => (int) ($product['min_stock'] ?? $product['minimum_stock'] ?? 0),
            'unit' => $product['unit'] ?? 'pcs',
        ]);
    }

    public function chartDaily(): void
    {
        $this->actor->requireRole('superadmin', 'admin');
        $days = max(1, min((int) ($_GET['days'] ?? 7), 90));
        $storeId = $this->actor->isSuperAdmin()
            ? ((int) ($_GET['store_id'] ?? 0) ?: null)
            : $this->actor->requireStoreId();
        $totalColumn = $this->hasColumn('transactions', 'total') ? 'total' : 'total_price';
        $scope = $storeId === null ? '' : ' AND store_id = ?';
        $stmt = $this->pdo->prepare(
            "SELECT DATE(created_at) AS sale_date, COUNT(*) AS transaction_count,
                    COALESCE(SUM({$totalColumn}), 0) AS revenue
             FROM transactions
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY){$scope}
             GROUP BY DATE(created_at) ORDER BY sale_date ASC"
        );
        $stmt->execute($storeId === null ? [] : [$storeId]);
        $this->json(true, 'Data chart berhasil dimuat.', $stmt->fetchAll());
    }

    private function payload(): array
    {
        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Payload JSON tidak valid.');
        }
        return $payload;
    }

    private function json(bool $success, string $message, mixed $data = null): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function hasColumn(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
