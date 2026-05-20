<?php
// =========================================================================
// users.php — Halaman List User (Admin - Premium Light Theme)
// Tugas: Moses | Fitur: Membuat halaman tabel list user kontras tinggi
// =========================================================================

// 1. KONEKSI LANGSUNG KE DATABASE
$host    = 'localhost';
$dbname  = 'pos_db'; 
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

// Ambil semua user yang belum dihapus (soft delete)
$stmt = $pdo->query("SELECT id, nama, email, role, created_at FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$totalUser  = count($users);
$totalAdmin = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$totalKasir = count(array_filter($users, fn($u) => $u['role'] === 'kasir'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User — TokoKu Clean Light</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f8fafc; /* Abu-abu sangat muda bersih */
            --bg-card: #ffffff; /* Putih murni kontras */
            --primary-green: #059669; /* Hijau Zamrud Tegas */
            --primary-dark: #064e3b; /* Hijau Tua untuk Teks Utama */
            --text-main: #0f172a; /* Hitam pekat agar tulisan jelas */
            --text-muted: #475569; /* Abu-abu gelap untuk sub-teks */
            --sidebar-bg: #064e3b; /* Samping Hijau Tua Elegan */
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* ---- SIDEBAR CLEAN ---- */
        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar-bg); position: fixed; top: 0; left: 0; display: flex; flex-direction: column; z-index: 100; box-shadow: 4px 0 15px rgba(0,0,0,0.05); }
        .sidebar-brand { padding: 32px 24px 20px; color: #fff; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand span { color: #34d399; font-weight: 900; }
        .sidebar-nav { padding: 20px 16px; flex: 1; }
        .nav-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #a7f3d0; letter-spacing: 1.5px; padding: 8px 12px; margin-bottom: 6px; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px; color: #d1fae5; text-decoration: none; font-size: 0.95rem; font-weight: 600; margin-bottom: 6px; transition: all 0.2s; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-nav a.active { background: #34d399; color: #064e3b; font-weight: 700; box-shadow: 0 4px 12px rgba(52, 211, 153, 0.2); }
        .sidebar-nav a i { font-size: 1.1rem; }

        /* ---- MAIN CONTENT ---- */
        .main-content { margin-left: 260px; padding: 40px 48px; min-height: 100vh; }
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px; }
        .topbar h1 { font-size: 1.9rem; font-weight: 800; color: var(--primary-dark); letter-spacing: -0.5px; margin: 0; }
        .topbar p { color: var(--text-muted); margin: 6px 0 0; font-size: 0.95rem; font-weight: 600; }

        /* ---- HIGH CONTRAST CARDS ---- */
        .stat-card { background: var(--bg-card); border-radius: 20px; padding: 24px 28px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }

        /* ---- TABLE CARD ---- */
        .card-table { background: var(--bg-card); border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .card-table-header { padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #edf2f7; background: #fafafa; }
        .card-table-header h5 { font-weight: 800; font-size: 1.1rem; color: var(--primary-dark); margin: 0; }

        /* ---- HIGH CONTRAST TABLES ---- */
        .table { margin: 0; }
        .table thead th { background: #f1f5f9; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--primary-dark); padding: 16px 20px; border-bottom: 2px solid #cbd5e1; letter-spacing: 0.5px; white-space: nowrap; }
        .table tbody td { padding: 18px 20px; font-size: 0.95rem; color: var(--text-main); font-weight: 600; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table tbody tr:hover { background-color: #f8fafc; }

        .avatar { width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; color: #fff; flex-shrink: 0; }
        .badge-role { padding: 6px 14px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; }
        .badge-admin { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .badge-kasir { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

        /* ---- BUTTON CUSTOM (UKURAN BESAR & JELAS) ---- */
        .btn-primary-custom { background: var(--primary-green); color: #fff; border: none; border-radius: 10px; padding: 10px 20px; font-size: 0.95rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; cursor: pointer; transition: background 0.15s; }
        .btn-primary-custom:hover { background: #047857; color: #fff; }

        /* ---- INPUT CARI ---- */
        .search-box { border: 2px solid #cbd5e1; border-radius: 10px; padding: 10px 14px 10px 40px; font-size: 0.9rem; font-weight: 600; outline: none; width: 260px; transition: border-color 0.15s; color: var(--text-main); }
        .search-box:focus { border-color: var(--primary-green); }
        .search-wrapper { position: relative; display: inline-block; }
        .search-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem; }

        .empty-state { text-align: center; padding: 48px 16px; color: var(--text-muted); font-weight: 600; }
        .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 12px; color: #94a3b8; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <span>My</span>TokoGue
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Admin</div>
        <a href="dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>

        <div class="nav-label">Produk</div>
        <a href="#"><i class="bi bi-box-seam-fill"></i> List Produk</a>

        <div class="nav-label">Users</div>
        <a href="users.php" class="active"><i class="bi bi-people-fill"></i> List User</a>
        <a href="tambah_user.php"><i class="bi bi-person-plus-fill"></i> Tambah User</a>

        <div class="nav-label">Transaksi</div>
        <a href="#"><i class="bi bi-receipt-cutoff"></i> Daftar Transaksi</a>
        <a href="#"><i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan</a>
    </nav>
</aside>

<main class="main-content">

    <div class="topbar">
        <div>
            <h1>Manajemen User</h1>
            <p>Kelola hak akses akun administrator dan kasir aktif toko</p>
        </div>
        <a href="tambah_user.php" class="btn-primary-custom">
            <i class="bi bi-person-plus-fill"></i> Tambah User Baru
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Total User</div>
                    <div class="stat-value"><?= $totalUser ?> <span style="font-size: 0.95rem; color: var(--text-muted); font-weight: 600;">Orang</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7; color:#d97706;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="stat-label">Hak Akses Admin</div>
                    <div class="stat-value"><?= $totalAdmin ?> <span style="font-size: 0.95rem; color: var(--text-muted); font-weight: 600;">Akun</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#d1fae5; color:#065f46;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Hak Akses Kasir</div>
                    <div class="stat-value"><?= $totalKasir ?> <span style="font-size: 0.95rem; color: var(--text-muted); font-weight: 600;">Akun</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-table">
        <div class="card-table-header">
            <h5><i class="bi bi-people-fill me-2" style="color: var(--primary-green);"></i>Daftar User Aktif Sistem</h5>
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" class="search-box" id="searchInput" placeholder="Ketik nama / email kasir...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="userTable">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Role Jabatan</th>
                        <th>Tanggal Terdaftar</th>
                        <th style="width:140px; text-align:center;">Aksi Pengaturan</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-people-fill"></i>
                                Tidak ditemukan data user aktif di dalam database.
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    // Palet warna inisial avatar kontras tinggi
                    $colors = ['#10b981','#7c3aed','#0284c7','#059669','#ea580c','#dc2626','#db2777','#4d7c0f'];
                    foreach ($users as $index => $user):
                        $initial = strtoupper(substr($user['nama'], 0, 1));
                        $color = $colors[$index % count($colors)];
                    ?>
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;"><?= $index + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar" style="background:<?= $color ?>; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"><?= $initial ?></div>
                                <span style="font-weight:700; color: var(--text-main);"><?= htmlspecialchars($user['nama']) ?></span>
                            </div>
                        </td>
                        <td style="color: var(--text-muted); font-weight: 700;"><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge-role <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-kasir' ?>">
                                <i class="bi <?= $user['role'] === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-fill' ?> me-1"></i>
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-weight: 700;">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="edit_user.php?id=<?= $user['id'] ?>"
                                   class="btn btn-sm px-3 py-2"
                                   style="background:#f1f5f9; color:#1e293b; border:1px solid #cbd5e1; border-radius:8px; font-weight:700;"
                                   title="Edit Data">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                                <a href="hapus_user.php?id=<?= $user['id'] ?>"
                                   class="btn btn-sm px-3 py-2"
                                   style="background:#fef2f2; color:#dc2626; border:1px solid #fca5a5; border-radius:8px; font-weight:700;"
                                   title="Hapus Akun"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus user <?= htmlspecialchars($user['nama']) ?>?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 28px; font-size: 0.85rem; color: var(--text-muted); font-weight: 700; border-top: 1px solid #edf2f7; background: #fafafa;">
            Total data terhitung: Menampilkan <?= $totalUser ?> user aktif dalam sistem tokomu.
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Fitur pencarian live pencocokan data nama atau email
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>