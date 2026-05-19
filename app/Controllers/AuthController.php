<?php

require_once __DIR__ . '/Controller.php';
require_once BASE_PATH . '/app/Models/User.php';

/**
 * =================================================================
 * AUTH CONTROLLER
 * =================================================================
 * Controller untuk menangani autentikasi pengguna (login & logout).
 *
 * Routes:
 *   GET  /login                → loginForm()  — Tampilkan form login
 *   POST /login/authenticate   → login()      — Proses login
 *   GET  /logout               → logout()     — Logout
 * =================================================================
 */

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Tampilkan halaman form login
     */
    public function loginForm(): void
    {
        if (isAuthenticated()) {
            $this->redirectBasedOnRole();
        }

        $this->view('auth/login', [
            'title' => 'Login',
        ]);
    }

    /**
     * Proses login: validasi email & password
     */
    public function login(): void
    {
        verifyCsrf();
        keepOldInput();

        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('error', 'Email dan password wajib diisi.');
            $this->redirect('/login');
        }

        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            flash('error', 'Email atau password salah.');
            $this->redirect('/login');
        }

        $_SESSION['user'] = $user;

        flash('success', 'Selamat datang, ' . e($user['email']) . '!');
        $this->redirectBasedOnRole();
    }

    /**
     * Logout: hapus session user
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        flash('success', 'Anda berhasil logout.');
        $this->redirect('/login');
    }

    /**
     * Redirect berdasarkan role user yang login
     */
    private function redirectBasedOnRole(): void
    {
        $role = $_SESSION['user']['role'] ?? 'kasir';

        if ($role === 'admin') {
            $this->redirect('/admin/product');
        } else {
            $this->redirect('/kasir/product');
        }
    }
}
