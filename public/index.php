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

// 3. Ambil URL dari browser
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = rtrim($url, '/') ?: '/';   // Bersihkan trailing slash, default ke '/'

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
