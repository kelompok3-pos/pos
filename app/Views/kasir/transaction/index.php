<!-- ============================================================ -->
<!-- TRANSAKSI PENJUALAN -->
<!-- ============================================================ -->
<?php
$products            ??= [];
$transactions        ??= [];
$cart                 ??= [];
$transactionDetails  ??= [];
$lastTransactionId   = $_SESSION['last_transaction_id'] ?? null;
?>

<div class="page-hero">
    <div class="page-title">
        <h2><i class="bi bi-cart-check text-success"></i> Transaksi Penjualan</h2>
        <p>Cari produk, atur jumlah, hitung kembalian, dan cetak struk dengan cepat.</p>
    </div>
    <a href="<?= url('/report/daily/export') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

<div class="row g-4">

    <!-- Kolom Kiri: Daftar Produk + Keranjang -->
    <div class="col-lg-5">

        <!-- Daftar Produk -->
        <div class="card modern-card mb-3">
            <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between gap-2 pt-4 px-4">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Pilih Produk</h5>
                <div class="position-relative product-search-wrap">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    <input type="search"
                           class="form-control form-control-sm ps-4"
                           id="productSearch"
                           placeholder="Cari produk...">
                </div>
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
                                    <tr class="product-row">
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
                                                <form action="<?= url('/kasir/transaction/add') ?>" method="POST" class="d-flex gap-1">
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
        <div class="card modern-card">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
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
                                        <td class="text-center align-middle">
                                            <form action="<?= url('/kasir/transaction/update') ?>" method="POST" class="d-flex justify-content-center gap-1">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= e((string) $item['product_id']) ?>">
                                                <input type="number"
                                                       name="quantity"
                                                       class="form-control form-control-sm"
                                                       value="<?= e((string) $item['quantity']) ?>"
                                                       min="1"
                                                       style="width: 64px;"
                                                       required>
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end align-middle fw-semibold text-success">
                                            <?= formatRupiah($item['subtotal']) ?>
                                        </td>
                                        <td class="align-middle">
                                            <form action="<?= url('/kasir/transaction/remove') ?>" method="POST">
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
                    <div class="card-footer bg-white">
                        <form action="<?= url('/kasir/transaction/checkout') ?>" method="POST" class="mb-2">
                            <?= csrf_field() ?>
                            <input type="hidden" id="cartTotal" value="<?= e((string) $grandTotal) ?>">
                            <div class="mb-2">
                                <label for="paidAmount" class="form-label small fw-semibold">Uang Dibayar</label>
                                <input type="number"
                                       class="form-control"
                                       id="paidAmount"
                                       name="paid_amount"
                                       min="<?= e((string) $grandTotal) ?>"
                                       step="100"
                                       required
                                       placeholder="Masukkan nominal pembayaran">
                            </div>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="<?= e((string) $grandTotal) ?>">
                                    Pas
                                </button>
                                <?php
                                $quickCashOptions = [20000, 50000, 100000, 150000, 200000];
                                foreach ($quickCashOptions as $amount):
                                    if ($amount < $grandTotal) {
                                        continue;
                                    }
                                ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="<?= $amount ?>">
                                        <?= formatRupiah($amount) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <div class="alert alert-info py-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Total</span>
                                    <strong><?= formatRupiah($grandTotal) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Kembalian</span>
                                    <strong id="changePreview">Rp 0</strong>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Bayar Sekarang
                            </button>
                        </form>
                        <div class="d-flex gap-2">
                            <?php if ($lastTransactionId): ?>
                                <a href="<?= url('/kasir/transaction/receipt') ?>?id=<?= e((string) $lastTransactionId) ?>"
                                   class="btn btn-outline-primary flex-grow-1">
                                    <i class="bi bi-printer"></i> Cetak Struk Terakhir
                                </a>
                            <?php endif; ?>
                            <form action="<?= url('/kasir/transaction/clear') ?>" method="POST" class="ms-auto">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-secondary"
                                        onclick="return confirm('Kosongkan keranjang?')">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($cart) && $lastTransactionId): ?>
            <div class="d-grid mt-3">
                <a href="<?= url('/kasir/transaction/receipt') ?>?id=<?= e((string) $lastTransactionId) ?>"
                   class="btn btn-outline-primary">
                    <i class="bi bi-printer"></i> Cetak Struk Transaksi Terakhir
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kolom Kanan: Riwayat Transaksi -->
    <div class="col-lg-7">
        <div class="card modern-card">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Transaksi Hari Ini</h5>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= url('/report/daily/export') ?>" class="btn btn-sm btn-light">
                        <i class="bi bi-download"></i> CSV
                    </a>
                    <span class="badge bg-light text-dark"><?= count($transactions) ?> transaksi</span>
                </div>
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
                                    <th class="text-end">Bayar</th>
                                    <th class="text-end">Kembali</th>
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
                                        <td class="text-end">
                                            <?= formatRupiah($trx['paid_amount'] ?? 0) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= formatRupiah($trx['change_amount'] ?? 0) ?>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <?= date('H:i', strtotime($trx['created_at'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary mb-1" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#detail-<?= $trx['id'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="<?= url('/kasir/transaction/receipt') ?>?id=<?= e((string) $trx['id']) ?>"
                                               class="btn btn-sm btn-outline-secondary mb-1">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- Detail transaksi (collapsible) -->
                                    <?php
                                    $details = $transactionDetails[$trx['id']] ?? [];
                                    ?>
                                    <tr class="collapse" id="detail-<?= $trx['id'] ?>">
                                        <td colspan="7" class="bg-light p-3">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paidInput = document.getElementById('paidAmount');
    const totalInput = document.getElementById('cartTotal');
    const changePreview = document.getElementById('changePreview');
    const productSearch = document.getElementById('productSearch');
    const productRows = document.querySelectorAll('.product-row');

    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    });

    function updateChangePreview() {
        const total = Number(totalInput.value || 0);
        const paid = Number(paidInput.value || 0);
        const change = Math.max(paid - total, 0);
        changePreview.textContent = formatter.format(change);
    }

    if (paidInput && totalInput && changePreview) {
        paidInput.addEventListener('input', updateChangePreview);

        document.querySelectorAll('.quick-cash').forEach(function (button) {
            button.addEventListener('click', function () {
                paidInput.value = button.dataset.amount || '';
                updateChangePreview();
            });
        });
    }

    if (productSearch) {
        productSearch.addEventListener('input', function () {
            const keyword = productSearch.value.toLowerCase();
            productRows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });
        });
    }
});
</script>
