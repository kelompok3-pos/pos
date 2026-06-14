<?php

$totalProducts      ??= 0;
$totalStock         ??= 0;
$todayRevenue       ??= 0;
$todaySales         ??= 0;
$todayItemsSold     ??= 0;
$todayPaidAmount    ??= 0;
$todayChangeAmount  ??= 0;
$todayTopProducts   ??= [];
$totalUsers         ??= 0;
$lowStockProducts   ??= [];
$todayHeaders       ??= [];
$todayDetails       ??= [];
$dailySalesChart    ??= [];
$monthlySalesChart  ??= [];
$topSellingProducts ??= [];

$role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? currentRole();
$userEmail = $_SESSION['user']['email'] ?? $_SESSION['email'] ?? '-';

$isAdminPanel = in_array($role, ['super_admin', 'admin'], true);
$isCashierPanel = $role === 'kasir';

$primaryActionUrl = $isAdminPanel ? url('/admin/product') : url('/kasir/transaction');
$primaryActionText = $isAdminPanel ? 'Kelola Produk' : 'Mulai Transaksi';
$primaryActionIcon = $isAdminPanel ? 'ti-box-seam' : 'ti-cash-register';

$dailyChartByDate = [];

foreach ($dailySalesChart as $row) {
    $dailyChartByDate[$row['date_sort']] = $row;
}

$dailyChartLabels = [];
$dailyRevenueData = [];
$dailyTransactionData = [];
$dailyPaidData = [];
$dailyChangeData = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $row = $dailyChartByDate[$date] ?? [];

    $dailyChartLabels[] = date('d M', strtotime($date));
    $dailyRevenueData[] = (float) ($row['total'] ?? 0);
    $dailyTransactionData[] = (int) ($row['jumlah_transaksi'] ?? 0);
    $dailyPaidData[] = (float) ($row['total_dibayar'] ?? 0);
    $dailyChangeData[] = (float) ($row['total_kembalian'] ?? 0);
}

$monthlyChartLabels = array_map(fn($row) => $row['bulan_label'], $monthlySalesChart);
$monthlyRevenueData = array_map(fn($row) => (float) $row['total'], $monthlySalesChart);

$topProductLabels = array_map(fn($row) => $row['product_name'], $topSellingProducts);
$topProductQtyData = array_map(fn($row) => (int) $row['total_terjual'], $topSellingProducts);

$quickActions = [];

if ($isAdminPanel) {
    $quickActions[] = [
        'url' => url('/admin/product/create'),
        'icon' => 'ti-plus',
        'title' => 'Tambah Produk',
        'desc' => 'Masukkan produk dan stok baru.',
        'tour' => 'quick-add-product',
        'tone' => 'brand',
    ];

    $quickActions[] = [
        'url' => url('/admin/user/create'),
        'icon' => 'ti-user-plus',
        'title' => 'Tambah User',
        'desc' => 'Buat dan kelola akun tim.',
        'tour' => 'quick-add-user',
        'tone' => 'mint',
    ];

    $quickActions[] = [
        'url' => url('/inventory'),
        'icon' => 'ti-packages',
        'title' => 'Cek Stok',
        'desc' => 'Review stok dan movement produk.',
        'tour' => 'quick-stock',
        'tone' => 'amber',
    ];
}

if ($isCashierPanel) {
    $quickActions[] = [
        'url' => url('/kasir/transaction'),
        'icon' => 'ti-shopping-cart-plus',
        'title' => 'Transaksi Baru',
        'desc' => 'Buka kasir dan mulai checkout.',
        'tour' => 'quick-transaction',
        'tone' => 'brand',
    ];
}

