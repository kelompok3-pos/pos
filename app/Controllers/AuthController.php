<?php

require_once __DIR__ . '/Controller.php';
require_once BASE_PATH . '/app/Models/User.php';

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

        $title = 'Login';
        require BASE_PATH . '/app/Views/auth/login.php';
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

        session_regenerate_id(true);
        regenerateCsrf();
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['name']     = $user['name'];
        $_SESSION['store_id'] = ($user['store_id'] ?? null) === null ? null : (int) $user['store_id'];

        // Compatibility for existing views while top-level session keys remain authoritative.
        $_SESSION['user'] = [
            'id'       => (int) $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'role'     => $user['role'],
            'store_id' => $_SESSION['store_id'],
        ];
        try {
            $actor = ActorContext::fromSession();
        } catch (UnauthorizedException) {
            flash('error', 'Akun atau toko tidak aktif.');
            $this->redirect('/login');
        }
        $this->userModel->touchLastLogin((int) $user['id']);
        try {
            AuditLogger::log($actor, 'LOGIN', 'users', (int) $user['id']);
        } catch (Throwable $exception) {
            error_log($exception->__toString());
        }

        flash('success', 'Selamat datang, ' . e($user['email']) . '!');
        $this->redirectBasedOnRole();
    }

    /**
     * Logout: hapus session user
     */
    public function logout(): void
    {
        verifyCsrf();
        try {
            $actor = ActorContext::fromSession();
            AuditLogger::log($actor, 'LOGOUT', 'users', $actor->user_id);
        } catch (Throwable $exception) {
            error_log($exception->__toString());
        }
        unset(
            $_SESSION['user_id'],
            $_SESSION['role'],
            $_SESSION['name'],
            $_SESSION['store_id'],
            $_SESSION['user'],
            $_SESSION['cart'],
            $_SESSION['last_transaction_id']
            ,$_SESSION['shift_id']
        );
        session_regenerate_id(true);
        flash('success', 'Anda berhasil logout.');
        $this->redirect('/login');
    }

    /**
     * Redirect berdasarkan role user yang login
     */
    private function redirectBasedOnRole(): void
    {
        $role = currentRole();
        $this->redirect(match ($role) {
            'kasir' => '/kasir/transaction',
            'super_admin', 'superadmin' => '/superadmin/dashboard',
            default => '/dashboard',
        });
    }
}
