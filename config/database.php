<?php

/**
 * =================================================================
 * KONEKSI DATABASE
 * =================================================================
 * File ini menyediakan fungsi getConnection() untuk mendapatkan
 * koneksi PDO ke database MySQL.
 *
 * Cara pakai di Model:
 *   $pdo = getConnection();
 *   $stmt = $pdo->prepare("SELECT * FROM products WHERE store_id = ?");
 * =================================================================
 */

/**
 * Mendapatkan koneksi PDO ke database
 *
 * Menggunakan singleton pattern — koneksi hanya dibuat 1x,
 * lalu dipakai ulang setiap kali dipanggil.
 *
 * @return PDO
 */
function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = env('DB_HOST', 'localhost');
        $port = env('DB_PORT', '3306');
        $db   = env('DB_DATABASE', 'pos_db');
        $user = env('DB_USERNAME', 'root');
        $pass = env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Tampilkan error detail di development, pesan umum di production
            if (APP_ENV === 'local') {
                die("Koneksi database gagal: " . $e->getMessage());
            } else {
                die("Terjadi kesalahan pada server. Silakan coba lagi nanti.");
            }
        }
    }

    return $pdo;
}
