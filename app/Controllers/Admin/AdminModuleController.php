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
        ExcelExporter::download(
            'sales-report-' . $from . '-' . $to . '.xlsx',
            'Laporan Penjualan',
            [
                ['key' => 'transaction_code', 'label' => 'Invoice', 'width' => 20],
                ['key' => 'cashier_name', 'label' => 'Kasir', 'width' => 20],
                ['key' => 'subtotal', 'label' => 'Subtotal', 'type' => 'currency', 'width' => 18],
                ['key' => 'tax_amount', 'label' => 'Pajak', 'type' => 'currency', 'width' => 18],
                ['key' => 'total_price', 'label' => 'Total', 'type' => 'currency', 'width' => 18],
                ['key' => 'payment_method', 'label' => 'Pembayaran', 'width' => 16],
                ['key' => 'created_at', 'label' => 'Tanggal', 'type' => 'datetime', 'width' => 20],
            ],
            $transactionModel->getByDateRange($from, $to),
            ['Periode' => $from . ' s/d ' . $to, 'Dibuat pada' => date('d/m/Y H:i')]
        );
    }

    public function settings(): void
    {
        allowOnly([ROLE_ADMIN]);
        $this->view('admin/settings/index', [
            'title'    => 'Pengaturan Toko & Struk',
            'settings' => (new Setting())->all(),
        ]);
    }

    public function updateSettings(): void
    {
        allowOnly([ROLE_ADMIN]);
        verifyCsrf();
        $settings = [
            'store_name' => trim((string) ($_POST['store_name'] ?? '')),
            'store_address' => trim((string) ($_POST['store_address'] ?? '')),
            'currency_symbol' => trim((string) ($_POST['currency_symbol'] ?? '')),
            'tax_percentage' => trim((string) ($_POST['tax_percentage'] ?? '')),
            'receipt_footer' => trim((string) ($_POST['receipt_footer'] ?? '')),
        ];
        $tax = filter_var($settings['tax_percentage'], FILTER_VALIDATE_FLOAT);
        if (
            $settings['store_name'] === ''
            || mb_strlen($settings['store_name']) > 150
            || mb_strlen($settings['store_address']) > 500
            || $settings['currency_symbol'] === ''
            || mb_strlen($settings['currency_symbol']) > 8
            || $tax === false
            || $tax < 0
            || $tax > 100
            || mb_strlen($settings['receipt_footer']) > 500
        ) {
            flash('error', 'Pengaturan toko tidak valid. Periksa nama toko, simbol mata uang, pajak, dan panjang teks.');
            $this->redirect('/settings');
        }
        $settings['tax_percentage'] = rtrim(rtrim(number_format((float) $tax, 2, '.', ''), '0'), '.');

        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
            $directory = BASE_PATH . '/public/assets/uploads';
            try {
                $settings['store_logo'] = 'uploads/' . uploadImage($_FILES['store_logo'], $directory);
            } catch (Throwable $exception) {
                flash('error', $exception->getMessage());
                $this->redirect('/settings');
            }
        }
        (new Setting())->updateMany($settings);
        flash('success', 'Pengaturan toko dan struk berhasil disimpan.');
        $this->redirect('/settings');
    }
}
