<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0"><i class="bi bi-speedometer2 text-primary"></i> Ringkasan Utama Toko</h2>
        <p class="text-muted fw-semibold mb-0">Pantau performa stok operasional dan penjualan harian</p>
    </div>
    <div class="card border-0 shadow-sm px-3 py-2 fw-bold text-secondary bg-white">
        <i class="bi bi-calendar3 text-primary me-2"></i> <?= date('d M Y') ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-primary bg-opacity-10 text-primary fs-3"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Produk</div>
                    <div class="fs-4 fw-bold text-dark"><?= number_format($totalProducts, 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">Item</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-warning bg-opacity-10 text-warning fs-3"><i class="bi bi-layers-half"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Sisa Total Stok</div>
                    <div class="fs-4 fw-bold text-dark"><?= number_format($totalStock, 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">Pcs</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-success bg-opacity-10 text-success fs-3"><i class="bi bi-currency-dollar"></i></div>
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
                <div class="p-3 rounded bg-info bg-opacity-10 text-info fs-3"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Transaksi Hari Ini</div>
                    <div class="fs-4 fw-bold text-dark"><?= number_format($todaySales, 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">Trx</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-light py-3 border-0">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary me-1"></i> Monitor Aktivitas Belanja Hari Ini</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">No</th>
                    <th>Nama Barang Modul</th>
                    <th class="text-center">Jumlah Beli</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal Nilai</th>
                    <th>Waktu Transaksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-dark">
                <?php if (empty($recentTrx)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada nota transaksi yang dicetak untuk hari ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentTrx as $idx => $trx): ?>
                        <tr>
                            <td class="ps-3 text-muted"><?= $idx + 1 ?></td>
                            <td><?= e($trx['product_name']) ?></td>
                            <td class="text-center bg-light"><?= $trx['quantity'] ?></td>
                            <td class="text-muted"><?= formatRupiah($trx['price']) ?></td>
                            <td class="text-primary fw-bold"><?= formatRupiah($trx['total_price']) ?></td>
                            <td class="text-muted small"><?= date('H:i:s', strtotime($trx['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>