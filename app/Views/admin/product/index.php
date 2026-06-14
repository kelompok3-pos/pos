<?php
$products = $products ?? [];

$totalProducts = count($products);
$totalActive = 0;
$totalInactive = 0;
$totalLowStock = 0;
$totalStock = 0;

foreach ($products as $product) {
    $stock = (int) ($product['stock'] ?? 0);
    $totalStock += $stock;

    if (($product['status'] ?? '') === 'active') {
        $totalActive++;
    } else {
        $totalInactive++;
    }

    if ($stock <= 5) {
        $totalLowStock++;
    }
}
?>

<style>
.sa-product-page {
    width: 100%;
}

.sa-product-page * {
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

.sa-add-link {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    border: 0;
    background: #378add;
    color: #fff;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-add-link:hover {
    background: #2f7bc6;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
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

.sa-tools-body {
    padding: 1rem 1.1rem;
}

.sa-tools-grid {
    display: grid;
    grid-template-columns: minmax(260px, 1fr) minmax(320px, 1.2fr);
    gap: 1rem;
    align-items: start;
}

.sa-search-wrap {
    position: relative;
}

.sa-search-icon {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    pointer-events: none;
}

.sa-search-input {
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

.sa-search-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-import-box {
    border: 1px dashed rgba(55, 138, 221, .35);
    background: rgba(55, 138, 221, .045);
    border-radius: 1rem;
    padding: 1rem;
}

.sa-import-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: .75rem;
    align-items: center;
}

.sa-file-input {
    width: 100%;
    min-height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: #fff;
    color: var(--color-text-secondary, #6b7280);
    border-radius: .75rem;
    padding: .55rem .75rem;
    font-size: .82rem;
}

.sa-import-button {
    height: 42px;
    border: 1px solid rgba(55, 138, 221, .25);
    border-radius: .75rem;
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-import-button:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
}

.sa-help-text {
    display: block;
    margin-top: .55rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .72rem;
    font-weight: 650;
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    min-width: 920px;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: .825rem;
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

.sa-number {
    color: var(--color-text-secondary, #6b7280);
    font-weight: 700;
}

.sa-product-cell {
    display: flex;
    align-items: center;
    gap: .7rem;
    min-width: 0;
}

.sa-product-icon {
    width: 38px;
    height: 38px;
    border-radius: .85rem;
    background: rgba(55, 138, 221, .11);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1rem;
}

.sa-product-name {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-money {
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    white-space: nowrap;
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

.sa-badge-success {
    background: #eaf7ef;
    color: #247244;
}

.sa-badge-danger {
    background: #fef3f2;
    color: #b42318;
}

.sa-badge-muted {
    background: #f3f4f6;
    color: #6b7280;
}

.sa-badge-blue {
    background: #e6f1fb;
    color: #185fa5;
}

.sa-actions {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    flex-wrap: wrap;
}

.sa-action-link-sm,
.sa-action-button-sm {
    height: 34px;
    padding: 0 .7rem;
    border-radius: .65rem;
    font-size: .78rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}

.sa-action-link-sm {
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
}

.sa-action-link-sm:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-action-button-sm {
    border: 1px solid rgba(0, 0, 0, .10);
    background: #fff;
    color: #4b5563;
}

.sa-action-button-sm:hover {
    background: #f8fafc;
    border-color: rgba(55, 138, 221, .35);
    color: #2f7bc6;
}

.sa-action-danger {
    border-color: rgba(180, 35, 24, .18);
    background: #fff;
    color: #b42318;
}

.sa-action-danger:hover {
    background: #fef3f2;
    border-color: rgba(180, 35, 24, .35);
    color: #b42318;
}

.sa-inline-form {
    display: inline;
    margin: 0;
}

.sa-empty {
    padding: 2.5rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty i {
    display: block;
    margin-bottom: .5rem;
    font-size: 2rem;
    color: #9ca3af;
}

.sa-info-alert {
    padding: 1rem 1.1rem;
    border: 1px solid rgba(55, 138, 221, .16);
    border-radius: 1rem;
    background: rgba(55, 138, 221, .055);
    color: var(--color-text-secondary, #6b7280);
    display: flex;
    align-items: center;
    gap: .6rem;
    font-size: .86rem;
    font-weight: 700;
}

.sa-info-alert a {
    color: #2f7bc6;
    font-weight: 800;
    text-decoration: none;
}

.sa-info-alert a:hover {
    text-decoration: underline;
}

@media (max-width: 1100px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-tools-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-add-link {
        width: 100%;
    }

    .sa-summary-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .sa-import-form {
        grid-template-columns: 1fr;
    }

    .sa-import-button {
        width: 100%;
    }
}
</style>

<div class="sa-product-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Product Management</span>
            <h2>
                <i class="ti ti-package" aria-hidden="true"></i>
                Daftar Produk
            </h2>
            <p>Kelola nama, harga, stok, dan status produk toko.</p>
        </div>

        <a href="<?= url('/admin/product/create') ?>" class="sa-add-link">
            <i class="ti ti-plus" aria-hidden="true"></i>
            Tambah Produk
        </a>
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
                <i class="ti ti-circle-check" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Produk Aktif</span>
            <div class="sa-summary-value"><?= number_format($totalActive) ?></div>
            <span class="sa-summary-sub">siap dijual</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-red">
                <i class="ti ti-alert-triangle" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Stok Rendah</span>
            <div class="sa-summary-value"><?= number_format($totalLowStock) ?></div>
            <span class="sa-summary-sub">stok kurang/sama dengan 5</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-orange">
                <i class="ti ti-box-seam" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Stok</span>
            <div class="sa-summary-value"><?= number_format($totalStock) ?></div>
            <span class="sa-summary-sub">akumulasi seluruh produk</span>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-tool" aria-hidden="true"></i>
                    Tools Produk
                </div>
                <div class="sa-panel-subtitle">Cari produk atau import data produk melalui CSV.</div>
            </div>
        </div>

        <div class="sa-tools-body">
            <div class="sa-tools-grid">
                <div>
                    <label class="sa-panel-subtitle" for="managementProductSearch">
                        Pencarian Produk
                    </label>

                    <div class="sa-search-wrap" style="margin-top:.45rem">
                        <i class="ti ti-search sa-search-icon" aria-hidden="true"></i>
                        <input
                            type="search"
                            id="managementProductSearch"
                            class="sa-search-input"
                            placeholder="Cari nama produk..."
                        >
                    </div>
                </div>

                <div class="sa-import-box">
                    <form
                        action="<?= url('/admin/product/import') ?>"
                        method="POST"
                        enctype="multipart/form-data"
                        class="sa-import-form"
                    >
                        <?= csrf_field() ?>

                        <input
                            type="file"
                            name="csv"
                            class="sa-file-input"
                            accept=".csv"
                            required
                        >

                        <button class="sa-import-button" type="submit">
                            <i class="ti ti-upload" aria-hidden="true"></i>
                            Import CSV
                        </button>
                    </form>

                    <small class="sa-help-text">
                        Header CSV: name, price, stock, minimum_stock
                    </small>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="sa-info-alert">
            <i class="ti ti-info-circle" aria-hidden="true"></i>
            <span>
                Belum ada produk.
                <a href="<?= url('/admin/product/create') ?>">Tambah produk pertama</a>.
            </span>
        </div>
    <?php else: ?>
        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-list-details" aria-hidden="true"></i>
                        Tabel Produk
                    </div>
                    <div class="sa-panel-subtitle">Daftar produk, harga, stok, status, dan aksi pengelolaan.</div>
                </div>
            </div>

            <div class="sa-table-wrap">
                <table class="sa-table">
                    <colgroup>
                        <col style="width: 70px">
                        <col style="width: 280px">
                        <col style="width: 160px">
                        <col style="width: 110px">
                        <col style="width: 130px">
                        <col style="width: 260px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($products as $i => $product): ?>
                            <?php
                            $stock = (int) ($product['stock'] ?? 0);
                            $isActive = ($product['status'] ?? '') === 'active';
                            ?>
                            <tr class="management-product-row">
                                <td class="sa-number">
                                    <?= $i + 1 ?>
                                </td>

                                <td>
                                    <div class="sa-product-cell">
                                        <div class="sa-product-icon">
                                            <i class="ti ti-package" aria-hidden="true"></i>
                                        </div>

                                        <div class="sa-product-name">
                                            <?= e($product['name'] ?? '-') ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="sa-money">
                                    <?= formatRupiah($product['price'] ?? 0) ?>
                                </td>

                                <td>
                                    <span class="sa-badge <?= $stock <= 5 ? 'sa-badge-danger' : 'sa-badge-success' ?>">
                                        <?= number_format($stock) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="sa-badge <?= $isActive ? 'sa-badge-success' : 'sa-badge-muted' ?>">
                                        <?= e($product['status'] ?? '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="sa-actions">
                                        <a
                                            href="<?= url('/admin/product/edit') ?>?id=<?= e((string) ($product['id'] ?? '')) ?>"
                                            class="sa-action-link-sm"
                                        >
                                            <i class="ti ti-edit" aria-hidden="true"></i>
                                            Edit
                                        </a>

                                        <form action="<?= url('/admin/product/delete') ?>" method="POST" class="sa-inline-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= e((string) ($product['id'] ?? '')) ?>">

                                            <button
                                                type="submit"
                                                class="sa-action-button-sm sa-action-danger"
                                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                            >
                                                <i class="ti ti-trash" aria-hidden="true"></i>
                                                Hapus
                                            </button>
                                        </form>

                                        <form action="<?= url('/admin/product/status') ?>" method="POST" class="sa-inline-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= e((string) ($product['id'] ?? '')) ?>">
                                            <input type="hidden" name="status" value="<?= $isActive ? 'inactive' : 'active' ?>">

                                            <button class="sa-action-button-sm" type="submit">
                                                <?= $isActive ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
document.getElementById('managementProductSearch')?.addEventListener('input', function () {
    const value = this.value.toLowerCase();

    document.querySelectorAll('.management-product-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});
</script>