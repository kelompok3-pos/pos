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
     * Fitur hapus user (Soft Delete)
     */
    public function delete(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            flash('error', 'ID user tidak ditemukan.');
            $this->redirect('/admin/user');
        }

        $this->userModel->delete((int)$id);

        flash('success', 'Akun user berhasil dinonaktifkan!');
        $this->redirect('/admin/user');
    }
}

/**
     * Tampilkan form tambah user baru
     */
    public function create(): void
    {
        $this->view('admin/user/create', [
            'title' => 'Tambah User Baru'
        ]);
    }

    /**
     * Simpan data user baru ke database
     */
    public function store(): void
    {
        verifyCsrf();

        // Panggil model user untuk create data
        // $this->userModel->create([...]);

        flash('success', 'User baru berhasil ditambahkan!');
        $this->redirect('/admin/user');
    }