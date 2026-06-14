<?php

$stores = $stores ?? [];
$recentAudit = $recentAudit ?? [];

$totals = $totals ?? [
    'stores' => 0,
    'transactions' => 0,
    'revenue' => 0,
    'expenses' => 0,
    'net' => 0,
];

$auditIcons = [
    'login'           => 'ti-login',
    'logout'          => 'ti-logout',
    'transaction'     => 'ti-receipt',
    'update_product'  => 'ti-edit',
    'create_user'     => 'ti-user-plus',
    'delete'          => 'ti-trash',
    'stock_adjust'    => 'ti-package',
    'change_password' => 'ti-lock',
    'export'          => 'ti-download',
];

$chartStoreLabels = json_encode(array_map(
    fn($s) => $s['store_name'] ?? '-',
    $stores
));

$chartRevenue = json_encode(array_map(
    fn($s) => round((float) ($s['revenue'] ?? 0) / 1000000, 2),
    $stores
));

$chartExpenses = json_encode(array_map(
    fn($s) => round((float) ($s['expenses'] ?? 0) / 1000000, 2),
    $stores
));

$chartNet = json_encode(array_map(
    fn($s) => round((float) ($s['net'] ?? 0) / 1000000, 2),
    $stores
));
?>

<style>
.sa-dashboard {
    width: 100%;
}

.sa-dashboard * {
    box-sizing: border-box;
}

.sa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
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

.sa-action-group {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.sa-action-link {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .55rem .8rem;
    border-radius: .7rem;
    font-size: .8125rem;
    font-weight: 600;
    text-decoration: none;
    color: #185fa5;
    background: #e6f1fb;
    border: 1px solid rgba(24, 95, 165, .14);
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-action-link:hover {
    background: #d8eafb;
    border-color: rgba(24, 95, 165, .25);
    color: #185fa5;
    text-decoration: none;
}

.sa-metric-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-metric-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    padding: 1rem;
    min-height: 120px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-metric-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-metric-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: .85rem;
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

.sa-metric-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .25rem;
}

.sa-metric-value {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    line-height: 1.2;
    word-break: break-word;
}

.sa-metric-sub {
    display: block;
    margin-top: .35rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
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
    align-items: center;
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
    grid-template-columns: minmax(0, 1.3fr) minmax(320px, .7fr);
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

.sa-chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem;
    margin-bottom: .85rem;
    font-size: .75rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-chart-legend-item {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-weight: 700;
}

.sa-chart-dot {
    width: .65rem;
    height: .65rem;
    border-radius: 999px;
    display: inline-block;
}

.sa-chart-blue {
    background: #378add;
}

.sa-chart-red {
    background: #e24b4a;
}

.sa-chart-green {
    background: #1d9e75;
}

.sa-content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.65fr) minmax(320px, .85fr);
    gap: 1rem;
    align-items: start;
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .825rem;
    min-width: 720px;
}

.sa-table th {
    padding: .75rem 1rem;
    text-align: left;
    font-size: .72rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
    background: var(--color-background-secondary, #f9fafb);
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    white-space: nowrap;
}

.sa-table td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .06));
    color: var(--color-text-primary, #111827);
    vertical-align: middle;
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

.sa-store-name {
    display: flex;
    align-items: center;
    gap: .55rem;
    min-width: 0;
    font-weight: 600;
}

.sa-dot {
    width: .55rem;
    height: .55rem;
    border-radius: 50%;
    flex: 0 0 auto;
}

.sa-dot-green {
    background: #22c55e;
}

.sa-dot-gray {
    background: #9ca3af;
}

.sa-net-pos {
    color: #247244;
    font-weight: 700;
}

.sa-net-neg {
    color: #b42318;
    font-weight: 700;
}

.sa-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .25rem .55rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 700;
    line-height: 1;
    white-space: nowrap;
}

.sa-badge-green {
    background: #eaf7ef;
    color: #247244;
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

.sa-audit-list {
    padding: .35rem 1rem .7rem;
}

.sa-audit-item {
    display: flex;
    gap: .75rem;
    padding: .85rem 0;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .07));
    transition: background-color .16s ease;
}

.sa-audit-item:last-child {
    border-bottom: none;
}

.sa-audit-item:hover {
    background: rgba(55, 138, 221, .04);
    margin-inline: -.55rem;
    padding-inline: .55rem;
    border-radius: .75rem;
}

