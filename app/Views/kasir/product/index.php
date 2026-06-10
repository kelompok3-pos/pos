<!-- ============================================================ -->
<!-- KASIR: DAFTAR PRODUK (READ-ONLY) -->
<!-- ============================================================ -->
<div class="page-hero">
<div class="page-title">
<h2><i class="ti ti-package text-brand" aria-hidden="true"></i> Daftar Produk</h2>
<p>Lihat harga dan ketersediaan stok sebelum membuat transaksi.</p>
</div>
<a href="<?= url('/kasir/transaction') ?>" class="btn btn-primary">
<i class="ti ti-cash-register" aria-hidden="true"></i> Buka Transaksi </a>
</div>
<?php if (empty($products)): ?>
<div class="toast toast-info">
<i class="ti ti-info-circle" aria-hidden="true"></i> Belum ada produk. </div>
<?php else: ?>
<div class="row g-3">
<?php foreach ($products as $product): ?>
<div class="col-md-4 col-lg-3">
<div class="card h-100">
<div class="card__body">
<div class="flex-row flex-between gap-2 mb-2">
<h5 class="card__title mb-0"><?= e($product['name']) ?></h5>
<?php if ($product['stock'] <= 5): ?>
<span class="badge badge-error">Low</span>
<?php endif; ?>
</div>
<p class="text-sm text-muted"><?= e($product['description'] ?? '-') ?></p>
<div class="flex-row flex-between">
<span class="card__price"><?= formatRupiah($product['price']) ?></span>
<?php if ($product['stock'] <= 5): ?>
<span class="badge badge-error">Stok: <?= $product['stock'] ?></span>
<?php else: ?>
<span class="badge badge-success">Stok: <?= $product['stock'] ?></span>
<?php endif; ?>
</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
