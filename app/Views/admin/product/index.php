<!-- ============================================================ -->
<!-- ADMIN: DAFTAR PRODUK -->
<!-- ============================================================ -->
<div class="page-hero">
<div class="page-title">
<h2><i class="ti ti-package text-brand" aria-hidden="true"></i> Daftar Produk</h2>
<p>Kelola nama, harga, stok, dan status produk toko.</p>
</div>
<a href="<?= url('/admin/product/create') ?>" class="btn btn-primary">
<i class="ti ti-plus" aria-hidden="true"></i> Tambah Produk </a>
</div>
<div class="card surface mb-3">
<div class="card-body">
<div class="input-search-wrapper mb-3">
<i class="ti ti-search input-search-icon" aria-hidden="true"></i>
<input type="search" id="managementProductSearch" class="form-input-search" placeholder="Search product name">
</div>
<form action="<?= url('/admin/product/import') ?>" method="POST" enctype="multipart/form-data" class="flex-row align-items-center">
<?= csrf_field() ?>
<input type="file" name="csv" class="form-input" accept=".csv" required>
<button class="btn btn-outline" type="submit"><i class="ti ti-upload" aria-hidden="true"></i> Import CSV</button>
</form>
<small class="text-muted">Header CSV: name,price,stock,minimum_stock</small>
</div>
</div>
<?php if (empty($products)): ?>
<div class="toast toast-info">
<i class="ti ti-info-circle" aria-hidden="true"></i> Belum ada produk. <a href="<?= url('/admin/product/create') ?>">Tambah produk pertama</a>. </div>
<?php else: ?>
<div class="card surface">
<div class="table-responsive">
<table class="table table-hover mb-0">
<thead class="">
<tr>
<th width="50">#</th>
<th>Nama Produk</th>
<th>Harga</th>
<th>Stok</th>
<th>Status</th>
<th width="180">Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($products as $i => $product): ?>
<tr class="management-product-row">
<td><?= $i + 1 ?></td>
<td class=""><?= e($product['name']) ?></td>
<td><?= formatRupiah($product['price']) ?></td>
<td>
<?php if ($product['stock'] <= 5): ?>
<span class="badge badge-error"><?= $product['stock'] ?></span>
<?php else: ?>
<span class="badge badge-success"><?= $product['stock'] ?></span>
<?php endif; ?>
</td>
<td><span class="badge <?= $product['status'] === 'active' ? 'badge-success' : 'badge-neutral' ?>"><?= e($product['status']) ?></span></td>
<td>
<a href="<?= url('/admin/product/edit') ?>?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline">
<i class="ti ti-edit" aria-hidden="true"></i> Edit </a>
<form action="<?= url('/admin/product/delete') ?>" method="POST" class="d-inline">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">
<i class="ti ti-trash" aria-hidden="true"></i> Hapus </button>
</form>
<form action="<?= url('/admin/product/status') ?>" method="POST" class="d-inline">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
<input type="hidden" name="status" value="<?= $product['status'] === 'active' ? 'inactive' : 'active' ?>">
<button class="btn btn-sm btn-ghost" type="submit">
<?= $product['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
<?php endif; ?>
<script>
document.getElementById('managementProductSearch')?.addEventListener('input', function () { const value = this.value.toLowerCase(); document.querySelectorAll('.management-product-row').forEach(row => row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none');
});
</script>
