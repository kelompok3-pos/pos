<?php

/**
 * =================================================================
 * BASE CONTROLLER
 * =================================================================
 * Parent class untuk semua controller.
 * Menyediakan method view() dan redirect().
 *
 * Semua controller HARUS meng-extend class ini:
 *   class AdminProductController extends Controller { ... }
 * =================================================================
 */

class Controller
{
    /**
     * Render view dan kirim data ke tampilan
     *
     * Contoh penggunaan:
     *   $this->view('admin/product/index', [
     *       'title'    => 'Daftar Produk',
     *       'products' => $products,
     *   ]);
     *
     * @param string $viewName Path view relatif dari folder Views/ (tanpa .php)
     * @param array  $data     Data yang dikirim ke view (key jadi nama variabel)
     */
    protected function view(string $viewName, array $data = []): void
    {
        // Extract data menjadi variabel
        // Contoh: $data['products'] → jadi variabel $products di view
        extract($data);

        // Path ke file view
        $content = BASE_PATH . "/app/Views/{$viewName}.php";

        // Cek apakah file view ada
        if (!file_exists($content)) {
            die("View tidak ditemukan: {$viewName}.php");
        }

        // Load layout utama (yang akan meng-include $content)
        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    /**
     * Redirect ke URL lain
     *
     * Contoh: $this->redirect('/admin/product')
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
