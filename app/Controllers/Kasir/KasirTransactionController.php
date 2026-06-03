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
 *   GET  /kasir/transaction        → index()  — Halaman transaksi (list + keranjang)
 *   POST /kasir/transaction/add    → add()    — Tambah item ke keranjang
 *   POST /kasir/transaction/remove → remove() — Hapus item dari keranjang
 *   POST /kasir/transaction/clear  → clear()  — Kosongkan keranjang
 *   POST /kasir/transaction/checkout → checkout() — Simpan transaksi
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
     * Tampilkan halaman transaksi (list + keranjang dari session)
     */
    public function index(): void
    {
        $products     = $this->productModel->getAll();
        $transactions = $this->transactionModel->getToday();

        // Ambil keranjang dari session
        $cart = $_SESSION['cart'] ?? [];

        // Ambil detail untuk setiap transaksi
        $transactionDetails = [];
        foreach ($transactions as $trx) {
            $transactionDetails[$trx['id']] = $this->transactionModel->getDetails($trx['id']);
        }

        $this->view('kasir/transaction/index', [
            'title'               => 'Transaksi Penjualan',
            'products'            => $products,
            'transactions'        => $transactions,
            'cart'                => $cart,
            'transactionDetails' => $transactionDetails,
        ]);
    }

    /**
     * Tambah item ke keranjang
     */
    public function add(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = (int) ($_POST['quantity'] ?? 1);

        if ($productId <= 0 || $quantity <= 0) {
            flash('error', 'Data tidak valid.');
            $this->redirect('/kasir/transaction');
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        if ($product['stock'] < $quantity) {
            flash('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product['stock']);
            $this->redirect('/kasir/transaction');
        }

        // Ambil keranjang yang sudah ada
        $cart = $_SESSION['cart'] ?? [];

        // Jika produk sudah ada di keranjang, tambahkan quantity
        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['quantity'] + $quantity;

            if ($product['stock'] < $newQty) {
                flash('error', 'Stok tidak mencukupi untuk jumlah total!');
                $this->redirect('/kasir/transaction');
            }

            $cart[$productId]['quantity'] = $newQty;
            $cart[$productId]['subtotal']  = $product['price'] * $newQty;
        } else {
            // Produk baru, tambahkan ke keranjang
            $cart[$productId] = [
                'product_id' => $product['id'],
                'name'       => $product['name'],
                'price'      => (float) $product['price'],
                'quantity'   => $quantity,
                'subtotal'   => $product['price'] * $quantity,
            ];
        }

        $_SESSION['cart'] = $cart;

        flash('success', 'Item ditambahkan ke keranjang.');
        $this->redirect('/kasir/transaction');
    }

    /**
     * Hapus item dari keranjang
     */
    public function remove(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            flash('success', 'Item dihapus dari keranjang.');
        }

        $this->redirect('/kasir/transaction');
    }

    /**
     * Kosongkan seluruh keranjang
     */
    public function clear(): void
    {
        verifyCsrf();
        unset($_SESSION['cart']);
        flash('success', 'Keranjang dikosongkan.');
        $this->redirect('/kasir/transaction');
    }

    /**
     * Simpan transaksi (checkout)
     */
    public function checkout(): void
    {
        verifyCsrf();

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            flash('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
            $this->redirect('/kasir/transaction');
        }

        $kasirId = (int) $_SESSION['user']['id'];
        $total = array_reduce($cart, fn($sum, $item) => $sum + $item['subtotal'], 0);
        $paidAmount = (float) ($_POST['paid_amount'] ?? 0);

        if ($paidAmount < $total) {
            flash('error', 'Uang dibayar kurang. Total belanja: ' . formatRupiah($total));
            $this->redirect('/kasir/transaction');
        }

        // Konversi cart ke format items
        $items = array_map(fn($item) => [
            'product_id' => $item['product_id'],
            'name'       => $item['name'],
            'price'      => $item['price'],
            'quantity'   => $item['quantity'],
            'subtotal'   => $item['subtotal'],
        ], $cart);

        // Simpan transaksi dan update stok dalam satu database transaction
        $transactionId = $this->transactionModel->create($kasirId, $items, $paidAmount);

        if ($transactionId) {
            // Bersihkan keranjang
            unset($_SESSION['cart']);
            $_SESSION['last_transaction_id'] = $transactionId;

            flash(
                'success',
                'Transaksi berhasil! Total: ' . formatRupiah($total)
                . ' | Bayar: ' . formatRupiah($paidAmount)
                . ' | Kembalian: ' . formatRupiah($paidAmount - $total)
            );
        } else {
            flash('error', 'Transaksi gagal. Periksa stok produk dan nominal pembayaran.');
        }

        $this->redirect('/kasir/transaction');
    }

    /**
     * Update quantity item di keranjang
     */
    public function update(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = (int) ($_POST['quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0 || !isset($_SESSION['cart'][$productId])) {
            flash('error', 'Data keranjang tidak valid.');
            $this->redirect('/kasir/transaction');
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            unset($_SESSION['cart'][$productId]);
            flash('error', 'Produk tidak ditemukan atau sudah tidak aktif.');
            $this->redirect('/kasir/transaction');
        }

        if ((int) $product['stock'] < $quantity) {
            flash('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product['stock']);
            $this->redirect('/kasir/transaction');
        }

        $_SESSION['cart'][$productId]['quantity'] = $quantity;
        $_SESSION['cart'][$productId]['subtotal'] = (float) $product['price'] * $quantity;

        flash('success', 'Jumlah item berhasil diperbarui.');
        $this->redirect('/kasir/transaction');
    }

    /**
     * Tampilkan struk transaksi untuk dicetak
     */
    public function receipt(): void
    {
        $id = (int) ($_GET['id'] ?? ($_SESSION['last_transaction_id'] ?? 0));

        if ($id <= 0) {
            flash('error', 'ID transaksi tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        $transaction = $this->transactionModel->getById($id);

        if (!$transaction) {
            flash('error', 'Transaksi tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        $details = $this->transactionModel->getDetails($id);

        $this->view('kasir/transaction/receipt', [
            'title'       => 'Struk Transaksi',
            'transaction' => $transaction,
            'details'     => $details,
        ]);
    }
}
