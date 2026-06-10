<?php $transactions ??= []; $date ??= ''; ?>

<style>
/* ── My Transactions — same design tokens as POS ── */
.mytx {
  --brand:      #2563eb;
  --brand-dark: #1e40af;
  --brand-soft: #dbeafe;
  --mint:       #10b981;
  --mint-dark:  #047857;
  --mint-soft:  #dcfce7;
  --ink:        #172033;
  --muted:      #64748b;
  --border:     #dbe6f3;
  --surface:    #ffffff;
  --bg:         #f8fafc;
  --radius:     12px;
  --radius-sm:  8px;
  --shadow:     0 1px 3px rgba(15,23,42,.07), 0 1px 2px rgba(15,23,42,.05);
  --shadow-md:  0 4px 16px rgba(15,23,42,.10);
  --font:       Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;

  font-family: var(--font);
  color: var(--ink);
}

/* ── Filter bar ─────────────────────────────── */
.mytx-filter {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}
.mytx-filter input[type="date"] {
  padding: 9px 14px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: .88rem;
  font-family: var(--font);
  color: var(--ink);
  background: var(--surface);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.mytx-filter input[type="date"]:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.mytx-filter .btn-filter {
  padding: 9px 18px;
  border: none;
  border-radius: var(--radius-sm);
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: #fff;
  font-size: .88rem;
  font-weight: 700;
  font-family: var(--font);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(37,99,235,.22);
  transition: opacity .15s;
}
.mytx-filter .btn-filter:hover { opacity: .88; }
.mytx-filter .btn-reset {
  padding: 9px 14px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  background: var(--surface);
  color: var(--muted);
  font-size: .88rem;
  font-weight: 600;
  font-family: var(--font);
  cursor: pointer;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: border-color .15s, color .15s;
}
.mytx-filter .btn-reset:hover { border-color: var(--brand); color: var(--brand); }

/* ── Stats strip ────────────────────────────── */
.mytx-stats {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.mytx-stat {
  flex: 1;
  min-width: 140px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 18px;
  box-shadow: var(--shadow);
}
.mytx-stat__label {
  font-size: .72rem;
  font-weight: 800;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-bottom: 6px;
}
.mytx-stat__value {
  font-size: 1.2rem;
  font-weight: 900;
  color: var(--ink);
}
.mytx-stat__value.brand { color: var(--brand-dark); }

/* ── Table card ─────────────────────────────── */
.mytx-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  overflow: hidden;
}

.mytx-table {
  width: 100%;
  border-collapse: collapse;
  font-family: var(--font);
  font-size: .875rem;
}
.mytx-table thead tr {
  background: var(--bg);
  border-bottom: 2px solid var(--border);
}
.mytx-table thead th {
  padding: 13px 18px;
  font-size: .72rem;
  font-weight: 900;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .07em;
  white-space: nowrap;
  text-align: left;
}
.mytx-table thead th:last-child { text-align: center; }

.mytx-table tbody tr {
  border-bottom: 1px solid var(--border);
  transition: background .12s;
}
.mytx-table tbody tr:last-child { border-bottom: none; }
.mytx-table tbody tr:hover { background: #f0f6ff; }

.mytx-table td {
  padding: 14px 18px;
  color: var(--ink);
  vertical-align: middle;
}

/* Invoice chip */
.invoice-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--brand-soft);
  color: var(--brand-dark);
  border-radius: 6px;
  padding: 3px 10px;
  font-size: .78rem;
  font-weight: 800;
  letter-spacing: .01em;
}

/* Total */
.tx-total {
  font-size: .9rem;
  font-weight: 800;
  color: var(--brand-dark);
}

/* Date */
.tx-date {
  font-size: .82rem;
  color: var(--muted);
  white-space: nowrap;
}
.tx-date strong {
  display: block;
  font-size: .86rem;
  font-weight: 700;
  color: var(--ink);
}

/* Action button */
.btn-view {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border: 1.5px solid var(--brand);
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--brand);
  font-size: .78rem;
  font-weight: 700;
  font-family: var(--font);
  text-decoration: none;
  transition: background .15s, color .15s;
  white-space: nowrap;
}
.btn-view:hover {
  background: var(--brand);
  color: #fff;
}
.td-action { text-align: center; }

/* ── Empty state ────────────────────────────── */
.mytx-empty {
  text-align: center;
  padding: 56px 24px;
  color: var(--muted);
}
.mytx-empty svg {
  width: 52px; height: 52px;
  margin-bottom: 14px;
  opacity: .25;
  display: block;
  margin-inline: auto;
}
.mytx-empty p { font-size: .9rem; margin-top: 6px; }
</style>

<?php
// Pre-compute stats
$txCount    = count($transactions);
$txTotal    = array_sum(array_column($transactions, 'total_price'));
?>

<div class="mytx">

  <!-- Filter Bar -->
  <form method="GET" action="<?= url('/kasir/my-transactions') ?>" class="mytx-filter">
    <input type="date" name="date" value="<?= e($date) ?>" title="Filter tanggal">
    <button type="submit" class="btn-filter">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
      </svg>
      Filter
    </button>
    <?php if ($date): ?>
      <a href="<?= url('/kasir/my-transactions') ?>" class="btn-reset">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M6 18L18 6M6 6l12 12"/></svg>
        Reset
      </a>
    <?php endif; ?>
  </form>

  <!-- Stats Strip -->
  <div class="mytx-stats">
    <div class="mytx-stat">
      <div class="mytx-stat__label">Total Transaksi</div>
      <div class="mytx-stat__value"><?= $txCount ?></div>
    </div>
    <div class="mytx-stat">
      <div class="mytx-stat__label">Total Pendapatan</div>
      <div class="mytx-stat__value brand"><?= formatRupiah($txTotal) ?></div>
    </div>
    <?php if ($date): ?>
    <div class="mytx-stat">
      <div class="mytx-stat__label">Filter Aktif</div>
      <div class="mytx-stat__value" style="font-size:.95rem;">
        <?= date('d M Y', strtotime($date)) ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Table -->
  <div class="mytx-card">
    <?php if (!$transactions): ?>
      <div class="mytx-empty">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <strong>Belum ada transaksi</strong>
        <p><?= $date ? 'Tidak ada transaksi pada tanggal ' . date('d M Y', strtotime($date)) . '.' : 'Transaksi yang kamu buat akan muncul di sini.' ?></p>
      </div>
    <?php else: ?>
      <table class="mytx-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Invoice</th>
            <th>Total</th>
            <th>Tanggal & Waktu</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transactions as $i => $row): ?>
            <?php
            $dt   = new DateTime($row['created_at']);
            $tgl  = $dt->format('d M Y');
            $jam  = $dt->format('H:i');
            ?>
            <tr>
              <td style="color:var(--muted);font-size:.8rem;font-weight:700;"><?= $i + 1 ?></td>
              <td>
                <span class="invoice-chip">
                  <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                  <?= e($row['transaction_code']) ?>
                </span>
              </td>
              <td class="tx-total"><?= formatRupiah($row['total_price']) ?></td>
              <td class="tx-date">
                <strong><?= $tgl ?></strong>
                <?= $jam ?> WIB
              </td>
              <td class="td-action">
                <a class="btn-view" href="<?= url('/kasir/transaction/receipt') ?>?id=<?= $row['id'] ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  Lihat Struk
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>