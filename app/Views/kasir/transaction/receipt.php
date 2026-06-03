<!-- ============================================================ -->
<!-- STRUK TRANSAKSI -->
<!-- ============================================================ -->
<?php
$transaction ??= [];
$details     ??= [];
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-4">
        <div class="receipt-paper shadow-sm">
            <div class="receipt-body">
                <div class="text-center receipt-header">
                    <div class="receipt-logo">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h4 class="fw-bold mb-1"><?= e(APP_NAME) ?></h4>
                    <div class="small">Point of Sale</div>
                    <div class="small">Jl. Toko No. 3, Indonesia</div>
                    <div class="small">Telp. 0812-0000-0000</div>
                </div>

                <div class="receipt-divider"></div>

                <div class="receipt-meta">
                    <div class="d-flex justify-content-between">
                        <span>Kode</span>
                        <strong><?= e($transaction['transaction_code'] ?? '-') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kasir</span>
                        <span><?= e($transaction['cashier_name'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Waktu</span>
                        <span><?= date('d M Y H:i', strtotime($transaction['created_at'] ?? 'now')) ?></span>
                    </div>
                </div>

                <div class="receipt-divider"></div>

                <div class="receipt-items">
                    <?php foreach ($details as $item): ?>
                        <?php
                        $quantity = (int) $item['quantity'];
                        $subtotal = (float) $item['subtotal'];
                        $unitPrice = $quantity > 0 ? $subtotal / $quantity : 0;
                        ?>
                        <div class="receipt-item">
                            <div class="fw-semibold"><?= e($item['product_name']) ?></div>
                            <div class="d-flex justify-content-between">
                                <span><?= e((string) $quantity) ?> x <?= formatRupiah($unitPrice) ?></span>
                                <strong><?= formatRupiah($subtotal) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="receipt-divider"></div>

                <div class="receipt-total">
                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <strong><?= formatRupiah($transaction['total_price'] ?? 0) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Bayar</span>
                        <span><?= formatRupiah($transaction['paid_amount'] ?? 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between receipt-change">
                        <span>Kembalian</span>
                        <strong><?= formatRupiah($transaction['change_amount'] ?? 0) ?></strong>
                    </div>
                </div>

                <div class="receipt-divider"></div>

                <div class="text-center receipt-footer">
                    <div class="fw-semibold">Terima kasih sudah berbelanja</div>
                    <div class="small">Barang yang sudah dibeli tidak dapat dikembalikan.</div>
                    <div class="receipt-code mt-2"><?= e($transaction['transaction_code'] ?? '-') ?></div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3 no-print">
            <a href="<?= url('/kasir/transaction') ?>" class="btn btn-secondary flex-grow-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button type="button" class="btn btn-primary flex-grow-1" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>
</div>
