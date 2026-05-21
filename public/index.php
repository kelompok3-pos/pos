<?php

/**
 * =================================================================
 * ENTRY POINT — FRONT CONTROLLER
 * =================================================================
 * Semua request dari browser masuk ke file ini.
 * File ini akan:
 *   1. Load konfigurasi, database, dan helper
 *   2. Load daftar routes
 *   3. Mencocokkan URL dengan route yang terdaftar
 *   4. Memanggil Controller & Method yang sesuai
 * =================================================================
 */

// 1. Load semua yang dibutuhkan
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

// 2. Load daftar routes
require_once __DIR__ . '/../routes.php';

// 3. Ambil URL dari browser, lalu strip base path (subfolder) jika ada
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = rtrim($url, '/') ?: '/';

// Untuk XAMPP dengan subfolder (misal: /POS/public), hilangkan prefix subfolder
// Ini agar routing tetap bersih: /POS/public/admin/product → /admin/product
$scriptName = $_SERVER['SCRIPT_NAME'] ?? ''; // Contoh: /POS/public/index.php
if (preg_match('#^(.+)/index\.php$#', $scriptName, $m)) {
    $baseScript = rtrim($m[1], '/'); // Contoh: /POS/public
    if ($baseScript !== '' && str_starts_with($url, $baseScript)) {
        $url = '/' . ltrim(substr($url, strlen($baseScript)), '/') ?: '/';
    }
}

// 4. Cari route yang cocok
if (isset($routes[$url])) {
    $controllerPath = $routes[$url][0];   // contoh: "Admin/AdminProductController"
    $methodName     = $routes[$url][1];   // contoh: "index"

    // Load file controller
    require_once __DIR__ . "/../app/Controllers/{$controllerPath}.php";

    // Ambil nama class (bagian terakhir setelah /)
    $className = basename($controllerPath);  // "AdminProductController"

    // Buat instance controller dan panggil method-nya
    $controller = new $className();
    $controller->$methodName();
} else {
    // Route tidak ditemukan — tampilkan halaman 404
    http_response_code(404);
    $title = '404 - Halaman Tidak Ditemukan';
    $content = __DIR__ . '/../app/Views/errors/404.php';
    require __DIR__ . '/../app/Views/layouts/main.php';
}
