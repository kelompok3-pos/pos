<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';
require_once BASE_PATH . '/app/Models/Transaction.php';
require_once BASE_PATH . '/app/Models/Setting.php';

/**
 * =================================================================
 * KASIR - TRANSACTION CONTROLLER
 * =================================================================
 * Controller untuk mengelola transaksi penjualan.
 *
 * Routes:
 *   GET  /kasir/transaction              → index()          — Halaman POS (product grid + keranjang)
 *   POST /kasir/transaction/add          → add()            — Tambah item ke keranjang via product_id
 *   POST /kasir/transaction/update       → update()         — Update quantity item di keranjang
 *   POST /kasir/transaction/remove       → remove()         — Hapus item dari keranjang
 *   POST /kasir/transaction/clear        → clear()          — Kosongkan keranjang
 *   POST /kasir/transaction/checkout     → checkout()       — Simpan & selesaikan transaksi
 *   GET  /kasir/transaction/receipt      → receipt()        — Tampilkan struk
 *   GET  /kasir/my-transactions          → myTransactions() — Riwayat transaksi kasir
 * =================================================================
 */
class KasirTransactionController extends Controller
{
    private Product $productModel;
    private Transaction $transactionModel;

    public function __construct()
    {
        allowOnly(['admin', 'kasir']);
        $this->productModel     = new Product();
        $this->transactionModel = new Transaction();
    }

    // =========================================================
    // INDEX — Halaman POS utama
    // =========================================================

    public function index(): void
    {
        $settings = (new Setting())->all();

        // Ambil semua produk aktif yang stoknya > 0 untuk ditampilkan di grid
        $products = $this->productModel->getAll();

        // Riwayat transaksi hari ini (untuk panel samping jika diperlukan)
        $transactions = isRole('kasir')
            ? $this->transactionModel->getByCashier((int) $_SESSION['user_id'], date('Y-m-d'))
            : $this->transactionModel->getToday();

        // Keranjang dari session — ubah ke indexed array agar mudah di-loop di view
        $cart = array_values($_SESSION['cart'] ?? []);

        $transactionDetails = [];
        foreach ($transactions as $trx) {
            $transactionDetails[$trx['id']] = $this->transactionModel->getDetails($trx['id']);
        }

        $this->view('kasir/transaction/index', [
            'title'              => 'POS Kasir',
            'products'           => $products,
            'transactions'       => $transactions,
            'cart'               => $cart,
            'transactionDetails' => $transactionDetails,
            'settings'           => $settings,
        ]);
    }

    // =========================================================
    // ADD — Tambah produk ke keranjang via product_id
    // =========================================================

