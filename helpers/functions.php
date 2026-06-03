<?php

/**
 * =================================================================
 * HELPER FUNCTIONS
 * =================================================================
 * Kumpulan fungsi bantuan yang bisa dipakai di mana saja.
 * File ini di-load otomatis melalui public/index.php.
 * =================================================================
 */

// ============================================================
// URL & REDIRECT
// ============================================================

/**
 * Redirect ke URL tertentu
 * Selalu gunakan full URL (scheme://host/path) agar kompatibel
 * dengan setup subfolder (XAMPP) maupun root (Docker)
 *
 * Contoh: redirect('/admin/product')
 */
function redirect(string $url): void
{
    $base = env('APP_URL', '');
    $base = rtrim($base, '/');

    if (str_starts_with($url, '/')) {
        // Relative path — prepend APP_URL
        $fullUrl = $base . $url;
    } else {
        $fullUrl = $url;
    }

    header("Location: {$fullUrl}");
    exit;
}

/**
 * Generate full URL untuk form action, link, redirect
 * Include APP_URL dari .env agar kompatibel dengan subfolder
 *
 * Contoh:
 *   url('/login/authenticate')    → http://localhost/pos/public/login/authenticate
 *   url('/admin/product/store')   → http://localhost/pos/public/admin/product/store
 */
function url(string $path): string
{
    $base = rtrim(env('APP_URL', ''), '/');

    if ($base === '') {
        return $path;
    }

    return $base . '/' . ltrim($path, '/');
}

/**
 * Generate URL lengkap untuk asset (CSS, JS, gambar)
 * Include base path dari .env agar kompatibel dengan subfolder
 *
 * Contoh: asset('css/style.css') → /POS/public/assets/css/style.css
 */
function asset(string $path): string
{
    $base = rtrim(env('APP_URL', ''), '/');

    if ($base) {
        return $base . '/assets/' . ltrim($path, '/');
    }

    return '/assets/' . ltrim($path, '/');
}

/**
 * Base URL untuk internal links (navbar, redirect, form action)
 * Mengembalikan APP_URL dari .env
 *
 * Contoh: base_url() → 'http://localhost/POS/public'
 *         base_url('/admin/product') → 'http://localhost/POS/public/admin/product'
 */
function base_url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');

    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

// ============================================================
// FLASH MESSAGES
// ============================================================

/**
 * Simpan flash message ke session
 *
 * Contoh: flash('success', 'Data berhasil disimpan!')
 *         flash('error', 'Terjadi kesalahan!')
 */
function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Ambil flash message (otomatis terhapus setelah diambil)
 *
 * Contoh di View:
 *   <?php if ($msg = getFlash('success')): ?>
 *       <div class="alert alert-success"><?= $msg ?></div>
 *   <?php endif; ?>
 */
function getFlash(string $key): ?string
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

// ============================================================
// FORM HELPERS
// ============================================================

/**
 * Ambil nilai input sebelumnya (setelah validasi gagal)
 *
 * Contoh di View:
 *   <input name="name" value="<?= old('name') ?>">
 */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['old'][$key] ?? $default;
    unset($_SESSION['old'][$key]);
    return htmlspecialchars($value);
}

/**
 * Simpan semua input POST ke session (untuk fungsi old())
 */
function keepOldInput(): void
{
    $_SESSION['old'] = array_diff_key($_POST, array_flip(['password', 'csrf_token']));
}

/**
 * Generate hidden CSRF token field untuk form
 *
 * Contoh di View:
 *   <form method="POST">
 *       <?= csrf_field() ?>
 *       ...
 *   </form>
 */
function csrf_field(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verifikasi CSRF token dari form POST
 * Panggil di awal setiap proses POST
 *
 * Contoh di Controller:
 *   public function store() {
 *       verifyCsrf();
 *       // ... proses data
 *   }
 */
function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token tidak valid. Silakan refresh halaman dan coba lagi.');
    }
    // Generate token baru setelah verifikasi
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ============================================================
// AUTHENTICATION HELPERS
// ============================================================

/**
 * Cek apakah user sudah login
 *
 * @return bool
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Cek apakah user yang login memiliki role tertentu
 *
 * @param string $role 'admin' atau 'kasir'
 * @return bool
 */
function isRole(string $role): bool
{
    if (!isAuthenticated()) {
        return false;
    }

    $currentRole = $_SESSION['user']['role'] ?? '';

    if ($role === 'admin') {
        return in_array($currentRole, ['admin', 'super_admin'], true);
    }

    return $currentRole === $role;
}

/**
 * Cek apakah user login adalah super admin.
 */
function isSuperAdmin(): bool
{
    return isAuthenticated() && ($_SESSION['user']['role'] ?? '') === 'super_admin';
}

/**
 * Label role yang aman ditampilkan di UI.
 */
function roleLabel(?string $role): string
{
    return match ($role) {
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'kasir' => 'Kasir',
        default => '-',
    };
}

/**
 * Redirect ke halaman login jika belum autentikasi
 */
function requireAuth(): void
{
    if (!isAuthenticated()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login');
    }
}

/**
 * Redirect jika user tidak memiliki role yang sesuai
 *
 * @param string $role 'admin' atau 'kasir'
 */
function requireRole(string $role): void
{
    if (!isAuthenticated()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login');
    }

    if (!isRole($role)) {
        http_response_code(403);
        die('Anda tidak memiliki akses ke halaman ini.');
    }
}

// ============================================================
// FORMAT HELPERS
// ============================================================

/**
 * Format angka ke format Rupiah
 *
 * Contoh: formatRupiah(25000) → 'Rp 25.000'
 */
function formatRupiah(int|float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Escape HTML untuk mencegah XSS
 *
 * Contoh: e($user_input)
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