.sa-audit-icon {
    width: 34px;
    height: 34px;
    border-radius: .75rem;
    background: var(--color-background-secondary, #f3f4f6);
    color: var(--color-text-secondary, #6b7280);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
}

.sa-audit-content {
    min-width: 0;
    flex: 1;
}

.sa-audit-action {
    font-size: .8rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    margin-bottom: .15rem;
}

.sa-audit-meta {
    font-size: .75rem;
    color: var(--color-text-secondary, #6b7280);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sa-audit-time {
    margin-top: .15rem;
    font-size: .7rem;
    color: var(--color-text-tertiary, #9ca3af);
}

@media (max-width: 1100px) {
    .sa-metric-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-chart-grid,
    .sa-content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-action-group {
        width: 100%;
    }

    .sa-action-link {
        flex: 1;
        justify-content: center;
    }

    .sa-metric-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .sa-chart-wrap {
        height: 240px;
    }
}
</style>

<div class="sa-dashboard">

    <div class="sa-header">
        <div class="sa-title">
            <h2>
                <i class="ti ti-dashboard" aria-hidden="true"></i>
                Platform Overview
            </h2>
            <p>Ringkasan performa seluruh toko dan aktivitas sistem hari ini.</p>
        </div>

        <div class="sa-action-group">
            <a class="sa-action-link" href="<?= url('/superadmin/reports') ?>">
                <i class="ti ti-file-analytics" aria-hidden="true"></i>
                Laporan
            </a>

            <a class="sa-action-link" href="<?= url('/superadmin/audit') ?>">
                <i class="ti ti-history" aria-hidden="true"></i>
                Audit
            </a>
        </div>
    </div>

    <div class="sa-metric-grid">
        <div class="sa-metric-card">
            <div class="sa-metric-icon sa-icon-blue">
                <i class="ti ti-building-store" aria-hidden="true"></i>
            </div>
            <span class="sa-metric-label">Toko terpantau</span>
            <div class="sa-metric-value"><?= (int) $totals['stores'] ?></div>
            <span class="sa-metric-sub">seluruh cabang</span>
        </div>

        <div class="sa-metric-card">
            <div class="sa-metric-icon sa-icon-green">
                <i class="ti ti-receipt" aria-hidden="true"></i>
            </div>
            <span class="sa-metric-label">Transaksi hari ini</span>
            <div class="sa-metric-value"><?= number_format((int) $totals['transactions']) ?></div>
            <span class="sa-metric-sub">lintas toko</span>
        </div>

        <div class="sa-metric-card">
            <div class="sa-metric-icon sa-icon-orange">
                <i class="ti ti-cash" aria-hidden="true"></i>
            </div>
            <span class="sa-metric-label">Omzet hari ini</span>
            <div class="sa-metric-value"><?= formatRupiah($totals['revenue']) ?></div>
            <span class="sa-metric-sub">gross revenue</span>
        </div>

        <div class="sa-metric-card">
            <div class="sa-metric-icon <?= (float) $totals['net'] >= 0 ? 'sa-icon-green' : 'sa-icon-red' ?>">
                <i class="ti ti-trending-up" aria-hidden="true"></i>
            </div>
            <span class="sa-metric-label">Net hari ini</span>
            <div class="sa-metric-value <?= (float) $totals['net'] >= 0 ? 'sa-net-pos' : 'sa-net-neg' ?>">
                <?= formatRupiah($totals['net']) ?>
            </div>
            <span class="sa-metric-sub">setelah pengeluaran</span>
        </div>
    </div>

    <div class="sa-chart-grid">

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">Grafik Performa Toko</div>
                    <div class="sa-panel-subtitle">Perbandingan omzet, pengeluaran, dan net per cabang.</div>
                </div>
            </div>

            <div class="sa-chart-box">
                <div class="sa-chart-legend">
                    <span class="sa-chart-legend-item">
                        <span class="sa-chart-dot sa-chart-blue"></span>
                        Omzet
                    </span>

                    <span class="sa-chart-legend-item">
                        <span class="sa-chart-dot sa-chart-red"></span>
                        Pengeluaran
                    </span>

                    <span class="sa-chart-legend-item">
                        <span class="sa-chart-dot sa-chart-green"></span>
                        Net
                    </span>
                </div>

                <div class="sa-chart-wrap">
                    <canvas id="saStorePerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">Komposisi Net</div>
                    <div class="sa-panel-subtitle">Distribusi net berdasarkan toko.</div>
                </div>
            </div>

            <div class="sa-chart-box">
                <div class="sa-chart-wrap">
                    <canvas id="saNetCompositionChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="sa-content-grid">

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">Performa Toko Hari Ini</div>
                    <div class="sa-panel-subtitle">Omzet, pengeluaran, dan net per cabang.</div>
                </div>
            </div>

            <div class="sa-table-wrap">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>Toko</th>
                            <th>Transaksi</th>
                            <th>Omzet</th>
                            <th>Pengeluaran</th>
                            <th>Net</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stores as $store): ?>
                            <?php
                            $isActive = ($store['status'] ?? '') === 'active';
                            $netVal = (float) ($store['net'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="sa-store-name">
                                        <span class="sa-dot <?= $isActive ? 'sa-dot-green' : 'sa-dot-gray' ?>"></span>
                                        <?= e($store['store_name'] ?? '-') ?>
                                    </div>
                                </td>
                                <td><?= number_format((int) ($store['transaction_count'] ?? 0)) ?></td>
                                <td><?= formatRupiah($store['revenue'] ?? 0) ?></td>
                                <td><?= formatRupiah($store['expenses'] ?? 0) ?></td>
                                <td class="<?= $netVal >= 0 ? 'sa-net-pos' : 'sa-net-neg' ?>">
                                    <?= formatRupiah($store['net'] ?? 0) ?>
                                </td>
                                <td>
                                    <span class="sa-badge <?= $isActive ? 'sa-badge-green' : 'sa-badge-muted' ?>">
                                        <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($stores)): ?>
                            <tr>
                                <td colspan="6" class="sa-empty">
                                    Tidak ada data toko untuk hari ini.
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
                    <div class="sa-panel-title">Aktivitas Terbaru</div>
                    <div class="sa-panel-subtitle">Jejak audit platform.</div>
                </div>
            </div>

            <div class="sa-audit-list">
                <?php foreach ($recentAudit as $audit): ?>
                    <?php
                    $action = strtolower($audit['action'] ?? '');
                    $iconClass = $auditIcons[$action] ?? 'ti-activity';
                    ?>
                    <div class="sa-audit-item">
                        <div class="sa-audit-icon">
                            <i class="ti <?= e($iconClass) ?>" aria-hidden="true"></i>
                        </div>

                        <div class="sa-audit-content">
                            <div class="sa-audit-action">
                                <?= e(strtoupper($audit['action'] ?? 'ACTIVITY')) ?>
                            </div>

                            <div class="sa-audit-meta">
                                <?= e($audit['user_name'] ?? 'System') ?>
                                ·
                                <?= e($audit['store_name'] ?? 'Global') ?>
                            </div>

                            <div class="sa-audit-time">
                                <?= e($audit['created_at'] ?? '-') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($recentAudit)): ?>
                    <div class="sa-empty">
                        Belum ada aktivitas audit.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

<script>
(function () {
    const storeLabels = <?= $chartStoreLabels ?>;
    const revenueData = <?= $chartRevenue ?>;
    const expenseData = <?= $chartExpenses ?>;
    const netData = <?= $chartNet ?>;

    const textColor = '#6b7280';
    const gridColor = 'rgba(15, 23, 42, 0.07)';

    const performanceCanvas = document.getElementById('saStorePerformanceChart');
    const netCanvas = document.getElementById('saNetCompositionChart');

    if (performanceCanvas && storeLabels.length > 0) {
        new Chart(performanceCanvas, {
            type: 'bar',
            data: {
                labels: storeLabels,
                datasets: [
                    {
                        label: 'Omzet',
                        data: revenueData,
                        backgroundColor: '#378add',
                        borderRadius: 6
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData,
                        backgroundColor: '#e24b4a',
                        borderRadius: 6
                    },
                    {
                        label: 'Net',
                        data: netData,
                        backgroundColor: '#1d9e75',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.dataset.label + ': Rp ' + context.parsed.y + ' jt';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            }
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            callback: function (value) {
                                return value + ' jt';
                            }
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    if (netCanvas && storeLabels.length > 0) {
        new Chart(netCanvas, {
            type: 'doughnut',
            data: {
                labels: storeLabels,
                datasets: [
                    {
                        label: 'Net',
                        data: netData.map(value => Math.max(value, 0)),
                        backgroundColor: [
                            '#378add',
                            '#1d9e75',
                            '#ba7517',
                            '#8b5cf6',
                            '#06b6d4',
                            '#f97316',
                            '#22c55e',
                            '#64748b'
                        ],
                        borderWidth: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            boxWidth: 10,
                            boxHeight: 10,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.label + ': Rp ' + context.parsed + ' jt';
                            }
                        }
                    }
                }
            }
        });
    }
})();
</script>