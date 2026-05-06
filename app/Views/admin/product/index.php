<!-- ============================================================ -->
<!-- ADMIN: DAFTAR PRODUK -->
<!-- ============================================================ -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Daftar Produk</h2>
    <a href="/admin/product/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada produk. 
        <a href="/admin/product/create">Tambah produk pertama</a>.
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
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
                                <a href="/admin/product/edit?id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="/admin/product/delete?id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
