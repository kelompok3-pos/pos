<!-- ============================================================ -->
<!-- ADMIN: DAFTAR PRODUK -->
<!-- ============================================================ -->

<div class="page-hero">
    <div class="page-title">
        <h2><i class="bi bi-box-seam text-primary"></i> Daftar Produk</h2>
        <p>Kelola nama, harga, stok, dan status produk toko.</p>
    </div>
    <a href="<?= url('/admin/product/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada produk. 
        <a href="<?= url('/admin/product/create') ?>">Tambah produk pertama</a>.
    </div>
<?php else: ?>
    <div class="card modern-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Deskripsi</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $i => $product): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= e($product['name']) ?></td>
                            <td><?= formatRupiah($product['price']) ?></td>
                            <td>
                                <?php if ($product['stock'] <= 5): ?>
                                    <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= e($product['description'] ?? '-') ?></td>
                            <td>
                                <a href="<?= url('/admin/product/edit') ?>?id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="<?= url('/admin/product/delete') ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                        <i class="bi bi-trash"></i> Hapus
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
