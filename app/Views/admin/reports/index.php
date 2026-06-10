<div class="page-hero"><div class="page-title"><h2><i class="ti ti-chart-bar" aria-hidden="true"></i> Reports</h2><p><?= isSuperAdmin() ? 'All stores' : 'Own store only' ?> sales and cashier performance.</p></div></div>
<form method="GET" action="<?= url('/reports') ?>" class="row g-2 mb-3">
<div class="col-md-3"><input type="date" name="from" class="form-input" value="<?= e($from) ?>"></div>
<div class="col-md-3"><input type="date" name="to" class="form-input" value="<?= e($to) ?>"></div>
<div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
<div class="col-md-4"><a class="btn btn-ghost" href="<?= url('/reports/export') ?>?from=<?= e($from) ?>&to=<?= e($to) ?>">Export CSV</a>
<button type="button" class="btn btn-ghost" onclick="window.print()">Print / PDF</button></div>
</form>
<?php $reportRevenue = array_reduce($transactions, fn(float $sum, array $row): float => $sum + (float) $row['total_price'], 0.0); ?>
<div class="toast toast-info flex-row flex-between"><strong><?= count($transactions) ?> transactions</strong><strong>Revenue: <?= formatRupiah($reportRevenue) ?></strong></div>
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card surface"><div class="card-body"><small>Daily Revenue</small><h4><?= formatRupiah($dailyRevenue) ?></h4></div></div></div>
<div class="col-md-4"><div class="card surface"><div class="card-body"><small>Weekly Revenue</small><h4><?= formatRupiah($weeklyRevenue) ?></h4></div></div></div>
<div class="col-md-4"><div class="card surface"><div class="card-body"><small>Monthly Revenue</small><h4><?= formatRupiah($monthlyRevenue) ?></h4></div></div></div>
</div>
<div class="row g-3 mb-4">
<div class="col-lg-7"><div class="card surface"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Invoice</th><th>Kasir</th><th>Total</th><th>Date</th></tr></thead><tbody><?php foreach ($transactions as $row): ?><tr><td><?= e($row['transaction_code']) ?></td><td><?= e($row['cashier_name']) ?></td><td><?= formatRupiah($row['total_price']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<div class="col-lg-5"><div class="card surface"><div class="card-body"><h5>Kasir Performance</h5><?php foreach ($cashiers as $row): ?><div class="flex-row flex-between border-bottom py-2"><span><?= e($row['name']) ?> (<?= $row['transaction_count'] ?> trx)</span><strong><?= formatRupiah($row['revenue']) ?></strong></div><?php endforeach; ?></div></div></div>
</div>
<div class="card surface"><div class="card-body"><h5>Best Selling Products</h5><?php foreach ($topProducts as $row): ?><div class="flex-row flex-between border-bottom py-2"><span><?= e($row['product_name']) ?></span><span><?= $row['total_terjual'] ?> pcs / <?= formatRupiah($row['total_omzet']) ?></span></div><?php endforeach; ?></div></div>
