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
 * '/admin/category'        => ['Admin/AdminCategoryController', 'index'],
 * '/admin/category/create' => ['Admin/AdminCategoryController', 'create'],
 * =================================================================
 */

$routes = [
    // ============================================================
    // HOME
    // ============================================================
    '/'                          => ['HomeController',                  'landing'],
    '/dashboard'                 => ['HomeController',                  'index'],
    '/report/daily/export'       => ['HomeController',                  'exportDaily'],

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
    '/kasir/product'                       => ['Kasir/KasirProductController',      'index'],
    '/kasir/transaction'                   => ['Kasir/KasirTransactionController',   'index'],
    '/kasir/transaction/add'               => ['Kasir/KasirTransactionController',   'add'],
    '/kasir/transaction/update'            => ['Kasir/KasirTransactionController',   'update'],
    '/kasir/transaction/remove'            => ['Kasir/KasirTransactionController',   'remove'],
    '/kasir/transaction/clear'             => ['Kasir/KasirTransactionController',   'clear'],
    '/kasir/transaction/checkout'          => ['Kasir/KasirTransactionController',   'checkout'],
    '/kasir/transaction/receipt'           => ['Kasir/KasirTransactionController',   'receipt'],

    '/kasir/dashboard'           => ['Kasir/KasirDashboardController',  'index'], // ◄ FIXED FINAL KELOMPOK

    // ============================================================
    // ADMIN — Kelola Users
    // ============================================================
    '/admin/user'                => ['Admin/AdminUserController',       'index'],
    '/admin/user/create'         => ['Admin/AdminUserController',       'create'],
    '/admin/user/store'          => ['Admin/AdminUserController',       'store'],
    '/admin/user/edit'           => ['Admin/AdminUserController',       'edit'],
    '/admin/user/update'         => ['Admin/AdminUserController',       'update'],
    '/admin/user/delete'         => ['Admin/AdminUserController',       'delete'],
];
