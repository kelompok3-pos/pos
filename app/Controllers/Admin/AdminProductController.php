<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';

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
    private Product $productModel;

    public function __construct()
    {
        requireRole('admin');
        $this->productModel = new Product();
    }

    /**
     * Tampilkan daftar semua produk
     */
    public function index(): void
    {
        $products = $this->productModel->getAll();

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

        $this->productModel->create([
            'name'        => $_POST['name'],
            'price'       => $_POST['price'],
            'stock'       => $_POST['stock'],
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

        $product = $this->productModel->getById($id);

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

        if (!$id) {
            flash('error', 'ID produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        $this->productModel->update($id, [
            'name'        => $_POST['name'],
            'price'       => $_POST['price'],
            'stock'       => $_POST['stock'],
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
        $id = $_GET['id'] ?? null;

        if (!$id) {
            flash('error', 'ID produk tidak ditemukan.');
            $this->redirect('/admin/product');
        }

        $this->productModel->delete($id);

        flash('success', 'Produk berhasil dihapus!');
        $this->redirect('/admin/product');
    }
}
