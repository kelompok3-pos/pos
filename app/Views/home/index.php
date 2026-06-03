<!-- ============================================================ -->
<!-- DASHBOARD -->
<!-- ============================================================ -->

<?php
$totalProducts ??= 0;
$totalStock    ??= 0;
$todayRevenue  ??= 0;
$todaySales    ??= 0;
$totalUsers    ??= 0;
$todayHeaders   ??= [];
$todayDetails   ??= [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-speedometer2 text-primary"></i> Dashboard
        </h2>
        <p class="text-muted mb-0">Pantau performa stok dan penjualan harian</p>
    </div>
    <div class="card border-0 shadow-sm px-3 py-2 bg-white">
        <i class="bi bi-calendar3 text-primary me-2"></i>
        <?= date('d M Y') ?>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-primary bg-opacity-10 text-primary fs-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Produk</div>
                    <div class="fs-4 fw-bold text-dark">
                        <?= number_format($totalProducts, 0, ',', '.') ?>
                        <span class="fs-6 text-muted fw-normal">Item</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-warning bg-opacity-10 text-warning fs-3">
                    <i class="bi bi-layers-half"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Stok</div>
                    <div class="fs-4 fw-bold text-dark">
                        <?= number_format($totalStock, 0, ',', '.') ?>
                        <span class="fs-6 text-muted fw-normal">Pcs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-success bg-opacity-10 text-success fs-3">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pendapatan Hari Ini</div>
                    <div class="fs-5 fw-bold text-success"><?= formatRupiah($todayRevenue) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-info bg-opacity-10 text-info fs-3">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Transaksi Hari Ini</div>
                    <div class="fs-4 fw-bold text-dark">
                        <?= number_format($todaySales, 0, ',', '.') ?>
                        <span class="fs-6 text-muted fw-normal">Trx</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body d-flex gap-2">
                <?php if (isRole('admin')): ?>
                    <a href="<?= url('/admin/product/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
                    </a>
                    <a href="<?= url('/admin/user/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus me-1"></i> Tambah User
                    </a>
                <?php endif; ?>
                <a href="<?= url('/kasir/transaction') ?>" class="btn btn-success">
                    <i class="bi bi-cart-check me-1"></i> Transaksi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Hari Ini -->
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-light py-3 border-0">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-clock-history text-primary me-1"></i>
            Transaksi Hari Ini (<?= count($todayHeaders) ?> transaksi)
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Kode</th>
                    <th>Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todayHeaders)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Belum ada transaksi hari ini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todayHeaders as $trx): ?>
                        <?php
                        $items = $todayDetails[$trx['id']] ?? [];
                        ?>
                        <tr>
                            <td><code class="small"><?= e($trx['transaction_code']) ?></code></td>
                            <td>
                                <?php foreach ($items as $item): ?>
                                    <div>
                                        <span class="fw-semibold"><?= e($item['product_name']) ?></span>
                                        — <?= $item['quantity'] ?>x
                                        <?= formatRupiah(round($item['subtotal'] / $item['quantity'])) ?>
                                        = <strong class="text-success"><?= formatRupiah($item['subtotal']) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-end fw-bold text-success">
                                <?= formatRupiah($trx['total_price']) ?>
                            </td>
                            <td class="text-center text-muted small">
                                <?= date('H:i', strtotime($trx['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>