$kpis = [
    [
        'label' => 'Pendapatan Hari Ini',
        'value' => formatRupiah($todayRevenue),
        'icon' => 'ti-cash',
        'tone' => 'brand',
    ],
    [
        'label' => 'Transaksi',
        'value' => number_format($todaySales, 0, ',', '.') . ' trx',
        'icon' => 'ti-receipt',
        'tone' => 'mint',
    ],
    [
        'label' => 'Produk Terjual',
        'value' => number_format($todayItemsSold, 0, ',', '.') . ' pcs',
        'icon' => 'ti-shopping-bag-check',
        'tone' => 'violet',
    ],
    [
        'label' => 'Total Stok',
        'value' => number_format($totalStock, 0, ',', '.') . ' pcs',
        'icon' => 'ti-box-seam',
        'tone' => 'amber',
    ],
];

$toneClassMap = [
    'brand' => 'sa-tone-brand',
    'mint' => 'sa-tone-mint',
    'amber' => 'sa-tone-amber',
    'violet' => 'sa-tone-violet',
    'danger' => 'sa-tone-danger',
];
?>

<style>
.sa-dashboard {
    width: 100%;
}

.sa-dashboard * {
    box-sizing: border-box;
}

.sa-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.sa-title .sa-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
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
    line-height: 1.55;
}

.sa-hero-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .6rem;
    flex-wrap: wrap;
}

.sa-user-pill {
    height: 42px;
    max-width: 320px;
    padding: 0 .9rem;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 999px;
    background: var(--color-background-primary, #fff);
    color: var(--color-text-secondary, #6b7280);
    font-size: .84rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: .45rem;
}

.sa-user-pill span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-primary-link {
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

.sa-primary-link:hover {
    background: #2f7bc6;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-action-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-action-card {
    min-height: 126px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    background: var(--color-background-primary, #fff);
    padding: 1rem;
    color: var(--color-text-primary, #111827);
    text-decoration: none;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: .85rem;
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
}

.sa-action-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
    color: var(--color-text-primary, #111827);
    text-decoration: none;
}

.sa-action-icon,
.sa-kpi-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.sa-action-title {
    display: block;
    font-size: .95rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
}

.sa-action-desc {
    display: block;
    margin-top: .25rem;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    line-height: 1.45;
}

.sa-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-kpi-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    padding: 1rem;
    min-height: 120px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-kpi-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-kpi-icon {
    margin-bottom: .85rem;
}

.sa-kpi-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .25rem;
}

.sa-kpi-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    line-height: 1.2;
    word-break: break-word;
}

.sa-tone-brand .sa-action-icon,
.sa-tone-brand .sa-kpi-icon {
    color: #378add;
    background: rgba(55, 138, 221, .11);
}

.sa-tone-mint .sa-action-icon,
.sa-tone-mint .sa-kpi-icon {
    color: #1d9e75;
    background: rgba(29, 158, 117, .11);
}

.sa-tone-amber .sa-action-icon,
.sa-tone-amber .sa-kpi-icon {
    color: #ba7517;
    background: rgba(186, 117, 23, .12);
}

.sa-tone-violet .sa-action-icon,
.sa-tone-violet .sa-kpi-icon {
    color: #7c3aed;
    background: rgba(124, 58, 237, .11);
}

.sa-tone-danger .sa-action-icon,
.sa-tone-danger .sa-kpi-icon {
    color: #e24b4a;
    background: rgba(226, 75, 74, .12);
}

.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
}

.sa-panel-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
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

.sa-chart-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-chart-box {
    padding: 1rem 1.1rem 1.2rem;
}

.sa-chart-wrap {
    position: relative;
    width: 100%;
    height: 280px;
}

.sa-content-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: start;
}

.sa-list {
    padding: .35rem 1rem .7rem;
}

.sa-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .85rem 0;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .07));
}

.sa-list-item:last-child {
    border-bottom: none;
}

.sa-list-main {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: .75rem;
}

.sa-list-text {
    min-width: 0;
}

