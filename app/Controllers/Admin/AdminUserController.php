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
        requireRole('admin');
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
            'title' => 'Tambah User Baru',
            'canCreateAdmin' => isSuperAdmin(),
        ]);
    }

    // ============================================================
    // STORE — Simpan user baru
    // ============================================================
    public function store(): void
    {
        verifyCsrf();
        keepOldInput();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'kasir';
        $allowedRoles = isSuperAdmin() ? ['admin', 'kasir'] : ['kasir'];

        if (empty($name) || empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Nama, email, dan password wajib diisi dengan benar.');
            $this->redirect('/admin/user/create');
        }

        if (strlen($password) < 8) {
            flash('error', 'Password minimal 8 karakter.');
            $this->redirect('/admin/user/create');
        }

        if (!in_array($role, $allowedRoles, true)) {
            flash('error', 'Anda tidak memiliki izin membuat akun dengan role tersebut.');
            $this->redirect('/admin/user/create');
            return;
        }

        $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
        ]);

        flash('success', 'User berhasil ditambahkan!');
        $this->redirect('/admin/user');
    }

    // ============================================================
    // EDIT — Form edit user
    // ============================================================
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            flash('error', 'User tidak ditemukan.');
            $this->redirect('/admin/user');
            return;
        }

        if ($user['role'] === 'super_admin') {
            flash('error', 'Akun super admin tidak dapat diedit dari aplikasi.');
            $this->redirect('/admin/user');
        }

        if ($user['role'] === 'admin' && !isSuperAdmin()) {
            flash('error', 'Hanya super admin yang dapat mengedit akun admin.');
            $this->redirect('/admin/user');
        }

        $this->view('admin/user/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'canAssignAdmin' => isSuperAdmin(),
        ]);
    }

    // ============================================================
    // UPDATE — Simpan perubahan user
    // ============================================================
    public function update(): void
    {
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $targetUser = $this->userModel->getById($id);

        if (!$targetUser) {
            flash('error', 'User tidak ditemukan atau sudah dinonaktifkan.');
            $this->redirect('/admin/user');
        }

        if ($targetUser['role'] === 'super_admin') {
            flash('error', 'Akun super admin tidak dapat diedit dari aplikasi.');
            $this->redirect('/admin/user');
        }

        if ($targetUser['role'] === 'admin' && !isSuperAdmin()) {
            flash('error', 'Hanya super admin yang dapat mengedit akun admin.');
            $this->redirect('/admin/user');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? $targetUser['role'];
        $allowedRoles = isSuperAdmin() ? ['admin', 'kasir'] : [$targetUser['role']];

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Nama dan email wajib diisi dengan benar.');
            $this->redirect('/admin/user/edit?id=' . $id);
        }

        if (!in_array($role, $allowedRoles, true)) {
            flash('error', 'Anda tidak memiliki izin mengubah role tersebut.');
            $this->redirect('/admin/user/edit?id=' . $id);
        }

        if ($targetUser['role'] === 'admin' && $role !== 'admin') {
            $activeAdminCount = $this->userModel->countByRoles(['admin', 'super_admin']);

            if ($activeAdminCount <= 1) {
                flash('error', 'Minimal harus ada satu akun admin aktif.');
                $this->redirect('/admin/user/edit?id=' . $id);
            }
        }

        if (!empty($_POST['password']) && strlen($_POST['password']) < 8) {
            flash('error', 'Password minimal 8 karakter.');
            $this->redirect('/admin/user/edit?id=' . $id);
        }

        $this->userModel->update($id, [
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ]);

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
        $targetUser = $this->userModel->getById($id);
        $currentUserId = (int) ($_SESSION['user']['id'] ?? 0);

        if (!$targetUser) {
            flash('error', 'User tidak ditemukan atau sudah dinonaktifkan.');
            $this->redirect('/admin/user');
        }

        if ((int) $targetUser['id'] === $currentUserId) {
            flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            $this->redirect('/admin/user');
        }

        if ($targetUser['role'] === 'super_admin') {
            flash('error', 'Akun super admin tidak dapat dinonaktifkan dari aplikasi.');
            $this->redirect('/admin/user');
        }

        if ($targetUser['role'] === 'admin' && !isSuperAdmin()) {
            flash('error', 'Hanya super admin yang dapat menonaktifkan akun admin.');
            $this->redirect('/admin/user');
        }

        if (in_array($targetUser['role'], ['admin', 'super_admin'], true)) {
            $activeAdminCount = $this->userModel->countByRoles(['admin', 'super_admin']);

            if ($activeAdminCount <= 1) {
                flash('error', 'Minimal harus ada satu akun admin aktif.');
                $this->redirect('/admin/user');
            }
        }

        $this->userModel->delete((int) $targetUser['id']);

        flash('success', 'User berhasil dihapus.');
        $this->redirect('/admin/user');
    }
}
