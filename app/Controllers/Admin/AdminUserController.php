<?php

/**
 * =================================================================
 * ADMIN USER CONTROLLER
 * =================================================================
 * Menangani semua aksi CRUD untuk halaman manajemen user.
 * Role: Admin
 *
 * Routes yang dilayani:
 * GET  /admin/user          → index()   — list semua user
 * GET  /admin/user/create   → create()  — form tambah user
 * POST /admin/user/store    → store()   — simpan user baru
 * GET  /admin/user/edit     → edit()    — form edit user (?id=X)
 * POST /admin/user/update   → update()  — simpan perubahan user
 * POST /admin/user/delete   → delete()  — soft delete user
 * =================================================================
 */

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/User.php'; // Hubungkan ke Models/User.php yang asli

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // ============================================================
    // INDEX — Daftar semua user
    // ============================================================
    public function index(): void
    {
        $users     = $this->userModel->getAll();
        $totalUser = $this->userModel->count(); // Disesuaikan dari countAll() menjadi count() sesuai model kelompok

        // Menghitung statistik role secara manual dari array agar aman tanpa utak-atik database bawaan tim
        $totalAdmin = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
        $totalKasir = count(array_filter($users, fn($u) => $u['role'] === 'kasir'));

        $this->view('admin/user/index', [
            'title'      => 'Manajemen User',
            'users'      => $users,
            'totalUser'  => $totalUser,
            'totalAdmin' => $totalAdmin,
            'totalKasir' => $totalKasir,
        ]);
    }

    // ============================================================
    // CREATE — Form tambah user
    // ============================================================
    public function create(): void
    {
        // Fungsi ini bertugas memanggil tampilan form tambah user di folder views
        $this->view('admin/user/create', [
            'title' => 'Tambah User',
        ]);
    }

    // ============================================================
    // STORE — Simpan user baru
    // ============================================================
    public function store(): void
    {
        verifyCsrf();

        // Disesuaikan dari getByEmail() menjadi findByEmail() sesuai model kelompok
        if ($this->userModel->findByEmail($_POST['email'])) {
            keepOldInput();
            flash('error', 'Email sudah digunakan oleh user lain.');
            $this->redirect('/admin/user/create');
            return;
        }

        $this->userModel->create([
            'name'     => $_POST['name'],
            'email'    => $_POST['email'],
            'password' => $_POST['password'],
            'role'     => $_POST['role'],
        ]);

        flash('success', 'User berhasil ditambahkan!');
        $this->redirect('/admin/user');
    }

    // ============================================================
    // EDIT — Form edit user
    // ============================================================
    public function edit(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            flash('error', 'User tidak ditemukan.');
            $this->redirect('/admin/user');
            return;
        }

        $this->view('admin/user/edit', [
            'title' => 'Edit User',
            'user'  => $user,
        ]);
    }

    // ============================================================
    // UPDATE — Simpan perubahan user
    // ============================================================
    public function update(): void
    {
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        // Model kelompok memisahkan update data biasa dengan password
        $this->userModel->update($id, [
            'email' => $_POST['email'],
            'role'  => $_POST['role'],
        ]);

        // Jika password baru diisi, jalankan fungsi update password terpisah
        if (!empty($_POST['password'])) {
            $this->userModel->updatePassword($id, $_POST['password']);
        }

        flash('success', 'Data user berhasil diperbarui!');
        $this->redirect('/admin/user');
    }

    // ============================================================
    // DELETE — Soft delete user
    // ============================================================
    public function delete(): void
    {
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $this->userModel->delete($id);

        flash('success', 'User berhasil dihapus.');
        $this->redirect('/admin/user');
    }
}