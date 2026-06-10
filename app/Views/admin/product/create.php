<!-- ============================================================ -->
<!-- ADMIN: FORM TAMBAH PRODUK -->
<!-- ============================================================ -->
<div class="page-hero">
<div class="page-title">
<h2><i class="ti ti-plus text-brand" aria-hidden="true"></i> Tambah Produk</h2>
<p>Tambahkan produk baru dengan harga dan stok awal yang jelas.</p>
</div>
</div>
<div class="row justify-content-center">
<div class="col-md-8 col-lg-7">
<div class="card surface">
<div class="card-header pt-4 px-4">
<h5 class="mb-0 text-dark">Informasi Produk</h5>
</div>
<div class="card-body">
<form action="<?= url('/admin/product/store') ?>" method="POST">
<?= csrf_field() ?>
<div class="mb-3">
<label for="name" class="form-label">Nama Produk <span class="text-error">*</span></label>
<input type="text" class="form-input" id="name" name="name" value="<?= old('name') ?>" required placeholder="Contoh: Kopi Arabica">
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label for="price" class="form-label">Harga (Rp) <span class="text-error">*</span></label>
<input type="number" class="form-input" id="price" name="price" value="<?= old('price') ?>" required min="0" placeholder="Contoh: 25000">
</div>
<div class="col-md-6 mb-3">
<label for="minimum_stock" class="form-label">Minimum Stock</label>
<input type="number" class="form-input" id="minimum_stock" name="minimum_stock" value="<?= old('minimum_stock', '5') ?>" min="0">
</div>
<div class="col-md-6 mb-3">
<label for="stock" class="form-label">Stok <span class="text-error">*</span></label>
<input type="number" class="form-input" id="stock" name="stock" value="<?= old('stock') ?>" required min="0" placeholder="Contoh: 100">
</div>
</div>
<div class="flex-row flex-between">
<a href="<?= url('/admin/product') ?>" class="btn btn-ghost">
<i class="ti ti-arrow-left" aria-hidden="true"></i> Kembali </a>
<button type="submit" class="btn btn-primary">
<i class="ti ti-check" aria-hidden="true"></i> Simpan Produk </button>
</div>
</form>
</div>
</div>
</div>
</div>
