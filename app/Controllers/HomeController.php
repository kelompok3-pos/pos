<?php

require_once __DIR__ . '/Controller.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Models/User.php';

/**
 * =================================================================
 * HOME CONTROLLER
 * =================================================================
 * Controller untuk memproses data analisis finansial halaman Dashboard.
 * =================================================================
 */

class HomeController extends Controller
{
    /**
     * Landing page publik sebelum login
     */
    public function landing(): void
    {
        if (isAuthenticated()) {
            $this->redirect(currentRole() === ROLE_KASIR ? '/kasir/transaction' : '/dashboard');
        }

        $title = 'POS App';
        require BASE_PATH . '/app/Views/home/landing.php';
    }

    public function index(): void
    {
        if (isRole(ROLE_KASIR)) {
            $this->redirect('/kasir/transaction');
        }
        allowOnly([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $productModel     = new ProductRepository(getConnection(), ActorContext::fromSession());
        $transactionModel = new Transaction();
        $userModel        = new User();

        $totalProducts = $productModel->countActiveRecords();
        $totalStock    = $productModel->totalStock();
        $todayRevenue  = $transactionModel->todayRevenue();
        $monthRevenue  = $transactionModel->monthRevenue();
        $todaySales    = $transactionModel->todayCount();
        $dailySalesChart = $transactionModel->getDailySalesChartData(7);
        $monthlySalesChart = $transactionModel->getSalesChartData();
        $visibleUsers = $userModel->getAll(currentStoreId());
        $totalUsers = count(array_filter($visibleUsers, fn(array $user): bool =>
            $user['deleted_at'] === null && (isSuperAdmin() ? $user['role'] !== 'super_admin' : $user['role'] === 'kasir')
        ));
        $lowStockProducts = $productModel->lowStock(5);

        $todayHeaders = $transactionModel->getRecentTransactions();
        $todayDetails = [];
        foreach ($todayHeaders as $transaction) {
            $todayDetails[(int) $transaction['id']] = $transactionModel->getDetails((int) $transaction['id']);
        }

        $this->view('home/index', [
            'title'        => 'Dashboard',
            'totalProducts' => $totalProducts,
            'totalStock'    => $totalStock,
            'todayRevenue'  => $todayRevenue,
            'monthRevenue'  => $monthRevenue,
            'todaySales'    => $todaySales,
            'dailySalesChart' => $dailySalesChart,
            'monthlySalesChart' => $monthlySalesChart,
            'totalUsers'    => $totalUsers,
            'lowStockProducts' => $lowStockProducts,
            'todayHeaders'  => $todayHeaders,
            'todayDetails'  => $todayDetails,
        ]);
    }

    /**
     * Export laporan penjualan harian dalam format Excel
     */
    public function exportDaily(): void
    {
        allowOnly([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $transactionModel = new Transaction();
        $transactions     = $transactionModel->getToday();

        $rows = [];

        foreach ($transactions as $transaction) {
            $details = $transactionModel->getDetails((int) $transaction['id']);

            foreach ($details as $item) {
                $rows[] = [
                    'code' => $transaction['transaction_code'],
                    'cashier' => $transaction['cashier_name'] ?? '-',
                    'product' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'total' => $transaction['total_price'],
                    'paid' => $transaction['paid_amount'] ?? 0,
                    'change' => $transaction['change_amount'] ?? 0,
                    'created_at' => $transaction['created_at'],
                ];
            }
        }

        ExcelExporter::download(
            'laporan-penjualan-' . date('Y-m-d') . '.xlsx',
            'Laporan Penjualan Harian',
            [
                ['key' => 'code', 'label' => 'Kode Transaksi', 'width' => 20],
                ['key' => 'cashier', 'label' => 'Kasir', 'width' => 20],
                ['key' => 'product', 'label' => 'Produk', 'width' => 28],
                ['key' => 'quantity', 'label' => 'Qty', 'type' => 'integer', 'width' => 10],
                ['key' => 'subtotal', 'label' => 'Subtotal', 'type' => 'currency', 'width' => 18],
                ['key' => 'total', 'label' => 'Total Transaksi', 'type' => 'currency', 'width' => 20],
                ['key' => 'paid', 'label' => 'Uang Dibayar', 'type' => 'currency', 'width' => 18],
                ['key' => 'change', 'label' => 'Kembalian', 'type' => 'currency', 'width' => 18],
                ['key' => 'created_at', 'label' => 'Waktu', 'type' => 'datetime', 'width' => 20],
            ],
            $rows,
            ['Tanggal' => date('d/m/Y'), 'Dibuat pada' => date('d/m/Y H:i')]
        );
    }
}