    public function add(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            flash('error', 'Produk tidak valid.');
            $this->redirect('/kasir/transaction');
        }
        try {
            assertBelongsToStore('products', $productId, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/kasir/transaction');
        }

        if ((int) $product['stock'] < $quantity) {
            flash('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product['stock']);
            $this->redirect('/kasir/transaction');
        }

        $cart = $_SESSION['cart'] ?? [];

        if (isset($cart[$productId])) {
            // Produk sudah di keranjang — tambah qty
            $newQty = $cart[$productId]['quantity'] + $quantity;

            if ((int) $product['stock'] < $newQty) {
                flash('error', 'Stok tidak mencukupi untuk jumlah total! Stok tersedia: ' . $product['stock']);
                $this->redirect('/kasir/transaction');
            }

            $cart[$productId]['quantity'] = $newQty;
            $cart[$productId]['subtotal']  = (float) $product['price'] * $newQty;
        } else {
            // Produk baru
            $cart[$productId] = [
                'product_id' => $product['id'],
                'name'       => $product['name'],
                'price'      => (float) $product['price'],
                'quantity'   => $quantity,
                'subtotal'   => (float) $product['price'] * $quantity,
            ];
        }

        $_SESSION['cart'] = $cart;

        $this->redirect('/kasir/transaction');
    }

    // =========================================================
    // UPDATE — Update quantity item di keranjang
    // =========================================================

    public function update(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = (int) ($_POST['quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            flash('error', 'Data keranjang tidak valid.');
            $this->redirect('/kasir/transaction');
        }

        if (!isset($_SESSION['cart'][$productId])) {
            flash('error', 'Produk tidak ada di keranjang.');
            $this->redirect('/kasir/transaction');
        }
        try {
            assertBelongsToStore('products', $productId, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            unset($_SESSION['cart'][$productId]);
            flash('error', 'Produk tidak ditemukan.');
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
        $_SESSION['cart'][$productId]['subtotal']  = (float) $product['price'] * $quantity;

        $this->redirect('/kasir/transaction');
    }

    // =========================================================
    // REMOVE — Hapus satu item dari keranjang
    // =========================================================

    public function remove(): void
    {
        verifyCsrf();

        $productId = (int) ($_POST['product_id'] ?? 0);

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        $this->redirect('/kasir/transaction');
    }

    // =========================================================
    // CLEAR — Kosongkan seluruh keranjang
    // =========================================================

    public function clear(): void
    {
        verifyCsrf();
        unset($_SESSION['cart']);
        $this->redirect('/kasir/transaction');
    }

    // =========================================================
    // CHECKOUT — Simpan transaksi & bersihkan keranjang
    // =========================================================

    public function checkout(): void
    {
        verifyCsrf();

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            flash('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
            $this->redirect('/kasir/transaction');
        }
        $actor = ActorContext::fromSession();
        foreach ($cart as $item) {
            try {
                assertBelongsToStore('products', (int) ($item['product_id'] ?? 0), $actor->requireStoreId());
            } catch (UnauthorizedException) {
                flash('error', 'Keranjang berisi produk yang tidak valid untuk toko ini.');
                $this->redirect('/kasir/transaction');
            }
        }

        $kasirId       = (int) $_SESSION['user_id'];
        $settings      = (new Setting())->all();
        $taxPercentage = (float) ($settings['tax_percentage'] ?? 0);

        $subtotal  = array_reduce($cart, fn($sum, $item) => $sum + (float) $item['subtotal'], 0.0);
        $taxAmount = round($subtotal * $taxPercentage / 100, 2);
        $total     = $subtotal + $taxAmount;

        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        if (!in_array($paymentMethod, ['cash', 'qris', 'card'], true)) {
            $paymentMethod = 'cash';
        }

        // Non-cash: anggap lunas penuh
        $paidAmount = $paymentMethod !== 'cash'
            ? $total
            : (float) ($_POST['paid_amount'] ?? 0);

        if ($paidAmount < $total) {
            flash('error', 'Uang dibayar kurang. Total belanja: ' . formatRupiah($total));
            $this->redirect('/kasir/transaction');
        }

        $items = array_map(fn($item) => [
            'product_id' => $item['product_id'],
            'name'       => $item['name'],
            'price'      => $item['price'],
            'quantity'   => $item['quantity'],
            'subtotal'   => $item['subtotal'],
        ], $cart);

        $transactionId = $this->transactionModel->create(
            $kasirId,
            $items,
            $paidAmount,
            $taxPercentage,
            $paymentMethod
        );

        if ($transactionId) {
            unset($_SESSION['cart']);
            $_SESSION['last_transaction_id'] = $transactionId;

            $change = $paidAmount - $total;
            flash(
                'success',
                'Transaksi berhasil! Total: ' . formatRupiah($total)
                    . ' | Bayar: ' . formatRupiah($paidAmount)
                    . ' | Kembalian: ' . formatRupiah($change)
            );
        } else {
            flash('error', 'Transaksi gagal. Periksa stok produk dan coba lagi.');
        }

        $this->redirect('/kasir/transaction');
    }

    // =========================================================
    // RECEIPT — Tampilkan struk transaksi
    // =========================================================

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

        // Kasir hanya boleh lihat struk milik sendiri
        if (isRole('kasir') && (int) $transaction['cashier_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(403);
            die('Akses ditolak.');
        }

        $details = $this->transactionModel->getDetails($id);

        $this->view('kasir/transaction/receipt', [
            'title'       => 'Struk Transaksi',
            'transaction' => $transaction,
            'details'     => $details,
            'settings'    => (new Setting())->all(),
        ]);
    }

    // =========================================================
    // MY TRANSACTIONS — Riwayat transaksi kasir yang login
    // =========================================================

    public function myTransactions(): void
    {
        allowOnly(['kasir']);

        $date         = trim($_GET['date'] ?? '');
        $transactions = $this->transactionModel->getByCashier((int) $_SESSION['user_id'], $date);

        $details = [];
        foreach ($transactions as $transaction) {
            $details[$transaction['id']] = $this->transactionModel->getDetails((int) $transaction['id']);
        }

        $this->view('kasir/transaction/history', [
            'title'              => 'Transaksi Saya',
            'date'               => $date,
            'transactions'       => $transactions,
            'transactionDetails' => $details,
        ]);
    }
}
