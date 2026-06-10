<!-- ============================================================ -->
<!-- DASHBOARD - MODERN THEME VERSION -->
<!-- ============================================================ -->

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

    $quickActions[] = [
        'url' => url('/kasir/product'),
        'icon' => 'ti-search',
        'title' => 'Cari Produk',
        'desc' => 'Lihat harga dan stok tersedia.',
        'tour' => 'quick-product-search',
        'tone' => 'mint',
    ];
}

$quickActions[] = [
    'url' => url('/report/daily/export'),
    'icon' => 'ti-download',
    'title' => 'Export CSV',
    'desc' => 'Unduh laporan penjualan hari ini.',
    'tour' => 'quick-export',
    'tone' => 'violet',
];

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
    'brand' => 'dashboard-tone-brand',
    'mint' => 'dashboard-tone-mint',
    'amber' => 'dashboard-tone-amber',
    'violet' => 'dashboard-tone-violet',
    'danger' => 'dashboard-tone-danger',
];
?>

<style>
:root {
    --dash-bg: #f8fafc;
    --dash-bg-soft: #eef6ff;
    --dash-surface: rgba(255, 255, 255, 0.78);
    --dash-surface-solid: #ffffff;
    --dash-border: #dbe6f3;
    --dash-ink: #172033;
    --dash-muted: #64748b;
    --dash-brand: #2563eb;
    --dash-brand-dark: #1e40af;
    --dash-brand-soft: #dbeafe;
    --dash-mint: #10b981;
    --dash-mint-soft: #dcfce7;
    --dash-amber: #f59e0b;
    --dash-amber-soft: #fef3c7;
    --dash-violet: #7c3aed;
    --dash-violet-soft: #ede9fe;
    --dash-danger: #ef4444;
    --dash-danger-soft: #fee2e2;
    --dash-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
    --dash-shadow-strong: 0 28px 80px rgba(15, 23, 42, 0.12);
}

.dashboard-hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 18px;
    padding: 28px;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.9);
    border-radius: 30px;
    background:
        radial-gradient(circle at 8% 0%, rgba(37, 99, 235, 0.18), transparent 34%),
        radial-gradient(circle at 92% 18%, rgba(16, 185, 129, 0.14), transparent 32%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(248, 250, 252, 0.76));
    box-shadow: var(--dash-shadow);
    backdrop-filter: blur(20px);
}

.dashboard-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    padding: 7px 12px;
    border: 1px solid rgba(37, 99, 235, 0.18);
    border-radius: 999px;
    background: rgba(219, 234, 254, 0.7);
    color: var(--dash-brand);
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.dashboard-hero h2 {
    margin: 0;
    color: var(--dash-ink);
    font-size: clamp(2rem, 4vw, 4rem);
    font-weight: 950;
    line-height: 0.98;
    letter-spacing: -0.055em;
}

.dashboard-hero p {
    max-width: 660px;
    margin: 12px 0 0;
    color: var(--dash-muted);
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.7;
}

.dashboard-hero-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}

.dashboard-user-pill {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    max-width: 320px;
    padding: 11px 14px;
    border: 1px solid var(--dash-border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--dash-muted);
    font-size: 0.86rem;
    font-weight: 800;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
}

.dashboard-user-pill span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.dashboard-actions {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}

.dashboard-action-card {
    position: relative;
    display: flex;
    min-height: 140px;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
    padding: 20px;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 26px;
    background: var(--dash-surface);
    color: var(--dash-ink);
    text-decoration: none;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.dashboard-action-card::before {
    position: absolute;
    inset: 0;
    pointer-events: none;
    content: "";
    opacity: 0;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), transparent 56%);
    transition: opacity 0.2s ease;
}

.dashboard-action-card:hover {
    transform: translateY(-4px);
    border-color: rgba(37, 99, 235, 0.25);
    color: var(--dash-ink);
    box-shadow: var(--dash-shadow);
}

.dashboard-action-card:hover::before {
    opacity: 1;
}

.dashboard-action-card i {
    position: relative;
    z-index: 1;
    display: grid;
    width: 46px;
    height: 46px;
    place-items: center;
    border-radius: 17px;
    font-size: 1.35rem;
}

.dashboard-action-card strong,
.dashboard-action-card span {
    position: relative;
    z-index: 1;
}

.dashboard-action-card strong {
    display: block;
    font-size: 1.02rem;
    font-weight: 950;
}

.dashboard-action-card span {
    color: var(--dash-muted);
    font-size: 0.86rem;
    font-weight: 700;
    line-height: 1.55;
}

.dashboard-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}

