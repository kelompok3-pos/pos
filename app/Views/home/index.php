<!-- ============================================================ -->
<!-- DASHBOARD -->
<!-- ============================================================ -->

<?php
$totalProducts      ??= 0;
$totalStock         ??= 0;
$todayRevenue       ??= 0;
$todaySales         ??= 0;
$todayItemsSold     ??= 0;
$todayPaidAmount    ??= 0;
$todayChangeAmount  ??= 0;
$todayTopProducts   ??= [];
$totalUsers         ??= 0;
$lowStockProducts   ??= [];
$todayHeaders       ??= [];
$todayDetails       ??= [];

$role = $_SESSION['user']['role'] ?? '';
$primaryActionUrl = isRole('admin') ? url('/admin/product') : url('/kasir/transaction');
$primaryActionText = isRole('admin') ? 'Kelola Produk' : 'Mulai Transaksi';
$primaryActionIcon = isRole('admin') ? 'bi-box-seam' : 'bi-cart-check';
?>

<section class="dashboard-hero" data-tour="dashboard-overview">
    <div>
        <span class="dashboard-eyebrow"><?= e(roleLabel($role)) ?> Workspace</span>
        <h2>Dashboard</h2>
        <p>Ringkasan toko hari ini, dibuat ringkas supaya kamu langsung tahu aksi berikutnya.</p>
    </div>
    <div class="dashboard-hero-actions">
        <div class="dashboard-user-pill">
            <i class="bi bi-person-circle"></i>
            <span><?= e($_SESSION['user']['email'] ?? '-') ?></span>
        </div>
        <a href="<?= $primaryActionUrl ?>" class="btn btn-primary" data-tour="primary-action">
            <i class="bi <?= $primaryActionIcon ?>"></i> <?= $primaryActionText ?>
        </a>
    </div>
</section>

<section class="dashboard-actions">
    <?php if (isRole('admin')): ?>
        <a href="<?= url('/admin/product/create') ?>" class="dashboard-action-card" data-tour="quick-add-product">
            <i class="bi bi-plus-lg"></i>
            <strong>Tambah Produk</strong>
            <span>Masukkan produk dan stok baru.</span>
        </a>
        <a href="<?= url('/admin/user/create') ?>" class="dashboard-action-card" data-tour="quick-add-user">
            <i class="bi bi-person-plus"></i>
            <strong>Tambah User</strong>
            <span>Buat dan kelola akun tim.</span>
        </a>
        <a href="<?= url('/admin/product') ?>" class="dashboard-action-card" data-tour="quick-stock">
            <i class="bi bi-archive"></i>
            <strong>Cek Stok</strong>
            <span>Review stok dan harga produk.</span>
        </a>
    <?php endif; ?>

    <?php if (isRole('kasir')): ?>
        <a href="<?= url('/kasir/transaction') ?>" class="dashboard-action-card" data-tour="quick-transaction">
            <i class="bi bi-cart-plus"></i>
            <strong>Transaksi Baru</strong>
            <span>Buka kasir dan mulai checkout.</span>
        </a>
        <a href="<?= url('/kasir/product') ?>" class="dashboard-action-card" data-tour="quick-product-search">
            <i class="bi bi-search"></i>
            <strong>Cari Produk</strong>
            <span>Lihat harga dan stok tersedia.</span>
        </a>
    <?php endif; ?>

    <a href="<?= url('/report/daily/export') ?>" class="dashboard-action-card" data-tour="quick-export">
        <i class="bi bi-download"></i>
        <strong>Export CSV</strong>
        <span>Unduh laporan penjualan hari ini.</span>
    </a>
</section>

<section class="dashboard-kpis" data-tour="dashboard-kpis">
    <div class="dashboard-kpi">
        <span class="dashboard-kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-cash-stack"></i></span>
        <div>
            <small>Pendapatan Hari Ini</small>
            <strong><?= formatRupiah($todayRevenue) ?></strong>
        </div>
    </div>
    <div class="dashboard-kpi">
        <span class="dashboard-kpi-icon bg-success bg-opacity-10 text-success"><i class="bi bi-receipt"></i></span>
        <div>
            <small>Transaksi</small>
            <strong><?= number_format($todaySales, 0, ',', '.') ?> trx</strong>
        </div>
    </div>
    <div class="dashboard-kpi">
        <span class="dashboard-kpi-icon bg-info bg-opacity-10 text-info"><i class="bi bi-bag-check"></i></span>
        <div>
            <small>Produk Terjual</small>
            <strong><?= number_format($todayItemsSold, 0, ',', '.') ?> pcs</strong>
        </div>
    </div>
    <div class="dashboard-kpi">
        <span class="dashboard-kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-box-seam"></i></span>
        <div>
            <small>Total Stok</small>
            <strong><?= number_format($totalStock, 0, ',', '.') ?> pcs</strong>
        </div>
    </div>
