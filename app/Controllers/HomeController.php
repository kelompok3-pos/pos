<?php

require_once __DIR__ . '/Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';

/**
 * =================================================================
 * HOME CONTROLLER
 * =================================================================
 * Controller untuk halaman utama / dashboard.
 * =================================================================
 */

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();

        // Hitung total produk dan total stok untuk dashboard
        $totalProducts = $productModel->count();
        $totalStock    = $productModel->totalStock();

        $this->view('home/index', [
            'title'         => 'Dashboard',
            'totalProducts' => $totalProducts,
            'totalStock'    => $totalStock,
        ]);
    }
}
