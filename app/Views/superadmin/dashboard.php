<div class="page-hero">
  <div class="page-title"><span class="eyebrow">SUPER ADMIN</span><h2>Platform Overview</h2><p>Pantau performa seluruh toko dari satu layar.</p></div>
  <a class="btn btn-primary" href="<?= url('/superadmin/reports') ?>"><i class="ti ti-chart-bar"></i> Laporan lengkap</a>
</div>
<div class="metric-grid mb-4">
  <div class="metric-card"><span>Toko terpantau</span><strong><?= (int) $totals['stores'] ?></strong><small>seluruh cabang</small></div>
  <div class="metric-card"><span>Transaksi hari ini</span><strong><?= (int) $totals['transactions'] ?></strong><small>lintas toko</small></div>
  <div class="metric-card"><span>Omzet hari ini</span><strong><?= formatRupiah($totals['revenue']) ?></strong><small>gross revenue</small></div>
  <div class="metric-card"><span>Net hari ini</span><strong><?= formatRupiah($totals['net']) ?></strong><small>setelah pengeluaran</small></div>
</div>
<div class="row g-4">
  <div class="col-xl-8"><div class="card surface"><div class="card-body">
    <div class="section-heading"><div><h3>Performa Toko Hari Ini</h3><p>Omzet dan pengeluaran per cabang.</p></div></div>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Toko</th><th>Transaksi</th><th>Omzet</th><th>Pengeluaran</th><th>Net</th></tr></thead><tbody>
    <?php foreach ($stores as $store): ?><tr><td><strong><?= e($store['store_name']) ?></strong></td><td><?= (int) $store['transaction_count'] ?></td><td><?= formatRupiah($store['revenue']) ?></td><td><?= formatRupiah($store['expenses']) ?></td><td><strong><?= formatRupiah($store['net']) ?></strong></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div></div></div>
  <div class="col-xl-4"><div class="card surface"><div class="card-body"><div class="section-heading"><div><h3>Aktivitas Terbaru</h3><p>Jejak audit platform.</p></div></div>
    <div class="activity-list"><?php foreach ($recentAudit as $audit): ?><div class="activity-item"><span class="activity-icon"><i class="ti ti-activity"></i></span><div><strong><?= e(strtoupper($audit['action'])) ?></strong><p><?= e($audit['user_name'] ?? 'System') ?> · <?= e($audit['store_name'] ?? 'Global') ?></p><small><?= e($audit['created_at']) ?></small></div></div><?php endforeach; ?>
    <?php if ($recentAudit === []): ?><p class="text-muted">Belum ada aktivitas audit.</p><?php endif; ?></div>
  </div></div></div>
</div>
