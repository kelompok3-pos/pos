<?php

require_once __DIR__ . '/../Controller.php';

// Nama class disamakan persis dengan nama file fisik
class KasirDashboardController extends Controller 
{
    public function index(): void
    {
        // Koneksi mandiri langsung di dalam method agar tidak null
        $host     = 'localhost';
        $dbname   = 'pos_db';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die("Koneksi database di dashboard kasir gagal: " . $e->getMessage());
        }

        // ---- Total items sold ----
        $stmtProduk = $pdo->query("SELECT SUM(quantity) AS total FROM transaction_items");
        $totalProdukTerjual = $stmtProduk->fetchColumn() ?? 0;

        // ---- Total revenue ----
        $stmtPenjualan = $pdo->query("SELECT SUM(total_price) AS total FROM transactions");
        $totalPenjualan = $stmtPenjualan->fetchColumn() ?? 0;

        // ---- Total transactions ----
        $stmtTrx = $pdo->query("SELECT COUNT(*) AS total FROM transactions");
        $totalTransaksi = $stmtTrx->fetchColumn() ?? 0;

        // ---- Sales chart for last 6 months ----
        $stmtGrafik = $pdo->query("
            SELECT
                DATE_FORMAT(created_at, '%b %Y') AS bulan_label,
                DATE_FORMAT(created_at, '%Y-%m') AS bulan_sort,
                SUM(total_price) AS total,
                COUNT(*) AS jumlah_transaksi
            FROM transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY bulan_sort, bulan_label
            ORDER BY bulan_sort ASC
        ");
        $grafikData = $stmtGrafik->fetchAll();

        $grafikLabel  = json_encode(array_column($grafikData, 'bulan_label'));
        $grafikTotal  = json_encode(array_map('floatval', array_column($grafikData, 'total')));
        $grafikJumlah = json_encode(array_map('intval', array_column($grafikData, 'jumlah_transaksi')));

        // ---- 5 Recent transactions ----
        $stmtRecent = $pdo->query("
            SELECT t.transaction_code, t.total_price, t.created_at, u.name AS cashier
            FROM transactions t
            JOIN users u ON t.cashier_id = u.id
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        $recentTrx = $stmtRecent->fetchAll();

        // ---- 5 Top selling products ----
        $stmtTop = $pdo->query("
            SELECT product_name, SUM(quantity) AS total_terjual, SUM(subtotal) AS total_omzet
            FROM transaction_items
            GROUP BY product_name
            ORDER BY total_terjual DESC
            LIMIT 5
        ");
        $topProduk = $stmtTop->fetchAll();
        
        // Membuka scope HTML agar variabel di atas langsung terbaca di bawah
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard Kasir — MyToko Luxury Dark</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
            @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap');
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <style>
                :root {
                    --lx-bg:       #0e0f14;
                    --lx-card:     #16181f;
                    --lx-card2:    #1c1f28;
                    --lx-border:   #2a2d38;
                    --lx-gold:     #c9a84c;
                    --lx-gold2:    #e8c97a;
                    --lx-gold-bg:  rgba(201,168,76,0.08);
                    --lx-text:     #f0eee8;
                    --lx-muted:    #8b8fa8;
                    --lx-success:  #3ecf8e;
                    --lx-danger:   #f87171;
                    --lx-info:     #60a5fa;
                }

                body { font-family: 'DM Sans', sans-serif; background: var(--lx-bg); color: var(--lx-text); margin: 0; }
                .main-content { padding: 40px 48px; min-height: 100vh; }
                
                .topbar span.badge-top { background: var(--lx-gold-bg); color: var(--lx-gold2); border: 1px solid rgba(201,168,76,0.2); border-radius: 8px; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; margin-bottom: 8px; display: inline-block; }
                .topbar h1 { font-family: 'DM Serif Display', serif; font-size: 2.2rem; font-weight: 400; color: var(--lx-text); margin: 0; letter-spacing: 0.5px; }
                .topbar h1 span { color: var(--lx-gold2); }
                .topbar p { color: var(--lx-muted); font-size: 0.95rem; font-weight: 400; margin: 8px 0 0; }
                .date-badge { background: var(--lx-card); border: 1px solid var(--lx-border); border-radius: 12px; padding: 12px 20px; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 8px; color: var(--lx-muted); }
                
                .stat-card { background: var(--lx-card); border-radius: 20px; padding: 28px; border: 1px solid var(--lx-border); transition: transform 0.2s; }
                .stat-card:hover { transform: translateY(-3px); border-color: var(--lx-gold); }
                .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 18px; }
                .stat-label { font-size: 0.8rem; color: var(--lx-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 1px; }
                .stat-value { font-size: 2rem; font-weight: 800; color: var(--lx-text); }
                .stat-value span.val-muted { font-size: 1rem; color: var(--lx-muted); font-weight: 500; font-family: 'DM Sans', sans-serif; }
                
                .card-box { background: var(--lx-card); border-radius: 24px; border: 1px solid var(--lx-border); overflow: hidden; }
                .card-box-header { padding: 24px 28px; border-bottom: 1px solid var(--lx-border); display: flex; align-items: center; justify-content: space-between; background: var(--lx-card2); }
                .card-box-header h5 { font-family: 'DM Sans', sans-serif; font-weight: 700; font-size: 1.1rem; color: var(--lx-text); margin: 0; letter-spacing: 0.5px; }
                .card-box-body { padding: 28px; }
                
                .table thead th { background: var(--lx-card2); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--lx-muted); padding: 16px 20px; letter-spacing: 1px; border-bottom: 1px solid var(--lx-border); }
                .table tbody td { padding: 18px 20px; font-size: 0.95rem; color: var(--lx-text); border-bottom: 1px solid rgba(42,45,56,0.5); vertical-align: middle; }
                .lx-table td span.lx-gold-val { color: var(--lx-gold2); }
                .lx-badge-success { background: rgba(62,207,142,0.1); color: var(--lx-success); padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: 1px solid rgba(62,207,142,0.2); }
                
                .produk-item { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--lx-border); }
                .produk-item:last-child { border-bottom: none; }
                .produk-rank { width: 34px; height: 34px; border-radius: 10px; background: var(--lx-card2); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700; color: var(--lx-muted); border: 1px solid var(--lx-border); }
                .produk-rank.gold { background: var(--lx-gold-bg); color: var(--lx-gold); border-color: rgba(201,168,76,0.2); }
                .produk-rank.silver { background: #1c1c1c; color: #a1a1a1; }
                .produk-rank.bronze { background: #2c2111; color: #a3733a; }
                .produk-bar-wrap { flex: 1; }
                .produk-name { font-size: 0.95rem; font-weight: 600; color: var(--lx-text); margin-bottom: 6px; }
                .produk-bar { height: 8px; border-radius: 99px; background: var(--lx-border); overflow: hidden; }
                .produk-bar-fill { height: 100%; background: linear-gradient(90deg, var(--lx-gold), var(--lx-gold2)); border-radius: 99px; }
                .produk-count { font-size: 0.95rem; font-weight: 700; color: var(--lx-gold2); }
            </style>
        </head>
        <body>

        <main class="main-content">
            <div class="topbar d-flex justify-content-between align-items-center mb-5">
                <div>
                    <span class="badge-top">SISTEM KASIR MyTokoGue</span>
                    <h1>Selamat Datang di <span>Dashboard Performa</span></h1>
                    <p>Memantau omzet, volume transaksi, dan aktivitas kasir real-time</p>
                </div>
                <div class="date-badge"><i class="bi bi-calendar3 lx-gold-text"></i> <?= date('d F Y') ?></div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="d-flex justify-content-between">
                            <span class="stat-label">Produk Terjual</span>
                            <div class="stat-icon" style="background: rgba(167,243,208,0.05); color: var(--lx-success);"><i class="bi bi-box-seam"></i></div>
                        </div>
                        <div class="stat-value text-white"><?= number_format($totalProdukTerjual, 0, ',', '.') ?> <span class="val-muted">Pcs</span></div>
                        <p class="text-muted small mt-2 mb-0"><i class="bi bi-graph-up text-success"></i> Item sukses keluar gudang</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="d-flex justify-content-between">
                            <span class="stat-label">Total Penjualan</span>
                            <div class="stat-icon" style="background: var(--lx-gold-bg); color: var(--lx-gold);"><i class="bi bi-wallet2"></i></div>
                        </div>
                        <div class="stat-value lx-gold-text">
                            <?= 'Rp ' . number_format($totalPenjualan, 0, ',', '.'); ?>
                        </div> 
                        <p class="text-muted small mt-2 mb-0"><i class="bi bi-currency-dollar lx-gold-text"></i> Akumulasi omzet bruto</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="d-flex justify-content-between">
                            <span class="stat-label">Volume Transaksi</span>
                            <div class="stat-icon" style="background: rgba(96,165,250,0.05); color: var(--lx-info);"><i class="bi bi-receipt"></i></div>
                        </div>
                        <div class="stat-value text-white"><?= number_format($totalTransaksi, 0, ',', '.') ?> <span class="val-muted">Trx</span></div>
                        <p class="text-muted small mt-2 mb-0"><i class="bi bi-check-circle-fill text-info"></i> Nota penjualan sukses tercetak</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="card-box h-100">
                        <div class="card-box-header">
                            <h5><i class="bi bi-bar-chart-fill lx-gold-text me-2"></i>Grafik Tren Penjualan 6 Bulan</h5>
                        </div>
                        <div class="card-box-body"><canvas id="grafikPenjualan" height="280"></canvas></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-box h-100">
                        <div class="card-box-header"><h5><i class="bi bi-trophy-fill lx-gold-text me-2"></i>Produk Terlaris</h5></div>
                        <div class="card-box-body">
                            <?php
                            $maxTerjual = !empty($topProduk) ? max(array_column($topProduk, 'total_terjual')) : 1;
                            $rankClass  = ['gold', 'silver', 'bronze', '', ''];
                            foreach ($topProduk as $idx => $prod):
                                $pct = ($prod['total_terjual'] / $maxTerjual) * 100;
                            ?>
                            <div class="produk-item">
                                <div class="produk-rank <?= $rankClass[$idx] ?? '' ?>"><?= $idx+1 ?></div>
                                <div class="produk-bar-wrap">
                                    <div class="produk-name"><?= htmlspecialchars($prod['product_name']) ?></div>
                                    <div class="produk-bar"><div class="produk-bar-fill" style="width:<?= $pct ?>%;"></div></div>
                                </div>
                                <div class="produk-count"><?= $prod['total_terjual'] ?> <span class="val-muted fs-7">Pcs</span></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box lx-table">
                <div class="card-box-header"><h5><i class="bi bi-clock-history lx-gold-text me-2"></i>Transaksi Masuk Terbaru</h5></div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr><th>Kode Nota</th><th>Total Belanja</th><th>Waktu Transaksi</th><th class="text-center">Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTrx)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4 small">Belum ada transaksi hari ini</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentTrx as $trx): ?>
                                <tr>
                                    <td><span class="font-monospace text-white fw-bold"><?= htmlspecialchars($trx['transaction_code']) ?></span></td>
                                    <td><span class="lx-gold-val fw-bold"><?= 'Rp ' . number_format($trx['total_price'], 0, ',', '.'); ?></span></td>
                                    <td class="text-muted small"><?= date('d M Y • H:i', strtotime($trx['created_at'])) ?></td>
                                    <td class="text-center"><span class="lx-badge-success">Selesai</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <script>
        const ctx = document.getElementById('grafikPenjualan').getContext('2d');
        
        // Gradasi Gold Mewah untuk Chart Bar
        const goldGradient = ctx.createLinearGradient(0, 0, 0, 300);
        goldGradient.addColorStop(0, '#e8c97a');
        goldGradient.addColorStop(1, 'rgba(201,168,76,0.05)');

        new Chart(ctx, {
            data: {
                labels: <?= $grafikLabel ?>,
                datasets: [
                    {
                        label: 'Omzet Penjualan (Rupiah)',
                        data: <?= $grafikTotal ?>,
                        type: 'bar',
                        backgroundColor: goldGradient,
                        borderRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Volume Transaksi',
                        data: <?= $grafikJumlah ?>,
                        type: 'line',
                        borderColor: '#3ecf8e',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#3ecf8e',
                        pointRadius: 4,
                        yAxisID: 'y2',
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Sembunyikan legenda agar lebih mewah
                },
                scales: {
                    y: { 
                        position: 'left', 
                        grid: { color: 'rgba(255,255,255,0.04)' }, 
                        ticks: { color: '#8b8fa8', font: { size: 10 }, callback: val => 'Rp ' + (val/1000000).toFixed(1) + 'Jt' } 
                    },
                    y2: { 
                        position: 'right', 
                        grid: { drawOnChartArea: false }, 
                        ticks: { color: '#3ecf8e', font: { size: 10 } } 
                    },
                    x: { ticks: { color: '#8b8fa8', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } }
                }
            }
        });
        </script>
        </body>
        </html>
        <?php
    }
}