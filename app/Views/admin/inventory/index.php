<div class="page-hero inventory-hero">
    <div class="page-title">
        <h2><i class="ti ti-packages" aria-hidden="true"></i> Inventory / Stock</h2>
        <p>Kelola perubahan stok dan pantau produk yang perlu segera diisi ulang.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Stock Adjustment Card -->
    <div class="col-lg-5">
        <div class="card surface">
            <div class="card-body">
                <h5>Penyesuaian Stok</h5>
                <form action="<?= url('/inventory/adjust') ?>" method="POST">
                    <?= csrf_field() ?>

                    <select name="product_id" class="form-input mb-2" required>
                        <option value="">Pilih produk</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>">
                                <?= e($product['name']) ?> (<?= $product['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="movement_type" class="form-input mb-2" required>
                        <option value="in">Tambah Stok</option>
                        <option value="out">Kurangi Stok</option>
                    </select>

                    <input type="number" name="quantity" min="1" class="form-input mb-2" placeholder="Jumlah" required>
                    <input type="text" name="note" class="form-input mb-2" placeholder="Alasan perubahan stok" required>

                    <button class="btn btn-primary w-100">Perbarui Stok</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts Card -->
    <div class="col-lg-7">
        <div class="card surface">
            <div class="card-body">
                <h5>Peringatan Stok Menipis</h5>

                <?php if (!empty($lowStock)): ?>
                    <?php foreach ($lowStock as $product): ?>
                        <div class="flex-row flex-between border-bottom py-2">
                            <span><?= e($product['name']) ?></span>
                            <span class="badge badge-error">
                                <?= $product['stock'] ?> / min <?= $product['minimum_stock'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Semua stok masih dalam batas aman.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Current Stock Table -->
<div class="card surface mb-4">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Stok Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= e($product['name']) ?></td>
                        <td><?= $product['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock Movement History Table -->
<div class="card surface">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Sebelum</th>
                    <th>Sesudah</th>
                    <th>Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($movements)): ?>
                    <?php foreach ($movements as $row): ?>
                        <tr>
                            <td><?= e($row['created_at']) ?></td>
                            <td><?= e($row['product_name']) ?></td>
                            <td><?= e($row['movement_type']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= $row['stock_before'] ?></td>
                            <td><?= $row['stock_after'] ?></td>
                            <td><?= e($row['user_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada riwayat perubahan stok.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
