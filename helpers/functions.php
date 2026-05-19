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
 *
 * Contoh: redirect('/admin/product')
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Generate URL lengkap untuk asset (CSS, JS, gambar)
 *
 * Contoh: asset('css/style.css') → '/assets/css/style.css'
 */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
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
    $_SESSION['old'] = $_POST;
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
    return isAuthenticated() && ($_SESSION['user']['role'] ?? '') === $role;
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