</section>

<section class="dashboard-grid">
    <div class="dashboard-panel">
        <div class="dashboard-panel-header">
            <div>
                <h5>Perhatian Hari Ini</h5>
                <p>Hal yang perlu dicek sebelum toko makin ramai.</p>
            </div>
        </div>

        <?php if (empty($lowStockProducts)): ?>
            <div class="dashboard-empty">
                <i class="bi bi-check-circle"></i>
                <strong>Stok aman</strong>
                <span>Tidak ada produk dengan stok rendah.</span>
            </div>
        <?php else: ?>
            <div class="dashboard-list">
                <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                    <div class="dashboard-list-item">
                        <div>
                            <strong><?= e($product['name']) ?></strong>
                            <span>Sisa stok <?= e((string) $product['stock']) ?> pcs</span>
                        </div>
                        <?php if (isRole('admin')): ?>
                            <a href="<?= url('/admin/product/edit') ?>?id=<?= e((string) $product['id']) ?>" class="btn btn-sm btn-warning">
                                Update
                            </a>
                        <?php else: ?>
                            <span class="badge bg-danger">Low</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-panel">
        <div class="dashboard-panel-header">
            <div>
                <h5>Produk Terlaris</h5>
                <p>Produk yang paling banyak dibeli hari ini.</p>
            </div>
        </div>

        <?php if (empty($todayTopProducts)): ?>
            <div class="dashboard-empty">
                <i class="bi bi-inbox"></i>
                <strong>Belum ada data</strong>
                <span>Produk terlaris muncul setelah ada transaksi hari ini.</span>
            </div>
        <?php else: ?>
            <div class="dashboard-list">
                <?php foreach ($todayTopProducts as $index => $product): ?>
                    <div class="dashboard-list-item">
                        <div class="dashboard-rank"><?= $index + 1 ?></div>
                        <div>
                            <strong><?= e($product['product_name']) ?></strong>
                            <span><?= e((string) $product['total_quantity']) ?> pcs terjual</span>
                        </div>
                        <div class="dashboard-money"><?= formatRupiah($product['total_sales']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="dashboard-panel mt-4">
    <div class="dashboard-panel-header">
        <div>
            <h5>Transaksi Hari Ini</h5>
            <p><?= count($todayHeaders) ?> transaksi tercatat hari ini.</p>
        </div>
        <div class="dashboard-cash-summary">
            <span>Diterima: <strong><?= formatRupiah($todayPaidAmount) ?></strong></span>
            <span>Kembalian: <strong><?= formatRupiah($todayChangeAmount) ?></strong></span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Bayar</th>
                    <th class="text-end">Kembali</th>
                    <th class="text-center">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todayHeaders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Belum ada transaksi hari ini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todayHeaders as $trx): ?>
                        <?php $items = $todayDetails[$trx['id']] ?? []; ?>
                        <tr>
                            <td><code class="small"><?= e($trx['transaction_code']) ?></code></td>
                            <td>
                                <?php foreach ($items as $item): ?>
                                    <?php $quantity = (int) $item['quantity']; ?>
                                    <div class="dashboard-item-line">
                                        <span><?= e($item['product_name']) ?></span>
                                        <small><?= $quantity ?>x = <?= formatRupiah($item['subtotal']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-end fw-bold text-success"><?= formatRupiah($trx['total_price']) ?></td>
                            <td class="text-end"><?= formatRupiah($trx['paid_amount'] ?? 0) ?></td>
                            <td class="text-end"><?= formatRupiah($trx['change_amount'] ?? 0) ?></td>
                            <td class="text-center text-muted small"><?= date('H:i', strtotime($trx['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
