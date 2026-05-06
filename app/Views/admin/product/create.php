<!-- ============================================================ -->
<!-- ADMIN: FORM TAMBAH PRODUK -->
<!-- ============================================================ -->

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-lg"></i> Tambah Produk Baru</h5>
            </div>
            <div class="card-body">
                <form action="/admin/product/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name') ?>" required placeholder="Contoh: Kopi Arabica">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="<?= old('price') ?>" required min="0" placeholder="Contoh: 25000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?= old('stock') ?>" required min="0" placeholder="Contoh: 100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Deskripsi produk (opsional)"><?= old('description') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/product" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
