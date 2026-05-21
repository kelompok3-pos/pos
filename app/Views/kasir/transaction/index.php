<!-- ============================================================ -->
<!-- TRANSAKSI PENJUALAN -->
<!-- ============================================================ -->
<?php
$products            ??= [];
$transactions        ??= [];
$cart                 ??= [];
$transactionDetails  ??= [];
?>

<div class="row g-4">

    <!-- Kolom Kiri: Daftar Produk + Keranjang -->
    <div class="col-lg-5">

        <!-- Daftar Produk -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Pilih Produk</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <i class="bi bi-inbox fs-4"></i>
                                        <p class="mb-0">Belum ada produk.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($product['name']) ?></strong>
                                            <?php if (!empty($product['description'])): ?>
                                                <br><small class="text-muted"><?= e($product['description']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold text-success">
                                            <?= formatRupiah($product['price']) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($product['stock'] <= 5): ?>
                                                <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= $product['stock'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($product['stock'] > 0): ?>
                                                <form action="/kasir/transaction/add" method="POST" class="d-flex gap-1">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                    <input type="number" name="quantity" class="form-control form-control-sm"
                                                           value="1" min="1" max="<?= $product['stock'] ?>"
                                                           style="width: 60px;" required>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-cart-plus"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Habis</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cart4"></i> Keranjang</h5>
                <?php if (!empty($cart)): ?>
                    <span class="badge bg-light text-success">
                        <?= count($cart) ?> item
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($cart)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cart-x fs-1"></i>
                        <p class="mt-2 mb-0">Keranjang masih kosong.<br>Pilih produk di atas untuk menambahkan.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grandTotal = 0;
                                foreach ($cart as $item):
                                    $grandTotal += $item['subtotal'];
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($item['name']) ?></strong>
                                            <br><small class="text-muted"><?= formatRupiah($item['price']) ?>/pcs</small>
                                        </td>
                                        <td class="text-center align-middle"><?= $item['quantity'] ?></td>
                                        <td class="text-end align-middle fw-semibold text-success">
                                            <?= formatRupiah($item['subtotal']) ?>
                                        </td>
                                        <td class="align-middle">
                                            <form action="/kasir/transaction/remove" method="POST">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Hapus item ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-success">
                                <tr>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th class="text-end"><?= formatRupiah($grandTotal) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="card-footer bg-white d-flex gap-2">
                        <form action="/kasir/transaction/checkout" method="POST" class="flex-grow-1">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Bayar Sekarang
                            </button>
                        </form>
                        <form action="/kasir/transaction/clear" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-secondary"
                                    onclick="return confirm('Kosongkan keranjang?')">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Riwayat Transaksi -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Transaksi Hari Ini</h5>
                <span class="badge bg-light text-dark"><?= count($transactions) ?> transaksi</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($transactions)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2 mb-0">Belum ada transaksi hari ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kasir</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $trx): ?>
                                    <tr>
                                        <td><code class="small"><?= e($trx['transaction_code']) ?></code></td>
                                        <td><?= e($trx['cashier_name'] ?? '-') ?></td>
                                        <td class="text-end fw-bold text-success">
                                            <?= formatRupiah($trx['total_price']) ?>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <?= date('H:i', strtotime($trx['created_at'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#detail-<?= $trx['id'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Detail transaksi (collapsible) -->
                                    <?php
                                    $details = $transactionDetails[$trx['id']] ?? [];
                                    ?>
                                    <tr class="collapse" id="detail-<?= $trx['id'] ?>">
                                        <td colspan="5" class="bg-light p-3">
                                            <strong>Detail:</strong>
                                            <ul class="mb-0 mt-1">
                                                <?php foreach ($details as $detail): ?>
                                                    <li>
                                                        <?= e($detail['product_name']) ?> —
                                                        <?= $detail['quantity'] ?>x @
                                                        <?= formatRupiah($detail['subtotal'] / $detail['quantity']) ?>
                                                        = <strong><?= formatRupiah($detail['subtotal']) ?></strong>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>