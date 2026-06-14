<?php

/**
 * =================================================================
 * KONFIGURASI APLIKASI
 * =================================================================
 * File ini memuat environment variables dari file .env
 * dan menyediakan fungsi env() untuk mengakses nilainya.
 * =================================================================
 */

// Load file .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            $value = trim($value, '"\'');

            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

/**
 * Ambil nilai environment variable
 *
 * Contoh penggunaan:
 *   $dbHost = env('DB_HOST', 'localhost');
 *
 * @param string $key     Nama variabel
 * @param mixed  $default Nilai default jika tidak ditemukan
 * @return mixed
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ============================================================
// Constants
// ============================================================
define('APP_NAME', env('APP_NAME', 'POS App'));
define('APP_ENV', env('APP_ENV', 'local'));
define('APP_URL', env('APP_URL', 'http://localhost'));
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

// Start session
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || APP_ENV === 'production';
    session_name('POSSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}
