<!-- ============================================================ -->
<!-- KASIR: DAFTAR PRODUK (READ-ONLY) -->
<!-- ============================================================ -->

<div class="page-hero">
    <div class="page-title">
        <h2><i class="bi bi-box-seam text-primary"></i> Daftar Produk</h2>
        <p>Lihat harga dan ketersediaan stok sebelum membuat transaksi.</p>
    </div>
    <a href="<?= url('/kasir/transaction') ?>" class="btn btn-success">
        <i class="bi bi-cart-check"></i> Buka Transaksi
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada produk.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <h5 class="card-title fw-bold mb-0"><?= e($product['name']) ?></h5>
                            <?php if ($product['stock'] <= 5): ?>
                                <span class="badge bg-danger">Low</span>
                            <?php endif; ?>
                        </div>
                        <p class="card-text text-muted small"><?= e($product['description'] ?? '-') ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary"><?= formatRupiah($product['price']) ?></span>
                            <?php if ($product['stock'] <= 5): ?>
                                <span class="badge bg-danger">Stok: <?= $product['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">Stok: <?= $product['stock'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
