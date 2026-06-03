<?php

require_once __DIR__ . '/Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';
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
            $this->redirect('/dashboard');
        }

        $title = 'POS App';
        require BASE_PATH . '/app/Views/home/landing.php';
    }

    public function index(): void
    {
        requireAuth();

        $productModel     = new Product();
        $transactionModel = new Transaction();
        $userModel        = new User();

        $totalProducts = $productModel->count();
        $totalStock    = $productModel->totalStock();
        $todayRevenue  = $transactionModel->todayRevenue();
        $todaySales    = $transactionModel->todayCount();
        $todayItemsSold = $transactionModel->todayItemsSold();
        $todayPaidAmount = $transactionModel->todayPaidAmount();
        $todayChangeAmount = $transactionModel->todayChangeAmount();
        $todayTopProducts = $transactionModel->todayTopProducts();
        $dailySalesChart = $transactionModel->getDailySalesChartData(7);
        $monthlySalesChart = $transactionModel->getSalesChartData();
        $topSellingProducts = $transactionModel->getTopSellingProducts();
        $totalUsers    = $userModel->count();
        $lowStockProducts = $productModel->getLowStock();

        // Ambil transaksi header hari ini + detail-nya
        $todayHeaders = $transactionModel->getToday();
        $todayDetails = [];
        foreach ($todayHeaders as $trx) {
            $todayDetails[$trx['id']] = $transactionModel->getDetails($trx['id']);
        }

        $this->view('home/index', [
            'title'        => 'Dashboard',
            'totalProducts' => $totalProducts,
            'totalStock'    => $totalStock,
            'todayRevenue'  => $todayRevenue,
            'todaySales'    => $todaySales,
            'todayItemsSold' => $todayItemsSold,
            'todayPaidAmount' => $todayPaidAmount,
            'todayChangeAmount' => $todayChangeAmount,
            'todayTopProducts' => $todayTopProducts,
            'dailySalesChart' => $dailySalesChart,
            'monthlySalesChart' => $monthlySalesChart,
            'topSellingProducts' => $topSellingProducts,
            'totalUsers'    => $totalUsers,
            'lowStockProducts' => $lowStockProducts,
            'todayHeaders'  => $todayHeaders,
            'todayDetails'  => $todayDetails,
        ]);
    }

    /**
     * Export laporan penjualan harian dalam format CSV
     */
    public function exportDaily(): void
    {
        requireAuth();

        $transactionModel = new Transaction();
        $transactions     = $transactionModel->getToday();

        $filename = 'laporan-penjualan-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Kode Transaksi',
            'Kasir',
            'Produk',
            'Qty',
            'Subtotal',
            'Total Transaksi',
            'Uang Dibayar',
            'Kembalian',
            'Waktu',
        ]);

        foreach ($transactions as $transaction) {
            $details = $transactionModel->getDetails((int) $transaction['id']);

            foreach ($details as $item) {
                fputcsv($output, [
                    $transaction['transaction_code'],
                    $transaction['cashier_name'] ?? '-',
                    $item['product_name'],
                    $item['quantity'],
                    $item['subtotal'],
                    $transaction['total_price'],
                    $transaction['paid_amount'] ?? 0,
                    $transaction['change_amount'] ?? 0,
                    $transaction['created_at'],
                ]);
            }
        }

        fclose($output);
        exit;
    }
}
