<div class="page-hero">
  <div class="page-title"><span class="eyebrow">GLOBAL ANALYTICS</span><h2>Laporan Lintas Toko</h2><p>Bandingkan omzet, pengeluaran, dan net seluruh cabang.</p></div>
  <a class="btn btn-outline-primary" href="<?= url('/superadmin/reports/export?' . http_build_query(['from' => $from, 'to' => $to, 'store_id' => $selectedStore])) ?>"><i class="ti ti-download"></i> Export CSV</a>
</div>
<div class="card surface mb-4"><div class="card-body"><form method="GET" action="<?= url('/superadmin/reports') ?>" class="row g-3 align-items-end">
  <div class="col-md-3"><label class="form-label">Dari tanggal</label><input class="form-input" type="date" name="from" value="<?= e($from) ?>"></div>
  <div class="col-md-3"><label class="form-label">Sampai tanggal</label><input class="form-input" type="date" name="to" value="<?= e($to) ?>"></div>
  <div class="col-md-4"><label class="form-label">Toko</label><select class="form-input" name="store_id"><option value="">Semua toko</option><?php foreach ($storeOptions as $store): ?><option value="<?= (int) $store['id'] ?>" <?= $selectedStore === (int) $store['id'] ? 'selected' : '' ?>><?= e($store['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-2"><button class="btn btn-primary w-100"><i class="ti ti-filter"></i> Terapkan</button></div>
</form></div></div>
<div class="metric-grid mb-4">
  <div class="metric-card"><span>Transaksi</span><strong><?= (int) $totals['transactions'] ?></strong><small><?= e($from) ?> hingga <?= e($to) ?></small></div>
  <div class="metric-card"><span>Total omzet</span><strong><?= formatRupiah($totals['revenue']) ?></strong><small>semua transaksi</small></div>
  <div class="metric-card"><span>Pengeluaran</span><strong><?= formatRupiah($totals['expenses']) ?></strong><small>seluruh kategori</small></div>
  <div class="metric-card"><span>Estimasi net</span><strong><?= formatRupiah($totals['net']) ?></strong><small>omzet dikurangi pengeluaran</small></div>
</div>
<div class="card surface"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Toko</th><th>Status</th><th>Transaksi</th><th>Omzet</th><th>Pengeluaran</th><th>Net</th><th>Margin</th></tr></thead><tbody>
<?php foreach ($rows as $row): $margin = (float) $row['revenue'] > 0 ? ((float) $row['net'] / (float) $row['revenue']) * 100 : 0; ?><tr>
  <td><strong><?= e($row['store_name']) ?></strong></td><td><span class="status-pill <?= $row['status'] === 'active' ? 'is-success' : 'is-muted' ?>"><?= e($row['status']) ?></span></td><td><?= (int) $row['transaction_count'] ?></td><td><?= formatRupiah($row['revenue']) ?></td><td><?= formatRupiah($row['expenses']) ?></td><td><strong><?= formatRupiah($row['net']) ?></strong></td><td><?= number_format($margin, 1) ?>%</td>
</tr><?php endforeach; ?>
<?php if ($rows === []): ?><tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data pada rentang ini.</td></tr><?php endif; ?>
</tbody><tfoot><tr><th>TOTAL</th><th></th><th><?= (int) $totals['transactions'] ?></th><th><?= formatRupiah($totals['revenue']) ?></th><th><?= formatRupiah($totals['expenses']) ?></th><th><?= formatRupiah($totals['net']) ?></th><th></th></tr></tfoot></table></div></div>
