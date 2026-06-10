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

function redirect(string $url): void
{
    $base = rtrim(env('APP_URL', ''), '/');
    $fullUrl = str_starts_with($url, '/') ? $base . $url : $url;
    header("Location: {$fullUrl}");
    exit;
}

function url(string $path): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base ? $base . '/' . ltrim($path, '/') : $path;
}

function asset(string $path): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base ? $base . '/assets/' . ltrim($path, '/') : '/assets/' . ltrim($path, '/');
}

function base_url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

// ============================================================
// FLASH MESSAGES
// ============================================================

function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string
{
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

// ============================================================
// FORM HELPERS
// ============================================================

function old(string $key, string $default = ''): string
{
    $value = $_SESSION['old'][$key] ?? $default;
    unset($_SESSION['old'][$key]);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function keepOldInput(): void
{
    $_SESSION['old'] = array_diff_key($_POST, array_flip(['password', 'csrf_token']));
}

function csrf_field(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
        http_response_code(403);
        die('CSRF token tidak valid. Silakan refresh halaman dan coba lagi.');
    }
}

function regenerateCsrf(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ============================================================
// AUTHENTICATION HELPERS
// ============================================================

function isAuthenticated(): bool
{
    if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['name'])) {
        return false;
    }
    if (!class_exists('ActorContext')) {
        return true;
    }
    try {
        ActorContext::fromSession();
        return true;
    } catch (UnauthorizedException) {
        return false;
    }
}

function isRole(string $role): bool
{
    return isAuthenticated() && ($_SESSION['role'] ?? '') === $role;
}

function isSuperAdmin(): bool
{
    return isRole('super_admin') || isRole('superadmin');
}

function currentRole(): string
{
    return isAuthenticated() ? (string) ($_SESSION['role'] ?? '') : '';
}

function currentStoreId(): ?int
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    if (!class_exists('ActorContext')) {
        if (($_SESSION['role'] ?? '') === 'super_admin') {
            return null;
        }
        $storeId = (int) ($_SESSION['store_id'] ?? 0);
        return $storeId > 0 ? $storeId : 0;
    }
    try {
        $actor = ActorContext::fromSession();
        return $actor->isSuperAdmin() ? null : $actor->requireStoreId();
    } catch (UnauthorizedException) {
        return 0;
    }
}

function creatableRoles(): array
{
    return match (currentRole()) {
        'super_admin', 'superadmin' => ['admin', 'kasir'],
        'admin' => ['kasir'],
        default => [],
    };
}

function canManageRole(string $targetRole): bool
{
    return in_array($targetRole, creatableRoles(), true);
}

function isWithinCurrentStore(array $targetUser): bool
{
    if (isSuperAdmin()) return true;
    $storeId = currentStoreId();
    return $storeId !== null && $storeId > 0 && isset($targetUser['store_id']) && (int)$targetUser['store_id'] === $storeId;
}

function canManageUser(array $targetUser): bool
{
    if (!canManageRole((string)($targetUser['role'] ?? ''))) return false;

    if (isSuperAdmin()) return true;

    return isRole('admin')
        && ($targetUser['role'] ?? '') === 'kasir'
        && (int)($targetUser['assigned_admin_id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)
        && isWithinCurrentStore($targetUser);
}

function roleLabel(?string $role): string
{
    return match ($role) {
        'super_admin', 'superadmin' => 'Super Admin',
        'admin' => 'Admin',
        'kasir' => 'Kasir',
        default => '-',
    };
}

function allowOnly(array $roles): void
{
    try {
        ActorContext::fromSession()->requireRole(...$roles);
    } catch (UnauthorizedException $exception) {
        if (!isset($_SESSION['user_id'])) {
            flash('error', 'Silakan login terlebih dahulu.');
            redirect('/login');
        }
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode(['success' => false, 'message' => $exception->getMessage()]));
    }
}

// ============================================================
// FORMAT HELPERS
// ============================================================

function formatRupiah(int|float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generateSKU(string $prefix = 'PRD'): string
{
    $safePrefix = strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', $prefix));
    return substr($safePrefix ?: 'PRD', 0, 8) . '-' . strtoupper(bin2hex(random_bytes(4)));
}

function uploadImage(array $file, string $destination): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Upload gambar gagal.');
    }
    if ((int) ($file['size'] ?? 0) > 2 * 1024 * 1024) {
        throw new InvalidArgumentException('Ukuran gambar maksimal 2MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new InvalidArgumentException('Format gambar harus JPG, PNG, atau WEBP.');
    }
    if (!is_dir($destination) && !mkdir($destination, 0775, true) && !is_dir($destination)) {
        throw new RuntimeException('Direktori upload tidak dapat dibuat.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file((string) $file['tmp_name'], rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . $filename)) {
        throw new RuntimeException('Gambar gagal disimpan.');
    }
    return $filename;
}
