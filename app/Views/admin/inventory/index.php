<?php
$products = $products ?? [];
$lowStock = $lowStock ?? [];
$movements = $movements ?? [];

$totalProducts = count($products);
$totalStock = 0;
$totalLowStock = count($lowStock);
$totalMovements = count($movements);

foreach ($products as $product) {
    $totalStock += (int) ($product['stock'] ?? 0);
}
?>

<style>
.sa-inventory {
    width: 100%;
}

.sa-inventory * {
    box-sizing: border-box;
}

.sa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.sa-title .sa-eyebrow {
    display: inline-flex;
    margin-bottom: .4rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    color: #378add;
    text-transform: uppercase;
}

.sa-title h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.sa-title p {
    margin: .35rem 0 0;
    font-size: .875rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-summary-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    padding: 1rem;
    min-height: 112px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-summary-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-summary-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: .75rem;
    font-size: 1.15rem;
}

.sa-icon-blue {
    color: #378add;
    background: rgba(55, 138, 221, .11);
}

.sa-icon-green {
    color: #1d9e75;
    background: rgba(29, 158, 117, .11);
}

.sa-icon-orange {
    color: #ba7517;
    background: rgba(186, 117, 23, .12);
}

.sa-icon-red {
    color: #e24b4a;
    background: rgba(226, 75, 74, .12);
}

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .3rem;
}

.sa-summary-value {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    line-height: 1.25;
}

.sa-summary-sub {
    display: block;
    margin-top: .3rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-main-grid {
    display: grid;
    grid-template-columns: minmax(320px, .85fr) minmax(0, 1.15fr);
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: start;
}

.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sa-panel-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
}

.sa-panel-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .45rem;
}

.sa-panel-subtitle {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-body {
    padding: 1.1rem;
}

.sa-form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: .85rem;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-label {
    display: block;
    margin-bottom: .45rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-input-wrap {
    position: relative;
}

.sa-input-icon {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    font-size: 1rem;
    pointer-events: none;
}

.sa-form-input {
    width: 100%;
    height: 44px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .8rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-submit-button {
    width: 100%;
    height: 42px;
    border: 0;
    border-radius: .75rem;
    background: #378add;
    color: #fff;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-submit-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-low-list {
    padding: .35rem 1rem .75rem;
}

.sa-low-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .85rem 0;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .07));
}

.sa-low-item:last-child {
    border-bottom: none;
}

.sa-low-product {
    min-width: 0;
}

