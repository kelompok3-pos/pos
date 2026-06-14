<?php

require_once __DIR__ . '/../Controller.php';

/**
 * =================================================================
 * ADMIN - PRODUCT CONTROLLER
 * =================================================================
 * Controller untuk mengelola produk (CRUD lengkap).
 * Hanya untuk role Admin.
 *
 * Routes yang terdaftar:
 *   GET  /admin/product          → index()   — List semua produk
 *   GET  /admin/product/create   → create()  — Form tambah produk
 *   POST /admin/product/store    → store()   — Simpan produk baru
 *   GET  /admin/product/edit     → edit()    — Form edit produk
 *   POST /admin/product/update   → update()  — Update produk
 *   GET  /admin/product/delete   → delete()  — Hapus produk
 * =================================================================
 */

class AdminProductController extends Controller
{
    private ProductRepository $products;

    public function __construct()
    {
        allowOnly([ROLE_ADMIN]);
        $this->products = new ProductRepository(getConnection(), ActorContext::fromSession());
    }

    /**
     * Tampilkan daftar semua produk
     */
    public function index(): void
    {
        $products = $this->products->management();

        $this->view('admin/product/index', [
            'title'    => 'Daftar Produk',
            'products' => $products,
        ]);
    }

    /**
     * Tampilkan form tambah produk
     */
    public function create(): void
    {
        $this->view('admin/product/create', [
            'title' => 'Tambah Produk',
        ]);
    }

    /**
     * Simpan produk baru ke database
     */
    public function store(): void
    {
        verifyCsrf();
        keepOldInput();

        $name  = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? -1);
        $stock = (int) ($_POST['stock'] ?? -1);
        $minimumStock = (int) ($_POST['minimum_stock'] ?? 5);

        if ($name === '' || $price < 0 || $stock < 0) {
            flash('error', 'Nama, harga, dan stok produk wajib diisi dengan benar.');
            $this->redirect('/admin/product/create');
        }

        $this->products->insert([
            'name'        => $name,
            'price'       => $price,
            'stock'       => $stock,
            'minimum_stock' => $minimumStock,
            'description' => $_POST['description'] ?? '',
        ]);

        flash('success', 'Produk berhasil ditambahkan!');
        $this->redirect('/admin/product');
    }

    /**
     * Tampilkan form edit produk
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            flash('error', 'ID produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        try {
            assertBelongsToStore('products', (int) $id, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }
        $product = $this->products->findActiveById((int) $id);

        if (!$product) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        $this->view('admin/product/edit', [
            'title'   => 'Edit Produk',
            'product' => $product,
        ]);
    }

    /**
     * Update produk di database
     */
    public function update(): void
    {
        verifyCsrf();

        $id = $_POST['id'] ?? null;
        $name  = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? -1);
        $stock = (int) ($_POST['stock'] ?? -1);
        $minimumStock = (int) ($_POST['minimum_stock'] ?? 5);

        if (!$id) {
            flash('error', 'ID produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }
        try {
            assertBelongsToStore('products', (int) $id, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        if ($name === '' || $price < 0 || $stock < 0) {
            flash('error', 'Nama, harga, dan stok produk wajib diisi dengan benar.');
            $this->redirect('/admin/product/edit?id=' . $id);
        }

        $this->products->update((int) $id, [
            'name'        => $name,
            'price'       => $price,
            'stock'       => $stock,
            'minimum_stock' => $minimumStock,
            'description' => $_POST['description'] ?? '',
        ]);

        flash('success', 'Produk berhasil diperbarui!');
        $this->redirect('/admin/product');
    }

    /**
     * Hapus produk dari database
     */
    public function delete(): void
    {
        verifyCsrf();

        $id = $_POST['id'] ?? null;

        if (!$id) {
            flash('error', 'ID produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }
        try {
            assertBelongsToStore('products', (int) $id, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        $this->products->softDelete((int) $id);

        flash('success', 'Produk berhasil dihapus!');
        $this->redirect('/admin/product');
    }

    public function status(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        try {
            assertBelongsToStore('products', $id, ActorContext::fromSession()->requireStoreId());
        } catch (UnauthorizedException) {
            flash('error', 'Produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }
        if (!in_array($status, ['active', 'inactive'], true)) {
            flash('error', 'Status produk tidak valid.');
        } else {
            $this->products->update($id, ['status' => $status]);
            flash('success', 'Status produk diperbarui.');
        }
        $this->redirect('/admin/product');
    }

    public function import(): void
    {
        verifyCsrf();
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Pilih file CSV yang valid.');
            $this->redirect('/admin/product');
        }
        $handle = fopen($_FILES['csv']['tmp_name'], 'r');
        $header = fgetcsv($handle);
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if (!$data || empty($data['name'])) {
                continue;
            }
            $this->products->insert([
                'name' => $data['name'],
                'price' => (float) ($data['price'] ?? 0),
                'stock' => (int) ($data['stock'] ?? 0),
                'minimum_stock' => (int) ($data['minimum_stock'] ?? 5),
                'description' => $data['description'] ?? '',
            ]);
            $count++;
        }
        fclose($handle);
        flash('success', "{$count} produk berhasil diimpor.");
        $this->redirect('/admin/product');
    }
}
