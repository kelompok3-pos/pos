<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';

/**
 * =================================================================
 * KASIR - PRODUCT CONTROLLER
 * =================================================================
 * Controller untuk melihat daftar produk (read-only).
 * Kasir hanya bisa melihat, tidak bisa menambah/edit/hapus.
 *
 * Routes:
 *   GET /kasir/product → index() — List produk
 * =================================================================
 */

class KasirProductController extends Controller
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Tampilkan daftar produk (read-only untuk kasir)
     */
    public function index(): void
    {
        $products = $this->productModel->getAll();

        $this->view('kasir/product/index', [
            'title'    => 'Daftar Produk',
            'products' => $products,
        ]);
    }
}
