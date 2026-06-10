<?php

require_once __DIR__ . '/../Controller.php';

final class SuperAdminController extends Controller
{
    private ActorContext $actor;
    private PDO $pdo;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->actor->requireRole('superadmin');
        $this->pdo = getConnection();
    }

    public function dashboard(): void
    {
        $today = date('Y-m-d');
        $report = $this->reportRows($today, $today, null);
        $storeTable = $this->tableName('stores', 'tenants');
        $recentAudit = [];
        if ($this->tableExists('audit_logs')) {
            $recentAudit = $this->pdo->query(
                "SELECT a.*, u.name AS user_name, s.name AS store_name
                 FROM audit_logs a
                 LEFT JOIN users u ON u.id = a.user_id
                 LEFT JOIN {$storeTable} s ON s.id = a.store_id
                 ORDER BY a.created_at DESC LIMIT 10"
            )->fetchAll();
        }
        $this->view('superadmin/dashboard', [
            'title' => 'Platform Overview',
            'stores' => $report,
            'recentAudit' => $recentAudit,
            'totals' => $this->totals($report),
        ]);
    }

    public function reports(): void
    {
        [$from, $to, $storeId] = $this->filters();
        $rows = $this->reportRows($from, $to, $storeId);
        $this->view('superadmin/reports/index', [
            'title' => 'Laporan Lintas Toko',
            'from' => $from,
            'to' => $to,
            'selectedStore' => $storeId,
            'rows' => $rows,
            'totals' => $this->totals($rows),
            'storeOptions' => $this->storeOptions(),
        ]);
    }

    public function stores(): void
    {
        $from = date('Y-m-01');
        $to = date('Y-m-d');
        $rows = $this->reportRows($from, $to, null);
        $userStmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE store_id = ?');
        $productStmt = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE store_id = ?');
        foreach ($rows as &$row) {
            $userStmt->execute([$row['store_id']]);
            $productStmt->execute([$row['store_id']]);
            $row['user_count'] = (int) $userStmt->fetchColumn();
            $row['product_count'] = (int) $productStmt->fetchColumn();
        }
        unset($row);
        $this->view('superadmin/stores/index', [
            'title' => 'Manajemen Toko',
            'rows' => $rows,
        ]);
    }

    public function audit(): void
    {
        $storeTable = $this->tableName('stores', 'tenants');
        $where = [];
        $params = [];
        foreach (['store_id' => 'a.store_id', 'user_id' => 'a.user_id'] as $input => $column) {
            $value = (int) ($_GET[$input] ?? 0);
            if ($value > 0) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
        }
        $action = trim((string) ($_GET['action'] ?? ''));
        if ($action !== '') {
            $where[] = 'a.action = ?';
            $params[] = $action;
        }
        $from = $this->validDate((string) ($_GET['from'] ?? ''), '');
        $to = $this->validDate((string) ($_GET['to'] ?? ''), '');
        if ($from !== '') {
            $where[] = 'DATE(a.created_at) >= ?';
            $params[] = $from;
        }
        if ($to !== '') {
            $where[] = 'DATE(a.created_at) <= ?';
            $params[] = $to;
        }
        $sql = "SELECT a.*, u.name AS user_name, s.name AS store_name
                FROM audit_logs a
                LEFT JOIN users u ON u.id = a.user_id
                LEFT JOIN {$storeTable} s ON s.id = a.store_id" .
                ($where ? ' WHERE ' . implode(' AND ', $where) : '') .
                ' ORDER BY a.created_at DESC LIMIT 50';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $this->view('superadmin/audit/index', [
            'title' => 'Audit Log',
            'rows' => $stmt->fetchAll(),
            'storeOptions' => $this->storeOptions(),
        ]);
    }

    public function exportReports(): void
    {
        [$from, $to, $storeId] = $this->filters();
        $rows = $this->reportRows($from, $to, $storeId);
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"laporan-lintas-toko-{$from}-{$to}.csv\"");
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Toko', 'Transaksi', 'Omzet', 'Pengeluaran', 'Net']);
        foreach ($rows as $row) {
            fputcsv($output, [$row['store_name'], $row['transaction_count'], $row['revenue'], $row['expenses'], $row['net']]);
        }
        $totals = $this->totals($rows);
        fputcsv($output, ['TOTAL', $totals['transactions'], $totals['revenue'], $totals['expenses'], $totals['net']]);
        fclose($output);
        exit;
    }

    private function reportRows(string $from, string $to, ?int $storeId): array
    {
        $storeTable = $this->tableName('stores', 'tenants');
        $totalColumn = $this->columnExists('transactions', 'total') ? 'total' : 'total_price';
        $whereStore = $storeId === null ? '' : ' WHERE s.id = ?';
        $sql = "SELECT s.id AS store_id, s.name AS store_name, s.status,
                       COALESCE(t.transaction_count, 0) AS transaction_count,
                       COALESCE(t.revenue, 0) AS revenue,
                       COALESCE(e.expenses, 0) AS expenses,
                       COALESCE(t.revenue, 0) - COALESCE(e.expenses, 0) AS net
                FROM {$storeTable} s
                LEFT JOIN (
                    SELECT store_id, COUNT(*) AS transaction_count, COALESCE(SUM({$totalColumn}), 0) AS revenue
                    FROM transactions WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY store_id
                ) t ON t.store_id = s.id
                LEFT JOIN (
                    SELECT store_id, COALESCE(SUM(amount), 0) AS expenses
                    FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY store_id
                ) e ON e.store_id = s.id
                {$whereStore}
                ORDER BY revenue DESC, s.name ASC";
        $params = [$from, $to, $from, $to];
        if ($storeId !== null) {
            $params[] = $storeId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function storeOptions(): array
    {
        $table = $this->tableName('stores', 'tenants');
        return $this->pdo->query("SELECT id, name FROM {$table} ORDER BY name ASC")->fetchAll();
    }

    private function totals(array $rows): array
    {
        return array_reduce($rows, static function (array $carry, array $row): array {
            $carry['stores']++;
            $carry['transactions'] += (int) $row['transaction_count'];
            $carry['revenue'] += (float) $row['revenue'];
            $carry['expenses'] += (float) $row['expenses'];
            $carry['net'] += (float) $row['net'];
            return $carry;
        }, ['stores' => 0, 'transactions' => 0, 'revenue' => 0.0, 'expenses' => 0.0, 'net' => 0.0]);
    }

    private function filters(): array
    {
        $from = $this->validDate((string) ($_GET['from'] ?? date('Y-m-01')), date('Y-m-01'));
        $to = $this->validDate((string) ($_GET['to'] ?? date('Y-m-d')), date('Y-m-d'));
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        $storeId = (int) ($_GET['store_id'] ?? 0);
        return [$from, $to, $storeId > 0 ? $storeId : null];
    }

    private function validDate(string $value, string $fallback): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $fallback;
    }

    private function tableName(string $preferred, string $fallback): string
    {
        return $this->tableExists($preferred) ? $preferred : $fallback;
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
