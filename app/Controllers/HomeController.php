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
    public function index(): void
    {
        requireAuth();

        $productModel     = new Product();
        $transactionModel = new Transaction();
        $userModel        = new User();

        // Tarik data ringkasan statistik dari model-model terkait
        $totalProducts = $productModel->count();
        $totalStock    = $productModel->totalStock();
        $todayRevenue  = $transactionModel->todayRevenue();
        $todaySales    = $transactionModel->todayCount();
        $totalUsers    = $userModel->count();
        $recentTrx     = $transactionModel->getToday(); // Ambil transaksi hari ini

        $this->view('home/index', [
            'title'         => 'Dashboard Analisis',
            'totalProducts' => $totalProducts,
            'totalStock'    => $totalStock,
            'todayRevenue'  => $todayRevenue,
            'todaySales'    => $todaySales,
            'totalUsers'    => $totalUsers,
            'recentTrx'     => $recentTrx
        ]);
    }
}