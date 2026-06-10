<div class="page-hero"><div class="page-title"><h2>Shift Kasir</h2><p>Buka dan tutup shift kasir Anda.</p></div></div>
<div class="card surface mb-3"><div class="card-body">
  <form action="<?= url('/kasir/shift/open') ?>" method="POST">
    <?= csrf_field() ?><label class="form-label">Kas Awal</label>
    <input class="form-input mb-2" type="number" name="opening_cash" min="0" required>
    <button class="btn btn-primary">Buka Shift</button>
  </form>
</div></div>
<div class="card surface"><div class="table-responsive"><table class="table">
  <thead><tr><th>Dibuka</th><th>Ditutup</th><th>Kas Awal</th><th>Kas Akhir</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody><?php foreach ($shifts as $shift): ?><tr>
    <td><?= e($shift['opened_at']) ?></td><td><?= e($shift['closed_at'] ?? '-') ?></td>
    <td><?= formatRupiah($shift['opening_cash']) ?></td><td><?= formatRupiah($shift['closing_cash'] ?? 0) ?></td>
    <td><?= e($shift['status']) ?></td><td>
    <?php if ($shift['status'] === 'open'): ?><form action="<?= url('/kasir/shift/close') ?>" method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $shift['id'] ?>"><input type="number" name="closing_cash" min="0" required><button class="btn btn-sm btn-primary">Tutup</button></form><?php endif; ?>
    </td>
  </tr><?php endforeach; ?></tbody>
</table></div></div>
