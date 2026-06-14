<?php
$transactions = $transactions ?? [];
$cashiers = $cashiers ?? [];
$topProducts = $topProducts ?? [];
$from = $from ?? date('Y-m-d');
$to = $to ?? date('Y-m-d');
$dailyRevenue = $dailyRevenue ?? 0;
$weeklyRevenue = $weeklyRevenue ?? 0;
$monthlyRevenue = $monthlyRevenue ?? 0;

$reportRevenue = array_reduce(
    $transactions,
    fn(float $sum, array $row): float => $sum + (float) ($row['total_price'] ?? 0),
    0.0
);

$totalTransactions = count($transactions);
$totalCashiers = count($cashiers);
$totalTopProducts = count($topProducts);
?>

<style>
.sa-report-page {
    width: 100%;
}

.sa-report-page * {
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

.sa-filter-panel,
.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sa-filter-body {
    padding: 1rem 1.1rem;
}

.sa-filter-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(180px, 1fr)) auto auto auto;
    gap: .75rem;
    align-items: end;
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
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-filter-button,
.sa-export-link,
.sa-print-button {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.sa-filter-button {
    border: 0;
    background: #378add;
    color: #fff;
}

.sa-filter-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-export-link,
.sa-print-button {
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
}

.sa-export-link:hover,
.sa-print-button:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-info-strip {
    margin-bottom: 1rem;
    padding: 1rem 1.1rem;
    border: 1px solid rgba(55, 138, 221, .16);
    border-radius: 1rem;
    background: rgba(55, 138, 221, .055);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.sa-info-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    color: var(--color-text-primary, #111827);
    font-size: .88rem;
    font-weight: 800;
}

.sa-info-icon {
    width: 36px;
    height: 36px;
    border-radius: .75rem;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
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

.sa-icon-purple {
    color: #8b5cf6;
    background: rgba(139, 92, 246, .12);
}

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .3rem;
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
    margin-top: .3rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(320px, .75fr);
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: start;
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

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    min-width: 760px;
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

.sa-code {
    display: inline-flex;
    max-width: 150px;
    padding: .38rem .55rem;
    border-radius: .65rem;
    background: rgba(55, 138, 221, .12);
    color: #185fa5;
    font-size: .76rem;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sa-cashier {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-money {
    color: #1d9e75;
    font-weight: 850;
    white-space: nowrap;
}

.sa-date {
    color: var(--color-text-secondary, #6b7280);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.sa-list {
    padding: .35rem 1rem .75rem;
}

.sa-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .85rem 0;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .07));
}

.sa-list-item:last-child {
    border-bottom: none;
}

.sa-list-main {
    min-width: 0;
}

.sa-list-title {
    font-size: .85rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-list-sub {
    margin-top: .15rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-list-value {
    color: #1d9e75;
    font-size: .84rem;
    font-weight: 850;
    white-space: nowrap;
}

.sa-rank {
    width: 34px;
    height: 34px;
    border-radius: .75rem;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    font-weight: 900;
    flex: 0 0 auto;
}

.sa-product-item {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr) auto;
    align-items: center;
    gap: .75rem;
}

.sa-empty {
    padding: 2.4rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty i {
    display: block;
    margin-bottom: .5rem;
    font-size: 2rem;
    color: #9ca3af;
}

@media (max-width: 1100px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-content-grid {
        grid-template-columns: 1fr;
    }

    .sa-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-summary-grid,
    .sa-filter-grid {
        grid-template-columns: 1fr;
    }

    .sa-filter-button,
    .sa-export-link,
    .sa-print-button {
        width: 100%;
    }

    .sa-info-strip {
        flex-direction: column;
        align-items: flex-start;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media print {
    .sa-filter-panel,
    .sa-export-link,
    .sa-print-button {
        display: none !important;
    }

    .sa-report-page {
        color: #111827;
    }

    .sa-panel,
    .sa-summary-card,
    .sa-info-strip {
        box-shadow: none !important;
        border-color: #d1d5db !important;
    }
}
</style>

<div class="sa-report-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Reports</span>
            <h2>
                <i class="ti ti-chart-bar" aria-hidden="true"></i>
                Reports
            </h2>
            <p>
                <?= isSuperAdmin() ? 'All stores' : 'Own store only' ?> sales and cashier performance.
            </p>
        </div>
    </div>

    <div class="sa-filter-panel">
        <form method="GET" action="<?= url('/reports') ?>">
            <div class="sa-filter-body">
                <div class="sa-filter-grid">
                    <div class="sa-form-group">
                        <label class="sa-form-label" for="from">Dari</label>
                        <div class="sa-input-wrap">
                            <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                            <input
                                type="date"
                                id="from"
                                name="from"
                                class="sa-form-input"
                                value="<?= e($from) ?>"
                            >
                        </div>
                    </div>

                    <div class="sa-form-group">
                        <label class="sa-form-label" for="to">Sampai</label>
                        <div class="sa-input-wrap">
                            <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                            <input
                                type="date"
                                id="to"
                                name="to"
                                class="sa-form-input"
                                value="<?= e($to) ?>"
                            >
                        </div>
                    </div>

                    <button class="sa-filter-button" type="submit">
                        <i class="ti ti-filter" aria-hidden="true"></i>
                        Filter
                    </button>

                    <a
                        class="sa-export-link"
                        href="<?= url('/reports/export') ?>?from=<?= e($from) ?>&to=<?= e($to) ?>"
                    >
                        <i class="ti ti-download" aria-hidden="true"></i>
                        Export CSV
                    </a>

                    <button type="button" class="sa-print-button" onclick="window.print()">
                        <i class="ti ti-printer" aria-hidden="true"></i>
                        Print / PDF
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="sa-info-strip">
        <div class="sa-info-item">
            <span class="sa-info-icon">
                <i class="ti ti-receipt" aria-hidden="true"></i>
            </span>
            <span><?= number_format($totalTransactions) ?> transactions</span>
        </div>

        <div class="sa-info-item">
            <span class="sa-info-icon">
                <i class="ti ti-cash" aria-hidden="true"></i>
            </span>
            <span>Revenue: <?= formatRupiah($reportRevenue) ?></span>
        </div>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-blue">
                <i class="ti ti-calendar-day" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Daily Revenue</span>
            <div class="sa-summary-value"><?= formatRupiah($dailyRevenue) ?></div>
            <span class="sa-summary-sub">pendapatan harian</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-green">
                <i class="ti ti-calendar-week" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Weekly Revenue</span>
            <div class="sa-summary-value"><?= formatRupiah($weeklyRevenue) ?></div>
            <span class="sa-summary-sub">pendapatan mingguan</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-orange">
                <i class="ti ti-calendar-month" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Monthly Revenue</span>
            <div class="sa-summary-value"><?= formatRupiah($monthlyRevenue) ?></div>
            <span class="sa-summary-sub">pendapatan bulanan</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-purple">
                <i class="ti ti-users" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Cashiers</span>
            <div class="sa-summary-value"><?= number_format($totalCashiers) ?></div>
            <span class="sa-summary-sub">kasir dalam laporan</span>
        </div>
    </div>

    <div class="sa-content-grid">

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-receipt" aria-hidden="true"></i>
                        Transaction List
                    </div>
                    <div class="sa-panel-subtitle">
                        Riwayat transaksi sesuai rentang tanggal yang dipilih.
                    </div>
                </div>
            </div>

            <div class="sa-table-wrap">
                <table class="sa-table">
                    <colgroup>
                        <col style="width: 170px">
                        <col style="width: 190px">
                        <col style="width: 150px">
                        <col style="width: 210px">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $row): ?>
                                <tr>
                                    <td>
                                        <span class="sa-code">
                                            <?= e($row['transaction_code'] ?? '-') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="sa-cashier">
                                            <?= e($row['cashier_name'] ?? '-') ?>
                                        </div>
                                    </td>

                                    <td class="sa-money">
                                        <?= formatRupiah($row['total_price'] ?? 0) ?>
                                    </td>

                                    <td class="sa-date">
                                        <?= e($row['created_at'] ?? '-') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="sa-empty">
                                    <i class="ti ti-inbox" aria-hidden="true"></i>
                                    Belum ada transaksi pada periode ini.
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
                        <i class="ti ti-user-star" aria-hidden="true"></i>
                        Kasir Performance
                    </div>
                    <div class="sa-panel-subtitle">
                        Ringkasan performa penjualan tiap kasir.
                    </div>
                </div>
            </div>

            <div class="sa-list">
                <?php if (!empty($cashiers)): ?>
                    <?php foreach ($cashiers as $row): ?>
                        <div class="sa-list-item">
                            <div class="sa-list-main">
                                <div class="sa-list-title">
                                    <?= e($row['name'] ?? '-') ?>
                                </div>
                                <div class="sa-list-sub">
                                    <?= number_format((int) ($row['transaction_count'] ?? 0)) ?> trx
                                </div>
                            </div>

                            <div class="sa-list-value">
                                <?= formatRupiah($row['revenue'] ?? 0) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sa-empty">
                        <i class="ti ti-users-off" aria-hidden="true"></i>
                        Belum ada data performa kasir.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-trophy" aria-hidden="true"></i>
                    Best Selling Products
                </div>
                <div class="sa-panel-subtitle">
                    Produk dengan jumlah penjualan tertinggi pada periode ini.
                </div>
            </div>

            <div class="sa-panel-subtitle">
                <?= number_format($totalTopProducts) ?> produk
            </div>
        </div>

        <div class="sa-list">
            <?php if (!empty($topProducts)): ?>
                <?php foreach ($topProducts as $index => $row): ?>
                    <div class="sa-list-item sa-product-item">
                        <div class="sa-rank">
                            <?= $index + 1 ?>
                        </div>

                        <div class="sa-list-main">
                            <div class="sa-list-title">
                                <?= e($row['product_name'] ?? '-') ?>
                            </div>
                            <div class="sa-list-sub">
                                <?= number_format((int) ($row['total_terjual'] ?? 0)) ?> pcs terjual
                            </div>
                        </div>

                        <div class="sa-list-value">
                            <?= formatRupiah($row['total_omzet'] ?? 0) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sa-empty">
                    <i class="ti ti-inbox" aria-hidden="true"></i>
                    Belum ada produk terjual pada periode ini.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>