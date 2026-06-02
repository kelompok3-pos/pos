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

        $totalProducts  = $productModel->count();
        $totalStock     = $productModel->totalStock();
        $todayRevenue   = $transactionModel->todayRevenue();
        $todaySales     = $transactionModel->todayCount();
        $totalUsers     = $userModel->count();
        $todayItemsSold = $transactionModel->todayItemsSold(); // zain
        $monthlyRevenue = $transactionModel->monthlyRevenue(); // zain


        //yransaksi header dan detail untuk transaksi hari ini
        $todayHeaders = $transactionModel->getToday();
        $todayDetails = [];
        foreach ($todayHeaders as $trx) {
            $todayDetails[$trx['id']] = $transactionModel->getDetails($trx['id']);
        }

        $this->view('home/index', [
            'title'          => 'Dashboard',
            'totalProducts'  => $totalProducts,
            'totalStock'     => $totalStock,
            'todayRevenue'   => $todayRevenue,
            'todaySales'     => $todaySales,
            'totalUsers'     => $totalUsers,
            'todayItemsSold' => $todayItemsSold,
            'monthlyRevenue' => $monthlyRevenue,
            'todayHeaders'   => $todayHeaders,
            'todayDetails'   => $todayDetails,
        ]);
    }
}