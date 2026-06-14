<?php

require_once __DIR__ . '/../Controller.php';

final class AdminExpenseController extends Controller
{
    private ActorContext $actor;
    private ExpenseService $expenseService;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->actor->requireRole(ROLE_ADMIN);
        $pdo = getConnection();
        $this->expenseService = new ExpenseService($pdo, $this->actor);
    }

    public function index(): void
    {
        $from = self::dateOrFallback((string) ($_GET['from'] ?? date('Y-m-01')), date('Y-m-01'));
        $to = self::dateOrFallback((string) ($_GET['to'] ?? date('Y-m-d')), date('Y-m-d'));
        $category = trim((string) ($_GET['category'] ?? ''));
        $where = ['store_id = ?', 'expense_date BETWEEN ? AND ?', 'deleted_at IS NULL'];
        $params = [$this->actor->requireStoreId(), $from, $to];
        if (in_array($category, self::categories(), true)) {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        $stmt = getConnection()->prepare(
            'SELECT * FROM expenses WHERE ' . implode(' AND ', $where) . ' ORDER BY expense_date DESC, id DESC'
        );
        $stmt->execute($params);
        $summaryStmt = getConnection()->prepare(
            'SELECT category, COALESCE(SUM(amount), 0) AS total FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ? AND deleted_at IS NULL
             GROUP BY category ORDER BY total DESC'
        );
        $summaryStmt->execute([$this->actor->requireStoreId(), $from, $to]);
        $this->view('admin/expense/index', [
            'title' => 'Pengeluaran',
            'expenses' => $stmt->fetchAll(),
            'summary' => $summaryStmt->fetchAll(),
            'from' => $from,
            'to' => $to,
            'selectedCategory' => $category,
            'categories' => self::categories(),
        ]);
    }

    public function store(): void
    {
        verifyCsrf();
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $amount = (float) ($_POST['amount'] ?? 0);
        $date = trim($_POST['expense_date'] ?? date('Y-m-d'));

        if (!in_array($category, self::categories(), true) || $description === '' || $amount <= 0 || !self::isDate($date)) {
            flash('error', 'Data pengeluaran tidak valid.');
            $this->redirect('/admin/expense');
        }
        $this->expenseService->create(compact('category', 'amount', 'description') + ['expense_date' => $date]);
        flash('success', 'Pengeluaran berhasil dicatat.');
        $this->redirect('/admin/expense');
    }

    public function update(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        assertBelongsToStore('expenses', $id, $this->actor->requireStoreId());
        $category = trim((string) ($_POST['category'] ?? ''));
        $amount = (float) ($_POST['amount'] ?? 0);
        $description = trim((string) ($_POST['description'] ?? ''));
        $date = trim((string) ($_POST['expense_date'] ?? ''));
        if (!in_array($category, self::categories(), true) || $amount <= 0 || $description === '' || !self::isDate($date)) {
            flash('error', 'Data pengeluaran tidak valid.');
            $this->redirect('/admin/expense');
        }
        $this->expenseService->update($id, compact('category', 'amount', 'description') + ['expense_date' => $date]);
        flash('success', 'Pengeluaran berhasil diperbarui.');
        $this->redirect('/admin/expense');
    }

    public function export(): void
    {
        $this->indexExportRows();
    }

    public function delete(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        assertBelongsToStore('expenses', $id, $this->actor->requireStoreId());
        $this->expenseService->delete($id);
        flash('success', 'Pengeluaran berhasil dihapus.');
        $this->redirect('/admin/expense');
    }

    private static function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function indexExportRows(): void
    {
        $from = self::dateOrFallback((string) ($_GET['from'] ?? date('Y-m-01')), date('Y-m-01'));
        $to = self::dateOrFallback((string) ($_GET['to'] ?? date('Y-m-d')), date('Y-m-d'));
        $stmt = getConnection()->prepare(
            'SELECT expense_date, category, description, amount FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ? AND deleted_at IS NULL
             ORDER BY expense_date DESC'
        );
        $stmt->execute([$this->actor->requireStoreId(), $from, $to]);
        ExcelExporter::download(
            "pengeluaran-{$from}-{$to}.xlsx",
            'Laporan Pengeluaran',
            [
                ['key' => 'expense_date', 'label' => 'Tanggal', 'type' => 'date', 'width' => 16],
                ['key' => 'category', 'label' => 'Kategori', 'width' => 18],
                ['key' => 'description', 'label' => 'Deskripsi', 'width' => 40],
                ['key' => 'amount', 'label' => 'Nominal', 'type' => 'currency', 'width' => 20],
            ],
            $stmt->fetchAll(),
            ['Periode' => $from . ' s/d ' . $to, 'Dibuat pada' => date('d/m/Y H:i')]
        );
    }

    private static function categories(): array
    {
        return ['operational', 'purchase', 'salary', 'other'];
    }

    private static function dateOrFallback(string $value, string $fallback): string
    {
        return self::isDate($value) ? $value : $fallback;
    }
}