.sa-list-title {
    display: block;
    font-size: .84rem;
    font-weight: 750;
    color: var(--color-text-primary, #111827);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sa-list-subtitle {
    display: block;
    margin-top: .15rem;
    font-size: .74rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-rank {
    width: 34px;
    height: 34px;
    border-radius: .75rem;
    background: rgba(55, 138, 221, .11);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: .82rem;
    font-weight: 800;
}

.sa-money {
    font-size: .82rem;
    font-weight: 800;
    color: #247244;
    white-space: nowrap;
}

.sa-warning-link {
    height: 34px;
    padding: 0 .7rem;
    border-radius: .65rem;
    border: 1px solid rgba(186, 117, 23, .22);
    background: rgba(186, 117, 23, .10);
    color: #ba7517;
    text-decoration: none;
    font-size: .76rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color .16s ease, border-color .16s ease;
}

.sa-warning-link:hover {
    background: rgba(186, 117, 23, .15);
    border-color: rgba(186, 117, 23, .35);
    color: #ba7517;
    text-decoration: none;
}

.sa-danger-pill {
    display: inline-flex;
    align-items: center;
    padding: .25rem .55rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 800;
    background: #fef3f2;
    color: #b42318;
}

.sa-empty {
    min-height: 190px;
    padding: 2rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
    display: grid;
    place-items: center;
}

.sa-empty i {
    display: inline-flex;
    width: 46px;
    height: 46px;
    border-radius: .9rem;
    align-items: center;
    justify-content: center;
    margin-bottom: .65rem;
    color: #1d9e75;
    background: rgba(29, 158, 117, .11);
    font-size: 1.35rem;
}

.sa-empty strong {
    display: block;
    color: var(--color-text-primary, #111827);
    font-size: .9rem;
    font-weight: 800;
}

.sa-empty span {
    display: block;
    margin-top: .25rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .8rem;
}

.sa-cash-summary {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
}

.sa-cash-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .65rem;
    border-radius: 999px;
    background: var(--color-background-secondary, #f9fafb);
    color: var(--color-text-secondary, #6b7280);
    font-size: .74rem;
    font-weight: 700;
    white-space: nowrap;
}

.sa-cash-pill strong {
    color: var(--color-text-primary, #111827);
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
    vertical-align: top;
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

.sa-code {
    display: inline-flex;
    padding: .35rem .55rem;
    border-radius: .6rem;
    background: rgba(55, 138, 221, .09);
    color: #185fa5;
    font-size: .76rem;
    font-weight: 800;
}

.sa-item-line {
    display: grid;
    gap: .12rem;
    padding: .18rem 0;
}

.sa-item-line span {
    font-weight: 750;
    color: var(--color-text-primary, #111827);
}

.sa-item-line small {
    color: var(--color-text-secondary, #6b7280);
    font-size: .72rem;
}

.sa-text-right {
    text-align: right;
}

.sa-text-center {
    text-align: center;
}

.sa-text-muted {
    color: var(--color-text-secondary, #6b7280);
}

.sa-fw-bold {
    font-weight: 800;
}


.sa-trx-panel {
    margin-top: 1rem;
}

.sa-trx-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-trx-table {
    width: 100%;
    min-width: 980px;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: .825rem;
}

.sa-trx-table th {
    padding: .85rem 1rem;
    text-align: left;
    font-size: .72rem;
    font-weight: 800;
    color: var(--color-text-secondary, #6b7280);
    background: rgba(248, 250, 252, .9);
    border-bottom: 1px solid rgba(219, 230, 243, .95);
    white-space: nowrap;
}

.sa-trx-table td {
    padding: .9rem 1rem;
    border-bottom: 1px solid rgba(219, 230, 243, .72);
    color: var(--color-text-primary, #111827);
    vertical-align: top;
}

.sa-trx-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-trx-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-trx-code-col {
    width: 150px;
}

.sa-trx-items-col {
    width: 340px;
}

.sa-trx-money-col {
    width: 150px;
}

.sa-trx-time-col {
    width: 110px;
}

.sa-trx-code {
    display: inline-flex;
    max-width: 130px;
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

.sa-trx-items {
    display: grid;
    gap: .45rem;
}

.sa-trx-item {
    display: grid;
    gap: .12rem;
}

.sa-trx-item-name {
    color: var(--color-text-primary, #111827);
    font-size: .82rem;
    font-weight: 750;
    line-height: 1.35;
}

.sa-trx-item-meta {
    color: var(--color-text-secondary, #6b7280);
    font-size: .72rem;
    font-weight: 650;
}

.sa-trx-money {
    text-align: right;
    font-weight: 750;
    white-space: nowrap;
}

.sa-trx-money-main {
    color: #1D9E75;
    font-weight: 850;
}

.sa-trx-time {
    text-align: center;
    color: var(--color-text-secondary, #6b7280);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.sa-trx-empty {
    padding: 2.4rem 1rem;
    text-align: center;
    color: var(--color-text-secondary, #6b7280);
}

.sa-trx-empty i {
    display: inline-flex;
    width: 46px;
    height: 46px;
    border-radius: .9rem;
    align-items: center;
    justify-content: center;
    margin-bottom: .65rem;
    color: #1d9e75;
    background: rgba(29, 158, 117, .11);
    font-size: 1.35rem;
}

.sa-trx-empty strong {
    display: block;
    color: var(--color-text-primary, #111827);
    font-size: .9rem;
    font-weight: 800;
}

.sa-trx-empty span {
    display: block;
    margin-top: .25rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .8rem;
}

@media (max-width: 1100px) {
    .sa-action-grid,
    .sa-kpi-grid,
    .sa-chart-grid,
    .sa-content-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 800px) {
    .sa-hero {
        flex-direction: column;
    }

    .sa-hero-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .sa-user-pill,
    .sa-primary-link {
        width: 100%;
        justify-content: center;
    }

    .sa-action-grid,
    .sa-kpi-grid,
    .sa-chart-grid,
    .sa-content-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
    }

    .sa-chart-wrap {
        height: 240px;
    }

    .sa-trx-table {
        min-width: 920px;
    }
}
</style>

<div class="sa-dashboard">

    <section class="sa-hero" data-tour="dashboard-overview">
        <div class="sa-title">
            <span class="sa-eyebrow">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i>
                <?= e(roleLabel($role)) ?> Workspace
            </span>

            <h2>Dashboard</h2>

            <p>Ringkasan toko hari ini, dibuat ringkas supaya kamu langsung tahu aksi berikutnya.</p>
        </div>

        <div class="sa-hero-actions">
            <div class="sa-user-pill">
                <i class="ti ti-user-circle" aria-hidden="true"></i>
                <span><?= e($userEmail) ?></span>
            </div>

            <a href="<?= $primaryActionUrl ?>" class="sa-primary-link" data-tour="primary-action">
                <i class="ti <?= e($primaryActionIcon) ?>" aria-hidden="true"></i>
                <?= e($primaryActionText) ?>
            </a>
        </div>
    </section>

    <?php if (!empty($quickActions)): ?>
        <section class="sa-action-grid">
            <?php foreach ($quickActions as $action): ?>
                <?php $toneClass = $toneClassMap[$action['tone']] ?? $toneClassMap['brand']; ?>

                <a
                    href="<?= $action['url'] ?>"
                    class="sa-action-card <?= e($toneClass) ?>"
                    data-tour="<?= e($action['tour']) ?>"
                >
                    <span class="sa-action-icon">
                        <i class="ti <?= e($action['icon']) ?>" aria-hidden="true"></i>
                    </span>

                    <span>
                        <span class="sa-action-title"><?= e($action['title']) ?></span>
                        <span class="sa-action-desc"><?= e($action['desc']) ?></span>
                    </span>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="sa-kpi-grid" data-tour="dashboard-kpis">
        <?php foreach ($kpis as $kpi): ?>
            <?php $toneClass = $toneClassMap[$kpi['tone']] ?? $toneClassMap['brand']; ?>

            <div class="sa-kpi-card <?= e($toneClass) ?>">
                <span class="sa-kpi-icon">
                    <i class="ti <?= e($kpi['icon']) ?>" aria-hidden="true"></i>
                </span>

                <span class="sa-kpi-label"><?= e($kpi['label']) ?></span>
                <div class="sa-kpi-value"><?= $kpi['value'] ?></div>
            </div>
        <?php endforeach; ?>
    </section>

    <?php if ($isAdminPanel): ?>
        <section class="sa-chart-grid" data-tour="dashboard-charts">
            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div>
                        <div class="sa-panel-title">Grafik Penjualan 7 Hari</div>
                        <div class="sa-panel-subtitle">Tren omzet dan jumlah transaksi harian.</div>
                    </div>
                </div>

                <div class="sa-chart-box">
                    <div class="sa-chart-wrap">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div>
                        <div class="sa-panel-title">Produk Terlaris</div>
                        <div class="sa-panel-subtitle">Produk dengan quantity penjualan tertinggi.</div>
                    </div>
                </div>

                <div class="sa-chart-box">
                    <div class="sa-chart-wrap">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div>
                        <div class="sa-panel-title">Penjualan Bulanan</div>
                        <div class="sa-panel-subtitle">Pergerakan omzet dalam 6 bulan terakhir.</div>
                    </div>
                </div>

                <div class="sa-chart-box">
                    <div class="sa-chart-wrap">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div>
                        <div class="sa-panel-title">Arus Kas Hari Ini</div>
                        <div class="sa-panel-subtitle">Perbandingan uang diterima dan kembalian.</div>
                    </div>
                </div>

                <div class="sa-chart-box">
                    <div class="sa-chart-wrap">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="sa-content-grid">
        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">Perhatian Hari Ini</div>
                    <div class="sa-panel-subtitle">Hal yang perlu dicek sebelum toko makin ramai.</div>
                </div>
            </div>

            <?php if (empty($lowStockProducts)): ?>
                <div class="sa-empty">
                    <div>
                        <i class="ti ti-circle-check" aria-hidden="true"></i>
                        <strong>Stok aman</strong>
                        <span>Tidak ada produk dengan stok rendah.</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="sa-list">
                    <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                        <div class="sa-list-item">
                            <div class="sa-list-main">
                                <span class="sa-rank">
                                    <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                                </span>

                                <div class="sa-list-text">
                                    <span class="sa-list-title"><?= e($product['name']) ?></span>
                                    <span class="sa-list-subtitle">Sisa stok <?= e((string) $product['stock']) ?> pcs</span>
                                </div>
                            </div>

                            <?php if ($isAdminPanel): ?>
                                <a
                                    href="<?= url('/admin/product/edit') ?>?id=<?= e((string) $product['id']) ?>"
                                    class="sa-warning-link"
                                >
                                    Update
                                </a>
                            <?php else: ?>
                                <span class="sa-danger-pill">Low</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">Produk Terlaris</div>
                    <div class="sa-panel-subtitle">Produk yang paling banyak dibeli hari ini.</div>
                </div>
            </div>

            <?php if (empty($todayTopProducts)): ?>
                <div class="sa-empty">
                    <div>
                        <i class="ti ti-inbox" aria-hidden="true"></i>
                        <strong>Belum ada data</strong>
                        <span>Produk terlaris muncul setelah ada transaksi hari ini.</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="sa-list">
                    <?php foreach ($todayTopProducts as $index => $product): ?>
                        <div class="sa-list-item">
                            <div class="sa-list-main">
                                <span class="sa-rank"><?= $index + 1 ?></span>

                                <div class="sa-list-text">
                                    <span class="sa-list-title"><?= e($product['product_name']) ?></span>
                                    <span class="sa-list-subtitle"><?= e((string) $product['total_quantity']) ?> pcs terjual</span>
                                </div>
                            </div>

                            <div class="sa-money">
                                <?= formatRupiah($product['total_sales']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="sa-panel sa-trx-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">Transaksi Hari Ini</div>
                <div class="sa-panel-subtitle"><?= count($todayHeaders) ?> transaksi tercatat hari ini.</div>
            </div>

            <div class="sa-cash-summary">
                <span class="sa-cash-pill">
                    Diterima:
                    <strong><?= formatRupiah($todayPaidAmount) ?></strong>
                </span>

                <span class="sa-cash-pill">
                    Kembalian:
                    <strong><?= formatRupiah($todayChangeAmount) ?></strong>
                </span>
            </div>
        </div>

        <div class="sa-trx-table-wrap">
            <table class="sa-trx-table">
                <colgroup>
                    <col class="sa-trx-code-col">
                    <col class="sa-trx-items-col">
                    <col class="sa-trx-money-col">
                    <col class="sa-trx-money-col">
                    <col class="sa-trx-money-col">
                    <col class="sa-trx-time-col">
                </colgroup>

                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Items</th>
                        <th style="text-align:right">Total</th>
                        <th style="text-align:right">Bayar</th>
                        <th style="text-align:right">Kembali</th>
                        <th style="text-align:center">Waktu</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($todayHeaders)): ?>
                        <tr>
                            <td colspan="6" class="sa-trx-empty">
                                <div>
                                    <i class="ti ti-inbox" aria-hidden="true"></i>
                                    <strong>Belum ada transaksi hari ini.</strong>
                                    <span>Data transaksi akan muncul setelah kasir melakukan checkout.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todayHeaders as $trx): ?>
                            <?php $items = $todayDetails[$trx['id']] ?? []; ?>

                            <tr>
                                <td>
                                    <span class="sa-trx-code">
                                        <?= e($trx['transaction_code']) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="sa-trx-items">
                                        <?php if (empty($items)): ?>
                                            <span class="sa-trx-item-meta">Tidak ada item.</span>
                                        <?php else: ?>
                                            <?php foreach ($items as $item): ?>
                                                <?php $quantity = (int) $item['quantity']; ?>

                                                <div class="sa-trx-item">
                                                    <span class="sa-trx-item-name">
                                                        <?= e($item['product_name']) ?>
                                                    </span>

                                                    <span class="sa-trx-item-meta">
                                                        <?= $quantity ?>x = <?= formatRupiah($item['subtotal']) ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="sa-trx-money sa-trx-money-main">
                                    <?= formatRupiah($trx['total_price']) ?>
                                </td>

                                <td class="sa-trx-money">
                                    <?= formatRupiah($trx['paid_amount'] ?? 0) ?>
                                </td>

                                <td class="sa-trx-money">
                                    <?= formatRupiah($trx['change_amount'] ?? 0) ?>
                                </td>

                                <td class="sa-trx-time">
                                    <?= date('H:i', strtotime($trx['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

<?php if ($isAdminPanel): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartTheme = {
            ink: '#111827',
            muted: '#6b7280',
            line: 'rgba(55, 138, 221, 0.12)',

            blue: '#378ADD',
            blueSoft: 'rgba(55, 138, 221, 0.16)',

            green: '#1D9E75',
            greenSoft: 'rgba(29, 158, 117, 0.16)',

            orange: '#BA7517',
            orangeSoft: 'rgba(186, 117, 23, 0.16)',

            red: '#E24B4A',
            redSoft: 'rgba(226, 75, 74, 0.16)',

            purple: '#8B5CF6',
            purpleSoft: 'rgba(139, 92, 246, 0.16)',

            cyan: '#06B6D4',
            cyanSoft: 'rgba(6, 182, 212, 0.16)',

            surface: '#ffffff'
        };

        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        const dailyLabels = <?= json_encode($dailyChartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const dailyRevenue = <?= json_encode($dailyRevenueData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const dailyTransactions = <?= json_encode($dailyTransactionData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const dailyPaid = <?= json_encode($dailyPaidData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const dailyChange = <?= json_encode($dailyChangeData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const monthlyLabels = <?= json_encode($monthlyChartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const monthlyRevenue = <?= json_encode($monthlyRevenueData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const topProductLabels = <?= json_encode($topProductLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const topProductQty = <?= json_encode($topProductQtyData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: chartTheme.muted,
                        boxWidth: 10,
                        usePointStyle: true,
                        font: {
                            weight: 700
                        }
                    }
                },
                tooltip: {
                    backgroundColor: chartTheme.ink,
                    padding: 12,
                    cornerRadius: 12,
                    titleColor: chartTheme.surface,
                    bodyColor: '#e5e7eb'
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: chartTheme.muted,
                        font: {
                            weight: 700
                        }
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: chartTheme.line
                    },
                    ticks: {
                        color: chartTheme.muted,
                        callback: function(value) {
                            return value >= 1000 ? `${Math.round(value / 1000)}k` : value;
                        }
                    },
                    border: {
                        display: false
                    }
                }
            }
        };

        const dailySalesCanvas = document.getElementById('dailySalesChart');
        const topProductsCanvas = document.getElementById('topProductsChart');
        const monthlyRevenueCanvas = document.getElementById('monthlyRevenueChart');
        const cashFlowCanvas = document.getElementById('cashFlowChart');

        if (dailySalesCanvas) {
            new Chart(dailySalesCanvas, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [
                        {
                            label: 'Omzet',
                            data: dailyRevenue,
                            borderColor: chartTheme.blue,
                            backgroundColor: chartTheme.blueSoft,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointBackgroundColor: chartTheme.blue
                        },
                        {
                            label: 'Transaksi',
                            data: dailyTransactions,
                            borderColor: chartTheme.green,
                            backgroundColor: chartTheme.greenSoft,
                            tension: 0.38,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    ...chartOptions,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        ...chartOptions.scales,
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                color: chartTheme.muted,
                                precision: 0
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                            ...chartOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label === 'Omzet'
                                        ? `Omzet: ${currency.format(context.parsed.y)}`
                                        : `Transaksi: ${context.parsed.y} trx`;
                                }
                            }
                        }
                    }
                }
            });
        }

        if (topProductsCanvas) {
            new Chart(topProductsCanvas, {
                type: 'bar',
                data: {
                    labels: topProductLabels.length ? topProductLabels : ['Belum ada data'],
                    datasets: [{
                        label: 'Qty Terjual',
                        data: topProductQty.length ? topProductQty : [0],
                        backgroundColor: [
                            chartTheme.blue,
                            chartTheme.green,
                            chartTheme.orange,
                            chartTheme.purple,
                            chartTheme.cyan,
                            chartTheme.red
                        ],
                        borderRadius: 8
                    }]
                },
                options: chartOptions
            });
        }

        if (monthlyRevenueCanvas) {
            new Chart(monthlyRevenueCanvas, {
                type: 'bar',
                data: {
                    labels: monthlyLabels.length ? monthlyLabels : ['Belum ada data'],
                    datasets: [{
                        label: 'Omzet Bulanan',
                        data: monthlyRevenue.length ? monthlyRevenue : [0],
                        backgroundColor: chartTheme.blue,
                        borderRadius: 8
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                            ...chartOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return `Omzet: ${currency.format(context.parsed.y)}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        if (cashFlowCanvas) {
            new Chart(cashFlowCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Uang Diterima', 'Kembalian'],
                    datasets: [{
                        data: [
                            dailyPaid.reduce((sum, value) => sum + Number(value), 0),
                            dailyChange.reduce((sum, value) => sum + Number(value), 0)
                        ],
                        backgroundColor: [chartTheme.blue, chartTheme.orange],
                        borderColor: chartTheme.surface,
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTheme.muted,
                                usePointStyle: true,
                                font: {
                                    weight: 700
                                }
                            }
                        },
                        tooltip: {
                            ...chartOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${currency.format(context.parsed)}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
<?php endif; ?>
