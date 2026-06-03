<!-- ============================================================ -->
<!-- ADMIN: FORM EDIT PRODUK -->
<!-- ============================================================ -->
<?php $product ??= []; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Produk</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/product/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= e($product['id'] ?? '') ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= e($product['name'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price"
                                   value="<?= e($product['price'] ?? '') ?>" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock"
                                   value="<?= e($product['stock'] ?? '') ?>" required min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="3"><?= e($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= url('/admin/product') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
