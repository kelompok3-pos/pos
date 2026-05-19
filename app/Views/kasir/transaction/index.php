<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Buat Transaksi</h5>
            </div>
            <div class="card-body">
                <form action="/kasir/transaction/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Pilih Produk <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" 
                                        data-price="<?= $product['price'] ?>"
                                        data-stock="<?= $product['stock'] ?>">
                                    <?= e($product['name']) ?> — <?= formatRupiah($product['price']) ?> (Stok: <?= $product['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               min="1" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Harga</label>
                        <div class="form-control bg-light fw-bold text-success" id="totalPrice">
                            Rp 0
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-lg"></i> Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Hari Ini -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Hari Ini</h5>
                <span class="badge bg-light text-dark"><?= count($transactions) ?> transaksi</span>
            </div>
            <div class="table-responsive">
                <?php if (empty($transactions)): ?>
                    <div class="card-body text-center text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">Belum ada transaksi hari ini.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $i => $trx): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= e($trx['product_name']) ?></td>
                                    <td><?= $trx['quantity'] ?></td>
                                    <td><?= formatRupiah($trx['price']) ?></td>
                                    <td class="fw-bold"><?= formatRupiah($trx['total_price']) ?></td>
                                    <td class="text-muted small"><?= date('H:i', strtotime($trx['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Script: Hitung total harga otomatis -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const totalDisplay  = document.getElementById('totalPrice');

    function updateTotal() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price    = parseInt(selected.dataset.price || 0);
        const quantity = parseInt(quantityInput.value || 0);
        const total    = price * quantity;

        totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    productSelect.addEventListener('change', updateTotal);
    quantityInput.addEventListener('input', updateTotal);
});
</script>
