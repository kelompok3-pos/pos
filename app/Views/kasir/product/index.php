<!-- ============================================================ -->
<!-- KASIR: DAFTAR PRODUK (READ-ONLY) -->
<!-- ============================================================ -->

<h2 class="mb-4"><i class="bi bi-box-seam"></i> Daftar Produk</h2>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada produk.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= e($product['name']) ?></h5>
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
