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
    '/'                          => ['HomeController',                  'landing',        ['methods' => ['GET']]],
    '/dashboard'                 => ['HomeController',                  'index',          ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/reports'                   => ['Admin/AdminModuleController',     'reports',        ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/reports/export'            => ['Admin/AdminModuleController',     'exportReports',  ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/inventory'                 => ['Admin/AdminModuleController',     'inventory',      ['methods' => ['GET'], 'roles' => ['admin']]],
    '/inventory/adjust'          => ['Admin/AdminModuleController',     'adjustStock',    ['methods' => ['POST'], 'roles' => ['admin']]],
    '/settings'                  => ['Admin/AdminModuleController',     'settings',       ['methods' => ['GET'], 'roles' => ['super_admin']]],
    '/settings/update'           => ['Admin/AdminModuleController',     'updateSettings', ['methods' => ['POST'], 'roles' => ['super_admin']]],
    '/superadmin/dashboard'      => ['SuperAdmin/SuperAdminController', 'dashboard',      ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin']]],
    '/superadmin/reports'        => ['SuperAdmin/SuperAdminController', 'reports',        ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin']]],
    '/superadmin/reports/export' => ['SuperAdmin/SuperAdminController', 'exportReports',  ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin']]],
    '/superadmin/stores'         => ['SuperAdmin/SuperAdminController', 'stores',         ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin']]],
    '/superadmin/audit'          => ['SuperAdmin/SuperAdminController', 'audit',          ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin']]],

    // ============================================================
    // AUTH — Login & Logout
    // ============================================================
    '/login'                     => ['AuthController',                  'loginForm',      ['methods' => ['GET']]],
    '/login/authenticate'        => ['AuthController',                  'login',          ['methods' => ['POST']]],
    '/logout'                    => ['AuthController',                  'logout',         ['methods' => ['POST'], 'roles' => ['super_admin', 'admin', 'kasir']]],

    // ============================================================
    // ADMIN — Kelola Produk (CRUD)
    // ============================================================
    '/admin/product'             => ['Admin/AdminProductController',    'index',  ['methods' => ['GET'], 'roles' => ['admin']]],
    '/admin/product/create'      => ['Admin/AdminProductController',    'create', ['methods' => ['GET'], 'roles' => ['admin']]],
    '/admin/product/store'       => ['Admin/AdminProductController',    'store',  ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/product/edit'        => ['Admin/AdminProductController',    'edit',   ['methods' => ['GET'], 'roles' => ['admin']]],
    '/admin/product/update'      => ['Admin/AdminProductController',    'update', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/product/delete'      => ['Admin/AdminProductController',    'delete', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/product/status'      => ['Admin/AdminProductController',    'status', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/product/import'      => ['Admin/AdminProductController',    'import', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/expense'             => ['Admin/AdminExpenseController',    'index',  ['methods' => ['GET'], 'roles' => ['admin']]],
    '/admin/expense/store'       => ['Admin/AdminExpenseController',    'store',  ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/expense/update'      => ['Admin/AdminExpenseController',    'update', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/expense/delete'      => ['Admin/AdminExpenseController',    'delete', ['methods' => ['POST'], 'roles' => ['admin']]],
    '/admin/expense/export'      => ['Admin/AdminExpenseController',    'export', ['methods' => ['GET'], 'roles' => ['admin']]],

    // ============================================================
    // KASIR — Lihat Produk & Transaksi
    // ============================================================
    '/kasir/transaction'                   => ['Kasir/KasirTransactionController', 'index',          ['methods' => ['GET'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/add'               => ['Kasir/KasirTransactionController', 'add',            ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/update'            => ['Kasir/KasirTransactionController', 'update',         ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/remove'            => ['Kasir/KasirTransactionController', 'remove',         ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/clear'             => ['Kasir/KasirTransactionController', 'clear',          ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/checkout'          => ['Kasir/KasirTransactionController', 'checkout',       ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/kasir/transaction/receipt'           => ['Kasir/KasirTransactionController', 'receipt',        ['methods' => ['GET'], 'roles' => ['admin', 'kasir']]],
    '/kasir/my-transactions'               => ['Kasir/KasirTransactionController', 'myTransactions', ['methods' => ['GET'], 'roles' => ['kasir']]],
    '/kasir/shift'                         => ['Kasir/KasirShiftController',       'index',          ['methods' => ['GET'], 'roles' => ['kasir']]],
    '/kasir/shift/open'                    => ['Kasir/KasirShiftController',       'open',           ['methods' => ['POST'], 'roles' => ['kasir']]],
    '/kasir/shift/close'                   => ['Kasir/KasirShiftController',       'close',          ['methods' => ['POST'], 'roles' => ['kasir']]],

    '/api/products/search'                 => ['Api/ApiController', 'productSearch',     ['methods' => ['GET'], 'roles' => ['admin', 'kasir']]],
    '/api/cart/calculate'                  => ['Api/ApiController', 'calculateCart',     ['methods' => ['POST'], 'roles' => ['admin', 'kasir']]],
    '/api/transactions/submit'             => ['Api/ApiController', 'submitTransaction', ['methods' => ['POST'], 'roles' => ['kasir']]],
    '/api/stock/product-info'              => ['Api/ApiController', 'productInfo',       ['methods' => ['GET'], 'roles' => ['admin', 'kasir']]],
    '/api/reports/chart-daily'              => ['Api/ApiController', 'chartDaily',        ['methods' => ['GET'], 'roles' => ['super_admin', 'superadmin', 'admin']]],


    // ============================================================
    // ADMIN — Kelola Users
    // ============================================================
    '/admin/user'                => ['Admin/AdminUserController', 'index',         ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/create'         => ['Admin/AdminUserController', 'create',        ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/store'          => ['Admin/AdminUserController', 'store',         ['methods' => ['POST'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/edit'           => ['Admin/AdminUserController', 'edit',          ['methods' => ['GET'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/update'         => ['Admin/AdminUserController', 'update',        ['methods' => ['POST'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/delete'         => ['Admin/AdminUserController', 'delete',        ['methods' => ['POST'], 'roles' => ['super_admin', 'admin']]],
    '/admin/user/reset-password' => ['Admin/AdminUserController', 'resetPassword', ['methods' => ['POST'], 'roles' => ['super_admin', 'admin']]],
];
