<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Models/Setting.php';

class AdminModuleController extends Controller
{
    public function __construct()
    {
        allowOnly([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
    }

    public function inventory(): void
    {
        $actor = ActorContext::fromSession();
        $actor->requireRole(ROLE_ADMIN);
        $productModel = new ProductRepository(getConnection(), $actor);
        $movementRepository = new StockRepository(getConnection(), $actor);
        $this->view('admin/inventory/index', [
            'title'      => 'Inventory / Stock',
            'products'   => $productModel->management(),
            'lowStock'   => $productModel->lowStock(5),
            'movements'  => $movementRepository->history(),
        ]);
    }

    public function adjustStock(): void
    {
        verifyCsrf();
        $actor = ActorContext::fromSession();
        $actor->requireRole(ROLE_ADMIN);
        $inventoryService = new InventoryService(getConnection(), $actor);
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $type = $_POST['movement_type'] ?? '';
        if ($quantity <= 0 || !in_array($type, ['in', 'out'], true)) {
            flash('error', 'Data perubahan stok tidak valid.');
            $this->redirect('/inventory');
        }

        $delta = $type === 'in' ? $quantity : -$quantity;
        try {
            $inventoryService->adjust($productId, $delta, trim($_POST['note'] ?? ''));
        } catch (Throwable) {
            flash('error', 'Stok tidak mencukupi untuk pengurangan tersebut.');
            $this->redirect('/inventory');
        }
        flash('success', 'Stok berhasil diperbarui.');
        $this->redirect('/inventory');
    }

    public function reports(): void
    {
        $transactionModel = new Transaction();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $this->view('admin/reports/index', [
            'title'        => 'Reports',
            'from'         => $from,
            'to'           => $to,
            'transactions' => $transactionModel->getByDateRange($from, $to),
            'topProducts'  => $transactionModel->getTopSellingProducts(),
            'cashiers'     => $transactionModel->getCashierPerformance($from, $to),
            'dailyRevenue' => $transactionModel->revenueByDateRange(date('Y-m-d'), date('Y-m-d')),
            'weeklyRevenue' => $transactionModel->revenueByDateRange(date('Y-m-d', strtotime('monday this week')), date('Y-m-d')),
            'monthlyRevenue' => $transactionModel->revenueByDateRange(date('Y-m-01'), date('Y-m-d')),
        ]);
    }

    public function exportReports(): void
    {
        $transactionModel = new Transaction();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sales-report-' . $from . '-' . $to . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Invoice', 'Kasir', 'Subtotal', 'Tax', 'Total', 'Payment', 'Date']);
        foreach ($transactionModel->getByDateRange($from, $to) as $row) {
            fputcsv($output, [$row['transaction_code'], $row['cashier_name'], $row['subtotal'], $row['tax_amount'], $row['total_price'], $row['payment_method'], $row['created_at']]);
        }
        fclose($output);
        exit;
    }

    public function settings(): void
    {
        allowOnly([ROLE_SUPER_ADMIN]);
        $this->view('admin/settings/index', [
            'title'    => 'Settings',
            'settings' => (new Setting())->all(),
        ]);
    }

    public function updateSettings(): void
    {
        allowOnly([ROLE_SUPER_ADMIN]);
        verifyCsrf();
        $allowed = ['store_name', 'store_address', 'currency_symbol', 'tax_percentage', 'receipt_footer', 'system_timezone'];
        $settings = [];
        foreach ($allowed as $key) {
            $settings[$key] = trim($_POST[$key] ?? '');
        }
        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
            $directory = BASE_PATH . '/public/assets/uploads';
            $settings['store_logo'] = 'uploads/' . uploadImage($_FILES['store_logo'], $directory);
        }
        (new Setting())->updateMany($settings);
        flash('success', 'Pengaturan berhasil disimpan.');
        $this->redirect('/settings');
    }
}
