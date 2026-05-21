<?php

/**
 * =================================================================
 * ROUTES — DAFTAR SEMUA HALAMAN
 * =================================================================
 * Format:  'URL' => ['NamaController', 'namaMethod']
 *
 * Aturan penamaan controller:
 * - Controller di root:    'HomeController'
 * - Controller di subfolder: 'Admin/AdminProductController'
 *
 * Contoh menambah route baru:
 *   '/admin/category'        => ['Admin/AdminCategoryController', 'index'],
 *   '/admin/category/create' => ['Admin/AdminCategoryController', 'create'],
 * =================================================================
 */

$routes = [
    // ============================================================
    // HOME
    // ============================================================
    '/'                          => ['HomeController',                  'index'],

    // ============================================================
    // AUTH — Login & Logout
    // ============================================================
    '/login'                     => ['AuthController',                  'loginForm'],
    '/login/authenticate'        => ['AuthController',                  'login'],
    '/logout'                    => ['AuthController',                  'logout'],

    // ============================================================
    // ADMIN — Kelola Produk (CRUD)
    // ============================================================
    '/admin/product'             => ['Admin/AdminProductController',    'index'],
    '/admin/product/create'      => ['Admin/AdminProductController',    'create'],
    '/admin/product/store'       => ['Admin/AdminProductController',    'store'],
    '/admin/product/edit'        => ['Admin/AdminProductController',    'edit'],
    '/admin/product/update'      => ['Admin/AdminProductController',    'update'],
    '/admin/product/delete'      => ['Admin/AdminProductController',    'delete'],

    // ============================================================
    // KASIR — Lihat Produk & Transaksi
    // ============================================================
    '/kasir/product'             => ['Kasir/KasirProductController',    'index'],
    '/kasir/transaction'         => ['Kasir/KasirTransactionController', 'index'],
    '/kasir/transaction/store'   => ['Kasir/KasirTransactionController', 'store'],
];

// ============================================================
    // ADMIN — Kelola Produk (CRUD)
    // ============================================================
    '/admin/product'             => ['Admin/AdminProductController',    'index'],
    '/admin/product/create'      => ['Admin/AdminProductController',    'create'],
    '/admin/product/store'       => ['Admin/AdminProductController',    'store'],
    '/admin/product/edit'        => ['Admin/AdminProductController',    'edit'],
    '/admin/product/update'      => ['Admin/AdminProductController',    'update'],
    '/admin/product/delete'      => ['Admin/AdminProductController',    'delete'],

    // ============================================================
    // ADMIN — Kelola Users (Tugas: Moses)
    // ============================================================
    '/admin/user'                => ['Admin/AdminUserController',       'index'],
    '/admin/user/create'         => ['Admin/AdminUserController',       'create'],
    '/admin/user/store'          => ['Admin/AdminUserController',       'store'],
    '/admin/user/delete'         => ['Admin/AdminUserController',       'delete'],