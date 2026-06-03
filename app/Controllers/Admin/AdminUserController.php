<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/User.php';

/**
 * =================================================================
 * ADMIN - USER CONTROLLER
 * =================================================================
 * Controller untuk memproses daftar akun kasir dan admin toko.
 * =================================================================
 */

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        requireRole('admin');
        $this->userModel = new User();
    }

    /**
     * Tampilkan data tabel user aktif
     */
    public function index(): void
    {
        $users = $this->userModel->getAll();

        $this->view('admin/user/index', [
            'title' => 'Manajemen User',
            'users' => $users,
        ]);
    }

    /**
     * Tampilkan form tambah user baru
     */
    public function create(): void
    {
        $this->view('admin/user/create', [
            'title' => 'Tambah User Baru',
            'canCreateAdmin' => isSuperAdmin(),
        ]);
    }

    /**
     * Simpan data user baru ke database
     */
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
        }

        $created = $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
        ]);

        if ($created) {
            flash('success', 'User baru berhasil ditambahkan!');
            $this->redirect('/admin/user');
        } else {
            flash('error', 'Email sudah terdaftar. Gunakan email lain.');
            $this->redirect('/admin/user/create');
        }
    }

    /**
     * Fitur hapus user (Soft Delete)
     */
    public function delete(): void
    {
        verifyCsrf();

        $id = $_POST['id'] ?? null;

        if (!$id) {
            flash('error', 'ID user tidak ditemukan.');
            $this->redirect('/admin/user');
        }

        $targetUser = $this->userModel->getById((int) $id);
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

        flash('success', 'Akun user berhasil dinonaktifkan!');
        $this->redirect('/admin/user');
    }
}
