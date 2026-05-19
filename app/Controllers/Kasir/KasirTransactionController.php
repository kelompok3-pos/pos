<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';
require_once BASE_PATH . '/app/Models/Transaction.php';

/**
 * =================================================================
 * KASIR - TRANSACTION CONTROLLER
 * =================================================================
 * Controller untuk mengelola transaksi penjualan.
 *
 * Routes:
 *   GET  /kasir/transaction       → index() — Halaman transaksi
 *   POST /kasir/transaction/store → store() — Simpan transaksi
 * =================================================================
 */

class KasirTransactionController extends Controller
{
    private Product $productModel;
    private Transaction $transactionModel;

    public function __construct()
    {
        requireRole('kasir');
        $this->productModel     = new Product();
        $this->transactionModel = new Transaction();
    }

    /**
     * Tampilkan halaman transaksi
     */
    public function index(): void
    {
        $products     = $this->productModel->getAll();
        $transactions = $this->transactionModel->getToday();

        $this->view('kasir/transaction/index', [
            'title'        => 'Transaksi Penjualan',
            'products'     => $products,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Simpan transaksi baru
     */
    public function store(): void
    {
        verifyCsrf();

        $productId = $_POST['product_id'] ?? null;
        $quantity  = (int) ($_POST['quantity'] ?? 0);

        if (!$productId || $quantity <= 0) {
            flash('error', 'Data transaksi tidak valid.');
            $this->redirect('/kasir/transaction');
        }

        // Ambil data produk
        $product = $this->productModel->getById($productId);

        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        // Cek stok
        if ($product['stock'] < $quantity) {
            flash('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product['stock']);
            $this->redirect('/kasir/transaction');
        }

        // Hitung total
        $totalPrice = $product['price'] * $quantity;

        // Simpan transaksi
        $this->transactionModel->create([
            'product_id'   => $productId,
            'quantity'     => $quantity,
            'price'        => $product['price'],
            'total_price'  => $totalPrice,
        ]);

        // Kurangi stok
        $this->productModel->reduceStock($productId, $quantity);

        flash('success', 'Transaksi berhasil! Total: ' . formatRupiah($totalPrice));
        $this->redirect('/kasir/transaction');
    }
}