.dashboard-kpi {
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 128px;
    padding: 20px;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 26px;
    background: var(--dash-surface);
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
}

.dashboard-kpi-icon {
    display: grid;
    width: 54px;
    height: 54px;
    flex-shrink: 0;
    place-items: center;
    border-radius: 20px;
    font-size: 1.45rem;
}

.dashboard-kpi small {
    display: block;
    margin-bottom: 6px;
    color: var(--dash-muted);
    font-size: 0.76rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.dashboard-kpi strong {
    display: block;
    color: var(--dash-ink);
    font-size: clamp(1.15rem, 2vw, 1.65rem);
    font-weight: 950;
    letter-spacing: -0.04em;
}

.dashboard-tone-brand i,
.dashboard-tone-brand .dashboard-kpi-icon {
    background: var(--dash-brand-soft);
    color: var(--dash-brand);
}

.dashboard-tone-mint i,
.dashboard-tone-mint .dashboard-kpi-icon {
    background: var(--dash-mint-soft);
    color: #047857;
}

.dashboard-tone-amber i,
.dashboard-tone-amber .dashboard-kpi-icon {
    background: var(--dash-amber-soft);
    color: #b45309;
}

.dashboard-tone-violet i,
.dashboard-tone-violet .dashboard-kpi-icon {
    background: var(--dash-violet-soft);
    color: var(--dash-violet);
}

.dashboard-tone-danger i,
.dashboard-tone-danger .dashboard-kpi-icon {
    background: var(--dash-danger-soft);
    color: var(--dash-danger);
}

.dashboard-report-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 18px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 18px;
}

.dashboard-panel {
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 28px;
    background: var(--dash-surface);
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
}

.dashboard-panel-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 20px 22px;
    border-bottom: 1px solid rgba(219, 230, 243, 0.78);
}

.dashboard-panel-header h5 {
    margin: 0;
    color: var(--dash-ink);
    font-size: 1.05rem;
    font-weight: 950;
    letter-spacing: -0.025em;
}

.dashboard-panel-header p {
    margin: 6px 0 0;
    color: var(--dash-muted);
    font-size: 0.88rem;
    font-weight: 700;
    line-height: 1.55;
}

.dashboard-chart-panel {
    min-height: 390px;
}

.dashboard-chart-wrap {
    position: relative;
    height: 300px;
    padding: 16px 18px 22px;
}

.dashboard-list {
    display: grid;
    gap: 10px;
    padding: 16px;
}

.dashboard-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px;
    border: 1px solid rgba(219, 230, 243, 0.78);
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.66);
}

.dashboard-list-item strong {
    display: block;
    color: var(--dash-ink);
    font-weight: 950;
}

.dashboard-list-item span {
    display: block;
    margin-top: 4px;
    color: var(--dash-muted);
    font-size: 0.82rem;
    font-weight: 700;
}

.dashboard-empty {
    display: grid;
    min-height: 210px;
    place-items: center;
    padding: 28px;
    text-align: center;
}

.dashboard-empty i {
    display: grid;
    width: 58px;
    height: 58px;
    place-items: center;
    margin-bottom: 12px;
    border-radius: 22px;
    background: var(--dash-mint-soft);
    color: #047857;
    font-size: 1.7rem;
}

.dashboard-empty strong {
    display: block;
    color: var(--dash-ink);
    font-size: 1rem;
    font-weight: 950;
}

.dashboard-empty span {
    display: block;
    margin-top: 6px;
    color: var(--dash-muted);
    font-size: 0.9rem;
    font-weight: 700;
}

.dashboard-rank {
    display: grid;
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    place-items: center;
    border-radius: 14px;
    background: var(--dash-brand-soft);
    color: var(--dash-brand);
    font-weight: 950;
}

.dashboard-money {
    margin-left: auto;
    color: #047857;
    font-size: 0.9rem;
    font-weight: 950;
    white-space: nowrap;
}

.dashboard-cash-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.dashboard-cash-summary span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 11px;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.68);
    color: var(--dash-muted);
    font-size: 0.78rem;
    font-weight: 800;
}

.dashboard-cash-summary strong {
    color: var(--dash-ink);
}

.dashboard-item-line {
    display: grid;
    gap: 2px;
    padding: 4px 0;
}

.dashboard-item-line span {
    color: var(--dash-ink);
    font-weight: 800;
}

.dashboard-item-line small {
    color: var(--dash-muted);
    font-weight: 700;
}

.dashboard-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.dashboard-table {
    width: 100%;
    min-width: 920px;
    margin: 0;
    border-collapse: collapse;
}

.dashboard-table thead th {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(219, 230, 243, 0.95);
    color: var(--dash-muted);
    font-size: 0.74rem;
    font-weight: 950;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
}