.sa-low-name {
    font-size: .85rem;
    font-weight: 750;
    color: var(--color-text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-low-meta {
    margin-top: .15rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .32rem .65rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}

.sa-badge-danger {
    background: #fef3f2;
    color: #b42318;
}

.sa-badge-success {
    background: #eaf7ef;
    color: #247244;
}

.sa-badge-blue {
    background: #e6f1fb;
    color: #185fa5;
}

.sa-badge-muted {
    background: #f3f4f6;
    color: #6b7280;
}

.sa-empty {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty i {
    display: block;
    margin-bottom: .5rem;
    font-size: 2rem;
    color: #9ca3af;
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .825rem;
}

.sa-table-stock {
    min-width: 520px;
}

.sa-table-movement {
    min-width: 980px;
    table-layout: fixed;
}

.sa-table th {
    padding: .75rem 1rem;
    text-align: left;
    font-size: .72rem;
    font-weight: 800;
    color: var(--color-text-secondary, #6b7280);
    background: var(--color-background-secondary, #f9fafb);
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    white-space: nowrap;
}

.sa-table td {
    padding: .9rem 1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .06));
    color: var(--color-text-primary, #111827);
    vertical-align: middle;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-product-cell {
    display: flex;
    align-items: center;
    gap: .7rem;
    min-width: 0;
}

.sa-product-icon {
    width: 36px;
    height: 36px;
    border-radius: .8rem;
    background: rgba(55, 138, 221, .11);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
}

.sa-product-name {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
}

.sa-stock-value {
    font-weight: 800;
    color: var(--color-text-primary, #111827);
}

.sa-stock-muted {
    color: var(--color-text-secondary, #6b7280);
    font-size: .75rem;
}

.sa-movement-type {
    text-transform: capitalize;
}

.sa-type-in {
    background: #eaf7ef;
    color: #247244;
}

.sa-type-out {
    background: #fef3f2;
    color: #b42318;
}

.sa-time-cell {
    color: var(--color-text-secondary, #6b7280);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.sa-number-cell {
    font-weight: 750;
    white-space: nowrap;
}

.sa-user-cell {
    color: var(--color-text-secondary, #6b7280);
    font-weight: 700;
    white-space: nowrap;
}

@media (max-width: 1100px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-main-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-summary-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="sa-inventory">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Inventory</span>
            <h2>
                <i class="ti ti-packages" aria-hidden="true"></i>
                Inventory / Stock
            </h2>
            <p>Kelola perubahan stok dan pantau produk yang perlu segera diisi ulang.</p>
        </div>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-blue">
                <i class="ti ti-package" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Produk</span>
            <div class="sa-summary-value"><?= number_format($totalProducts) ?></div>
            <span class="sa-summary-sub">produk terdaftar</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-green">
                <i class="ti ti-box-seam" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Stok</span>
            <div class="sa-summary-value"><?= number_format($totalStock) ?></div>
            <span class="sa-summary-sub">akumulasi seluruh produk</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon <?= $totalLowStock > 0 ? 'sa-icon-red' : 'sa-icon-green' ?>">
                <i class="ti ti-alert-triangle" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Stok Menipis</span>
            <div class="sa-summary-value"><?= number_format($totalLowStock) ?></div>
            <span class="sa-summary-sub"><?= $totalLowStock > 0 ? 'perlu segera dicek' : 'semua aman' ?></span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-orange">
                <i class="ti ti-history" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Riwayat Movement</span>
            <div class="sa-summary-value"><?= number_format($totalMovements) ?></div>
            <span class="sa-summary-sub">perubahan stok tercatat</span>
        </div>
    </div>

    <div class="sa-main-grid">

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-adjustments" aria-hidden="true"></i>
                        Penyesuaian Stok
                    </div>
                    <div class="sa-panel-subtitle">Tambah atau kurangi stok produk.</div>
                </div>
            </div>

            <form action="<?= url('/inventory/adjust') ?>" method="POST">
                <div class="sa-form-body">
                    <?= csrf_field() ?>

                    <div class="sa-form-grid">
                        <div class="sa-form-group">
                            <label class="sa-form-label">Produk</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-package sa-input-icon" aria-hidden="true"></i>
                                <select name="product_id" class="sa-form-input" required>
                                    <option value="">Pilih produk</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= (int) $product['id'] ?>">
                                            <?= e($product['name']) ?> (stok: <?= (int) $product['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="sa-form-group">
                            <label class="sa-form-label">Tipe Movement</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-arrows-exchange sa-input-icon" aria-hidden="true"></i>
                                <select name="movement_type" class="sa-form-input" required>
                                    <option value="in">Tambah Stok</option>
                                    <option value="out">Kurangi Stok</option>
                                </select>
                            </div>
                        </div>

                        <div class="sa-form-group">
                            <label class="sa-form-label">Jumlah</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-number sa-input-icon" aria-hidden="true"></i>
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    class="sa-form-input"
                                    placeholder="Jumlah"
                                    required
                                >
                            </div>
                        </div>

                        <div class="sa-form-group">
                            <label class="sa-form-label">Catatan</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-notes sa-input-icon" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    name="note"
                                    class="sa-form-input"
                                    placeholder="Alasan perubahan stok"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="sa-submit-button">
                            <i class="ti ti-device-floppy" aria-hidden="true"></i>
                            Perbarui Stok
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                        Peringatan Stok Menipis
                    </div>
                    <div class="sa-panel-subtitle">Produk yang sudah mencapai batas minimum.</div>
                </div>
            </div>

            <?php if (!empty($lowStock)): ?>
                <div class="sa-low-list">
                    <?php foreach ($lowStock as $product): ?>
                        <div class="sa-low-item">
                            <div class="sa-low-product">
                                <div class="sa-low-name"><?= e($product['name']) ?></div>
                                <div class="sa-low-meta">Perlu segera restock</div>
                            </div>

                            <span class="sa-badge sa-badge-danger">
                                <?= (int) $product['stock'] ?> / min <?= (int) $product['minimum_stock'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sa-empty">
                    <i class="ti ti-circle-check" aria-hidden="true"></i>
                    Semua stok masih dalam batas aman.
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-list-details" aria-hidden="true"></i>
                    Stok Saat Ini
                </div>
                <div class="sa-panel-subtitle">Daftar produk dan jumlah stok terbaru.</div>
            </div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table sa-table-stock">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="sa-product-cell">
                                        <div class="sa-product-icon">
                                            <i class="ti ti-package" aria-hidden="true"></i>
                                        </div>

                                        <div>
                                            <div class="sa-product-name"><?= e($product['name']) ?></div>
                                            <div class="sa-stock-muted">ID #<?= (int) $product['id'] ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="sa-badge <?= (int) $product['stock'] <= 0 ? 'sa-badge-danger' : 'sa-badge-blue' ?>">
                                        <?= number_format((int) $product['stock']) ?> pcs
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="sa-empty">
                                Belum ada produk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-clock-edit" aria-hidden="true"></i>
                    Riwayat Perubahan Stok
                </div>
                <div class="sa-panel-subtitle">Catatan movement stok masuk dan keluar.</div>
            </div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table sa-table-movement">
                <colgroup>
                    <col style="width: 170px">
                    <col style="width: 220px">
                    <col style="width: 120px">
                    <col style="width: 100px">
                    <col style="width: 100px">
                    <col style="width: 100px">
                    <col style="width: 170px">
                </colgroup>

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
                            <?php $isIn = ($row['movement_type'] ?? '') === 'in'; ?>
                            <tr>
                                <td class="sa-time-cell"><?= e($row['created_at'] ?? '-') ?></td>
                                <td>
                                    <div class="sa-product-name"><?= e($row['product_name'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="sa-badge <?= $isIn ? 'sa-type-in' : 'sa-type-out' ?>">
                                        <?= $isIn ? 'Masuk' : 'Keluar' ?>
                                    </span>
                                </td>
                                <td class="sa-number-cell"><?= number_format((int) ($row['quantity'] ?? 0)) ?></td>
                                <td class="sa-number-cell"><?= number_format((int) ($row['stock_before'] ?? 0)) ?></td>
                                <td class="sa-number-cell"><?= number_format((int) ($row['stock_after'] ?? 0)) ?></td>
                                <td class="sa-user-cell"><?= e($row['user_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="sa-empty">
                                Belum ada riwayat perubahan stok.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>