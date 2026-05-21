<?php
// =========================================================================
// dashboard.php — Premium Light Dashboard (High Contrast & Big Font)
// Ramah pengguna dewasa, kontras tinggi, teks besar & jelas.
// =========================================================================

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
    die("Koneksi database gagal: " . $e->getMessage());
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

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir — TokoKu Clean Light</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet text/css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --primary-green: #059669;
            --primary-dark: #064e3b;
            --text-main: #0f172a;
            --text-muted: #475569;
            --sidebar-bg: #064e3b;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar-bg); position: fixed; top: 0; left: 0; display: flex; flex-direction: column; z-index: 100; box-shadow: 4px 0 15px rgba(0,0,0,0.05); }
        .sidebar-brand { padding: 32px 24px 20px; color: #fff; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand span { color: #34d399; font-weight: 900; }
        .sidebar-nav { padding: 20px 16px; flex: 1; }
        .nav-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #a7f3d0; letter-spacing: 1.5px; padding: 8px 12px; margin-bottom: 6px; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px; color: #d1fae5; text-decoration: none; font-size: 0.95rem; font-weight: 600; margin-bottom: 6px; transition: all 0.2s; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-nav a.active { background: #34d399; color: #064e3b; font-weight: 700; box-shadow: 0 4px 12px rgba(52, 211, 153, 0.2); }
        .sidebar-nav a i { font-size: 1.1rem; }

        .main-content { margin-left: 260px; padding: 40px 48px; min-height: 100vh; }
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px; }
        .topbar h1 { font-size: 1.9rem; font-weight: 800; color: var(--primary-dark); letter-spacing: -0.5px; margin: 0; }
        .topbar p { color: var(--text-muted); margin: 6px 0 0; font-size: 0.95rem; font-weight: 600; }
        .date-badge { background: var(--bg-card); border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px 20px; font-size: 0.95rem; color: var(--text-main); font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .date-badge i { color: var(--primary-green); font-size: 1.05rem; }

        .stat-card { background: var(--bg-card); border-radius: 20px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08); border-color: var(--primary-green); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 18px; }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .stat-value { font-size: 2rem; font-weight: 800; color: var(--primary-dark); }

        .card-box { background: var(--bg-card); border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .card-box-header { padding: 24px 28px; border-bottom: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between; background: #fafafa; }
        .card-box-header h5 { font-weight: 800; font-size: 1.1rem; color: var(--primary-dark); margin: 0; display: flex; align-items: center; gap: 10px; }
        .card-box-body { padding: 28px; }

        .table { margin: 0; }
        .table thead th { background: #f1f5f9; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--primary-dark); padding: 16px 20px; border-bottom: 2px solid #cbd5e1; letter-spacing: 0.5px; }
        .table tbody td { padding: 18px 20px; font-size: 0.95rem; color: var(--text-main); font-weight: 600; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table tbody tr:hover { background-color: #f8fafc; }
        .badge-success { background: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; border: 1px solid #a7f3d0; }

        .produk-item { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid #edf2f7; }
        .produk-item:last-child { border-bottom: none; }
        .produk-rank { width: 34px; height: 34px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; color: var(--text-muted); }
        .produk-rank.gold { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .produk-rank.silver { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .produk-rank.bronze { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .produk-bar-wrap { flex: 1; }
        .produk-name { font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
        .produk-bar { height: 8px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
        .produk-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #059669); }
        .produk-count { font-size: 0.95rem; font-weight: 800; color: var(--primary-green); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand"><span>My</span>TokoGue</div>
    <nav class="sidebar-nav">
        <div class="nav-label">Kasir</div>
        <a href="#" class="active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="#"><i class="bi bi-cart-fill"></i> Halaman Kasir</a>
    </nav>
</aside>

<main class="main-content">
    <div class="topbar">
        <div>
            <h1>Dashboard Kasir</h1>
            <p>Ringkasan transaksi dan performa penjualan real-time</p>
        </div>
        <div class="date-badge"><i class="bi bi-calendar3"></i> <?= date('d F Y') ?></div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: #d1fae5; color: #065f46;"><i class="bi bi-box-seam-fill"></i></div>
                <div class="stat-label">Produk Terjual</div>
                <div class="stat-value"><?= number_format($totalProdukTerjual, 0, ',', '.') ?> <span style="font-size: 1rem; color: var(--text-muted); font-weight: 600;">Pcs</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e0f2fe; color: #0369a1;"><i class="bi bi-wallet2"></i></div>
                <div class="stat-label">Total Penjualan</div>
                <div class="stat-value" style="color: #047857;"><?= formatRupiah($totalPenjualan) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: #f3e8ff; color: #6b21a8;"><i class="bi bi-receipt-cutoff"></i></div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value"><?= number_format($totalTransaksi, 0, ',', '.') ?> <span style="font-size: 1rem; color: var(--text-muted); font-weight: 600;">Trx</span></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card-box">
                <div class="card-box-header">
                    <h5><i class="bi bi-graph-up-arrow" style="color: var(--primary-green);"></i> Grafik Volume & Omzet Penjualan</h5>
                </div>
                <div class="card-box-body"><canvas id="grafikPenjualan" height="105"></canvas></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-box" style="height:100%;">
                <div class="card-box-header"><h5><i class="bi bi-trophy-fill" style="color: #d97706;"></i> Produk Terlaris</h5></div>
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
                        <div class="produk-count"><?= $prod['total_terjual'] ?> <span style="font-size: 0.8rem; color: var(--text-muted);">Pcs</span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="card-box-header"><h5><i class="bi bi-clock-history" style="color: var(--primary-green);"></i> Log Transaksi Masuk Terbaru</h5></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Kode Nota</th><th>Nama Kasir</th><th>Total Belanja</th><th>Waktu Transaksi</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTrx as $trx): ?>
                    <tr>
                        <td><span style="font-family:monospace; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; color: var(--primary-dark); font-weight: 700; border: 1px solid #cbd5e1;"><?= htmlspecialchars($trx['transaction_code']) ?></span></td>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($trx['cashier']) ?></td>
                        <td style="font-weight: 800; color: #047857;"><?= formatRupiah($trx['total_price']) ?></td>
                        <td style="color: var(--text-muted); font-weight: 700;"><?= date('d M Y • H:i', strtotime($trx['created_at'])) ?></td>
                        <td><span class="badge-success">Selesai</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
const ctx = document.getElementById('grafikPenjualan').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= $grafikLabel ?>,
        datasets: [
            {
                label: 'Omzet (Rp)',
                data: <?= $grafikTotal ?>,
                backgroundColor: '#059669',
                borderRadius: 6,
                yAxisID: 'y'
            },
            {
                label: 'Jumlah Transaksi',
                data: <?= $grafikJumlah ?>,
                type: 'line',
                borderColor: '#2563eb',
                borderWidth: 4,
                pointBackgroundColor: '#2563eb',
                pointRadius: 6,
                yAxisID: 'y2',
                tension: 0.2
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#0f172a', font: { family: 'Plus Jakarta Sans', size: 13, weight: 700 } } }
        },
        scales: {
            y: { position: 'left', grid: { color: '#e2e8f0' }, ticks: { color: '#334155', font: { size: 12, weight: 600 }, callback: val => 'Rp ' + (val/1000).toFixed(0) + 'k' } },
            y2: { position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#2563eb', font: { size: 12, weight: 600 } } },
            x: { ticks: { color: '#334155', font: { size: 12, weight: 600 } } }
        }
    }
});
</script>
</body>
</html>