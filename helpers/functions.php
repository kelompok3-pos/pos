<?php

require_once dirname(__DIR__) . '/config/roles.php';
require_once dirname(__DIR__) . '/src/Support/Flash.php';
require_once dirname(__DIR__) . '/src/Support/Csrf.php';
require_once dirname(__DIR__) . '/src/Support/Formatter.php';
require_once dirname(__DIR__) . '/src/Support/Redirector.php';
require_once dirname(__DIR__) . '/src/Support/Escaper.php';

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

/** @deprecated Use Redirector::to(). */
function redirect(string $url): void
{
    Redirector::to($url);
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

// ============================================================
// FLASH MESSAGES
// ============================================================

/** @deprecated Use Flash::set(). */
function flash(string $key, string $message): void
{
    Flash::set($key, $message);
}

/** @deprecated Use Flash::get(). */
function getFlash(string $key): ?string
{
    return Flash::get($key);
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

/** @deprecated Use Csrf::field(). */
function csrf_field(): string
{
    return Csrf::field();
}

/** @deprecated Use Csrf::verify(). */
function verifyCsrf(): void
{
    Csrf::verify();
}

/** @deprecated Use Csrf::regenerate(). */
function regenerateCsrf(): void
{
    Csrf::regenerate();
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
    return isRole(ROLE_SUPER_ADMIN);
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
        if (($_SESSION['role'] ?? '') === ROLE_SUPER_ADMIN) {
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
        ROLE_SUPER_ADMIN => [ROLE_ADMIN, ROLE_KASIR],
        ROLE_ADMIN => [ROLE_KASIR],
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

    return isRole(ROLE_ADMIN)
        && ($targetUser['role'] ?? '') === ROLE_KASIR
        && (int)($targetUser['assigned_admin_id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0)
        && isWithinCurrentStore($targetUser);
}

function roleLabel(?string $role): string
{
    return match ($role) {
        ROLE_SUPER_ADMIN => 'Super Admin',
        ROLE_ADMIN => 'Admin',
        ROLE_KASIR => 'Kasir',
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

/** @deprecated Use Formatter::currency(). */
function formatRupiah(int|float $angka): string
{
    return Formatter::currency($angka);
}

/** @deprecated Use Escaper::escape(). */
function e(?string $value): string
{
    return Escaper::escape($value);
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