.dashboard-table tbody td {
    padding: 15px 16px;
    border-bottom: 1px solid rgba(219, 230, 243, 0.72);
    color: var(--dash-ink);
    vertical-align: top;
}

.dashboard-table tbody tr:hover {
    background: rgba(219, 234, 254, 0.24);
}

.dashboard-code {
    display: inline-flex;
    padding: 6px 9px;
    border-radius: 10px;
    background: rgba(219, 234, 254, 0.62);
    color: var(--dash-brand-dark);
    font-size: 0.78rem;
    font-weight: 900;
}

.dashboard-badge-danger {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--dash-danger-soft);
    color: #b91c1c;
    font-size: 0.76rem;
    font-weight: 950;
}

@media (max-width: 1199.98px) {
    .dashboard-actions,
    .dashboard-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-report-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 991.98px) {
    .dashboard-hero {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-hero-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 575.98px) {
    .dashboard-hero,
    .dashboard-panel-header {
        padding: 18px;
    }

    .dashboard-actions,
    .dashboard-kpis {
        grid-template-columns: 1fr;
    }

    .dashboard-kpi {
        min-height: auto;
    }

    .dashboard-user-pill {
        width: 100%;
    }

    .dashboard-hero-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .dashboard-cash-summary {
        width: 100%;
    }

    .dashboard-cash-summary span {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<section class="dashboard-hero" data-tour="dashboard-overview">
    <div>
        <span class="dashboard-eyebrow">
            <?= e(roleLabel($role)) ?> Workspace
        </span>

        <h2>Dashboard</h2>

        <p>
            Ringkasan toko hari ini, dibuat ringkas supaya kamu langsung tahu aksi berikutnya.
        </p>
    </div>

    <div class="dashboard-hero-actions">
        <div class="dashboard-user-pill">
            <i class="ti ti-user-circle" aria-hidden="true"></i>
            <span><?= e($userEmail) ?></span>
        </div>

        <a href="<?= $primaryActionUrl ?>" class="btn btn-primary" data-tour="primary-action">
            <i class="ti <?= e($primaryActionIcon) ?>" aria-hidden="true"></i>
            <?= e($primaryActionText) ?>
        </a>
    </div>
</section>

<section class="dashboard-actions">
    <?php foreach ($quickActions as $action): ?>
        <?php $toneClass = $toneClassMap[$action['tone']] ?? $toneClassMap['brand']; ?>

        <a
            href="<?= $action['url'] ?>"
            class="dashboard-action-card <?= e($toneClass) ?>"
            data-tour="<?= e($action['tour']) ?>"
        >
            <i class="ti <?= e($action['icon']) ?>" aria-hidden="true"></i>

            <div>
                <strong><?= e($action['title']) ?></strong>
                <span><?= e($action['desc']) ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</section>

<section class="dashboard-kpis" data-tour="dashboard-kpis">
    <?php foreach ($kpis as $kpi): ?>
        <?php $toneClass = $toneClassMap[$kpi['tone']] ?? $toneClassMap['brand']; ?>

        <div class="dashboard-kpi <?= e($toneClass) ?>">
            <span class="dashboard-kpi-icon">
                <i class="ti <?= e($kpi['icon']) ?>" aria-hidden="true"></i>
            </span>

            <div>
                <small><?= e($kpi['label']) ?></small>
                <strong><?= $kpi['value'] ?></strong>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<?php if ($isAdminPanel): ?>
    <section class="dashboard-report-grid" data-tour="dashboard-charts">
        <div class="dashboard-panel dashboard-chart-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h5>Grafik Penjualan 7 Hari</h5>
                    <p>Tren omzet dan jumlah transaksi harian.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <div class="dashboard-panel dashboard-chart-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h5>Produk Terlaris</h5>
                    <p>Produk dengan quantity penjualan tertinggi.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        <div class="dashboard-panel dashboard-chart-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h5>Penjualan Bulanan</h5>
                    <p>Pergerakan omzet dalam 6 bulan terakhir.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        <div class="dashboard-panel dashboard-chart-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h5>Arus Kas Hari Ini</h5>
                    <p>Perbandingan uang diterima dan kembalian.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="dashboard-grid">
    <div class="dashboard-panel">
        <div class="dashboard-panel-header">
            <div>
                <h5>Perhatian Hari Ini</h5>
                <p>Hal yang perlu dicek sebelum toko makin ramai.</p>
            </div>
        </div>

        <?php if (empty($lowStockProducts)): ?>
            <div class="dashboard-empty">
                <i class="ti ti-circle-check" aria-hidden="true"></i>
                <strong>Stok aman</strong>
                <span>Tidak ada produk dengan stok rendah.</span>
            </div>
        <?php else: ?>
            <div class="dashboard-list">
                <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                    <div class="dashboard-list-item">
                        <div>
                            <strong><?= e($product['name']) ?></strong>
                            <span>Sisa stok <?= e((string) $product['stock']) ?> pcs</span>
                        </div>

                        <?php if ($isAdminPanel): ?>
                            <a
                                href="<?= url('/admin/product/edit') ?>?id=<?= e((string) $product['id']) ?>"
                                class="btn btn-sm btn-warning"
                            >
                                Update
                            </a>
                        <?php else: ?>
                            <span class="dashboard-badge-danger">Low</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-panel">
        <div class="dashboard-panel-header">
            <div>
                <h5>Produk Terlaris</h5>
                <p>Produk yang paling banyak dibeli hari ini.</p>
            </div>
        </div>

        <?php if (empty($todayTopProducts)): ?>
            <div class="dashboard-empty">
                <i class="ti ti-inbox" aria-hidden="true"></i>
                <strong>Belum ada data</strong>
                <span>Produk terlaris muncul setelah ada transaksi hari ini.</span>
            </div>
        <?php else: ?>
            <div class="dashboard-list">
                <?php foreach ($todayTopProducts as $index => $product): ?>
                    <div class="dashboard-list-item">
                        <div class="dashboard-rank">
                            <?= $index + 1 ?>
                        </div>

                        <div>
                            <strong><?= e($product['product_name']) ?></strong>
                            <span><?= e((string) $product['total_quantity']) ?> pcs terjual</span>
                        </div>

                        <div class="dashboard-money">
                            <?= formatRupiah($product['total_sales']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="dashboard-panel mt-4">
    <div class="dashboard-panel-header">
        <div>
            <h5>Transaksi Hari Ini</h5>
            <p><?= count($todayHeaders) ?> transaksi tercatat hari ini.</p>
        </div>

        <div class="dashboard-cash-summary">
            <span>
                Diterima:
                <strong><?= formatRupiah($todayPaidAmount) ?></strong>
            </span>

            <span>
                Kembalian:
                <strong><?= formatRupiah($todayChangeAmount) ?></strong>
            </span>
        </div>
    </div>

    <div class="dashboard-table-wrap">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Bayar</th>
                    <th class="text-end">Kembali</th>
                    <th class="text-center">Waktu</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($todayHeaders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ti ti-inbox" style="font-size: 2.4rem;" aria-hidden="true"></i>
                            <p class="mb-0 mt-2">Belum ada transaksi hari ini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($todayHeaders as $trx): ?>
                        <?php $items = $todayDetails[$trx['id']] ?? []; ?>

                        <tr>
                            <td>
                                <code class="dashboard-code">
                                    <?= e($trx['transaction_code']) ?>
                                </code>
                            </td>

                            <td>
                                <?php if (empty($items)): ?>
                                    <span class="text-muted">Tidak ada item.</span>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php $quantity = (int) $item['quantity']; ?>

                                        <div class="dashboard-item-line">
                                            <span><?= e($item['product_name']) ?></span>
                                            <small>
                                                <?= $quantity ?>x = <?= formatRupiah($item['subtotal']) ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>

                            <td class="text-end fw-bold" style="color: #047857;">
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
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($isAdminPanel): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartTheme = {
            ink: '#172033',
            muted: '#64748b',
            line: '#dbe6f3',
            brand: '#2563eb',
            brandSoft: 'rgba(37, 99, 235, 0.12)',
            mint: '#10b981',
            mintSoft: 'rgba(16, 185, 129, 0.14)',
            amber: '#f59e0b',
            amberSoft: 'rgba(245, 158, 11, 0.16)',
            violet: '#7c3aed',
            violetSoft: 'rgba(124, 58, 237, 0.14)',
            danger: '#ef4444',
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
                    cornerRadius: 14,
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
                            borderColor: chartTheme.brand,
                            backgroundColor: chartTheme.brandSoft,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointBackgroundColor: chartTheme.brand
                        },
                        {
                            label: 'Transaksi',
                            data: dailyTransactions,
                            borderColor: chartTheme.mint,
                            backgroundColor: chartTheme.mintSoft,
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
                            chartTheme.brand,
                            chartTheme.mint,
                            chartTheme.amber,
                            chartTheme.violet,
                            chartTheme.danger
                        ],
                        borderRadius: 14
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
                        backgroundColor: chartTheme.brand,
                        borderRadius: 14
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
                        backgroundColor: [chartTheme.brand, chartTheme.amber],
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
