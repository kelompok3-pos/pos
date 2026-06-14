<?php
$rows = $rows ?? [];
$storeOptions = $storeOptions ?? [];

$from = $from ?? date('Y-m-01');
$to = $to ?? date('Y-m-d');
$selectedStore = $selectedStore ?? null;

$totals = $totals ?? [
    'stores' => 0,
    'transactions' => 0,
    'revenue' => 0,
    'expenses' => 0,
    'net' => 0,
];

$exportUrl = url('/superadmin/reports/export?' . http_build_query([
    'from' => $from,
    'to' => $to,
    'store_id' => $selectedStore,
]));
?>

<style>
.sa-reports {
    width: 100%;
}

.sa-reports * {
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
}

.sa-title p {
    margin: .35rem 0 0;
    font-size: .875rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-export-link {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    transition: background-color .18s ease, transform .18s ease, border-color .18s ease;
}

.sa-export-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
    transform: translateY(-1px);
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
}

.sa-panel-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
}

.sa-panel-subtitle {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-filter-body {
    padding: 1rem 1.1rem;
}

.sa-filter-grid {
    display: grid;
    grid-template-columns: minmax(150px, 1fr) minmax(150px, 1fr) minmax(220px, 1.3fr) minmax(140px, .75fr);
    gap: .85rem;
    align-items: end;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-label {
    display: block;
    margin-bottom: .4rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-input {
    width: 100%;
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-filter-button {
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
    transition: background-color .18s ease, transform .18s ease, box-shadow .18s ease;
}

.sa-filter-button:hover {
    background: #2f7bc6;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(55, 138, 221, .22);
}

.sa-filter-button:active {
    transform: translateY(0);
    box-shadow: none;
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
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.sa-summary-card:hover {
    transform: translateY(-2px);
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .35rem;
}

.sa-summary-value {
    font-size: 1.22rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    line-height: 1.25;
    word-break: break-word;
}

.sa-summary-sub {
    display: block;
    margin-top: .35rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-icon-blue {
    color: #378add;
}

.sa-icon-green {
    color: #247244;
}

.sa-icon-orange {
    color: #ba7517;
}

.sa-icon-red {
    color: #b42318;
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .825rem;
    min-width: 920px;
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
    white-space: nowrap;
}

.sa-table tbody tr {
    transition: background-color .16s ease;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-table tfoot th {
    padding: .9rem 1rem;
    background: var(--color-background-secondary, #f9fafb);
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    color: var(--color-text-primary, #111827);
    font-size: .8rem;
    font-weight: 800;
    white-space: nowrap;
}

.sa-store-name {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
}

.sa-status-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .65rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1;
    text-transform: capitalize;
}

.sa-status-dot {
    width: .45rem;
    height: .45rem;
    border-radius: 50%;
}

.sa-status-active {
    background: #eaf7ef;
    color: #247244;
}

.sa-status-active .sa-status-dot {
    background: #22c55e;
}

.sa-status-muted {
    background: #f3f4f6;
    color: #6b7280;
}

.sa-status-muted .sa-status-dot {
    background: #9ca3af;
}

.sa-count,
.sa-money {
    font-weight: 700;
}

.sa-net-pos {
    color: #247244;
    font-weight: 800;
}

.sa-net-neg {
    color: #b42318;
    font-weight: 800;
}

.sa-margin-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .28rem .55rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    background: #f3f4f6;
    color: #4b5563;
}

.sa-margin-good {
    background: #eaf7ef;
    color: #247244;
}

.sa-margin-bad {
    background: #fef3f2;
    color: #b42318;
}

.sa-empty {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

@media (max-width: 1100px) {
    .sa-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-export-link {
        width: 100%;
    }

    .sa-filter-grid,
    .sa-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sa-reports">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Global Analytics</span>
            <h2>Laporan Lintas Toko</h2>
            <p>Bandingkan omzet, pengeluaran, dan net seluruh cabang.</p>
        </div>

        <a class="sa-export-link" href="<?= $exportUrl ?>">
            <i class="ti ti-download" aria-hidden="true"></i>
            Export Excel
        </a>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div class="sa-panel-title">Filter Laporan</div>
            <div class="sa-panel-subtitle">Atur rentang tanggal dan toko yang ingin dianalisis.</div>
        </div>

        <div class="sa-filter-body">
            <form method="GET" action="<?= url('/superadmin/reports') ?>" class="sa-filter-grid">
                <div class="sa-form-group">
                    <label class="sa-form-label">Dari tanggal</label>
                    <input
                        class="sa-form-input"
                        type="date"
                        name="from"
                        value="<?= e($from) ?>"
                    >
                </div>

                <div class="sa-form-group">
                    <label class="sa-form-label">Sampai tanggal</label>
                    <input
                        class="sa-form-input"
                        type="date"
                        name="to"
                        value="<?= e($to) ?>"
                    >
                </div>

                <div class="sa-form-group">
                    <label class="sa-form-label">Toko</label>
                    <select class="sa-form-input" name="store_id">
                        <option value="">Semua toko</option>

                        <?php foreach ($storeOptions as $store): ?>
                            <option
                                value="<?= (int) $store['id'] ?>"
                                <?= $selectedStore === (int) $store['id'] ? 'selected' : '' ?>
                            >
                                <?= e($store['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sa-form-group">
                    <button type="submit" class="sa-filter-button">
                        <i class="ti ti-filter" aria-hidden="true"></i>
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <span class="sa-summary-label">Transaksi</span>
            <div class="sa-summary-value sa-icon-blue">
                <?= number_format((int) $totals['transactions']) ?>
            </div>
            <span class="sa-summary-sub"><?= e($from) ?> hingga <?= e($to) ?></span>
        </div>

        <div class="sa-summary-card">
            <span class="sa-summary-label">Total Omzet</span>
            <div class="sa-summary-value sa-icon-green">
                <?= formatRupiah($totals['revenue']) ?>
            </div>
            <span class="sa-summary-sub">semua transaksi</span>
        </div>

        <div class="sa-summary-card">
            <span class="sa-summary-label">Pengeluaran</span>
            <div class="sa-summary-value sa-icon-orange">
                <?= formatRupiah($totals['expenses']) ?>
            </div>
            <span class="sa-summary-sub">seluruh kategori</span>
        </div>

        <div class="sa-summary-card">
            <span class="sa-summary-label">Estimasi Net</span>
            <div class="sa-summary-value <?= (float) $totals['net'] >= 0 ? 'sa-net-pos' : 'sa-net-neg' ?>">
                <?= formatRupiah($totals['net']) ?>
            </div>
            <span class="sa-summary-sub">omzet dikurangi pengeluaran</span>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div class="sa-panel-title">Detail Performa Toko</div>
            <div class="sa-panel-subtitle">Ringkasan transaksi, omzet, pengeluaran, net, dan margin.</div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>Toko</th>
                        <th>Status</th>
                        <th>Transaksi</th>
                        <th>Omzet</th>
                        <th>Pengeluaran</th>
                        <th>Net</th>
                        <th>Margin</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $revenue = (float) ($row['revenue'] ?? 0);
                        $net = (float) ($row['net'] ?? 0);
                        $margin = $revenue > 0 ? ($net / $revenue) * 100 : 0;
                        $isActive = ($row['status'] ?? '') === 'active';
                        ?>
                        <tr>
                            <td>
                                <span class="sa-store-name">
                                    <?= e($row['store_name'] ?? '-') ?>
                                </span>
                            </td>

                            <td>
                                <span class="sa-status-pill <?= $isActive ? 'sa-status-active' : 'sa-status-muted' ?>">
                                    <span class="sa-status-dot"></span>
                                    <?= e($row['status'] ?? '-') ?>
                                </span>
                            </td>

                            <td class="sa-count">
                                <?= number_format((int) ($row['transaction_count'] ?? 0)) ?>
                            </td>

                            <td class="sa-money">
                                <?= formatRupiah($row['revenue'] ?? 0) ?>
                            </td>

                            <td class="sa-money">
                                <?= formatRupiah($row['expenses'] ?? 0) ?>
                            </td>

                            <td class="<?= $net >= 0 ? 'sa-net-pos' : 'sa-net-neg' ?>">
                                <?= formatRupiah($row['net'] ?? 0) ?>
                            </td>

                            <td>
                                <span class="sa-margin-pill <?= $margin >= 0 ? 'sa-margin-good' : 'sa-margin-bad' ?>">
                                    <?= number_format($margin, 1) ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="7" class="sa-empty">
                                Tidak ada data pada rentang ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th></th>
                        <th><?= number_format((int) $totals['transactions']) ?></th>
                        <th><?= formatRupiah($totals['revenue']) ?></th>
                        <th><?= formatRupiah($totals['expenses']) ?></th>
                        <th class="<?= (float) $totals['net'] >= 0 ? 'sa-net-pos' : 'sa-net-neg' ?>">
                            <?= formatRupiah($totals['net']) ?>
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
