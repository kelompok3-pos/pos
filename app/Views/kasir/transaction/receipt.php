<?php
$settings    ??= [];
$transaction ??= [];
$details     ??= [];
?>

<style>
/* ── Receipt screen styles ─────────────────────────── */
.receipt-wrap {
  --brand:      #2563eb;
  --brand-dark: #1e40af;
  --brand-soft: #dbeafe;
  --mint:       #10b981;
  --mint-dark:  #047857;
  --ink:        #172033;
  --muted:      #64748b;
  --border:     #dbe6f3;
  --surface:    #ffffff;
  --bg:         #f8fafc;
  --radius:     12px;
  --radius-sm:  8px;
  --shadow-md:  0 4px 16px rgba(15,23,42,.10);
  --font:       Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;

  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: var(--font);
  color: var(--ink);
}

.receipt-paper {
  width: 100%;
  max-width: 400px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  overflow: hidden;
}

/* ── Paper header (store info) ─────────────────────── */
.receipt-store {
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
  color: #fff;
  text-align: center;
  padding: 28px 24px 22px;
}
.receipt-store img {
  width: 56px; height: 56px;
  border-radius: 14px;
  object-fit: cover;
  margin-bottom: 10px;
  border: 2px solid rgba(255,255,255,.3);
}
.receipt-store__logo-fallback {
  width: 52px; height: 52px;
  border-radius: 14px;
  background: rgba(255,255,255,.2);
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 1.5rem; font-weight: 900;
  margin-bottom: 10px;
}
.receipt-store__name {
  font-size: 1.1rem; font-weight: 900;
  letter-spacing: -.01em; margin-bottom: 4px;
}
.receipt-store__address {
  font-size: .78rem; opacity: .82; line-height: 1.4;
}

/* ── Body ──────────────────────────────────────────── */
.receipt-body { padding: 20px 22px; }

/* ── Meta rows ─────────────────────────────────────── */
.receipt-meta { margin-bottom: 16px; }
.receipt-meta__row {
  display: flex; justify-content: space-between; align-items: baseline;
  padding: 5px 0;
  font-size: .82rem;
  border-bottom: 1px dashed var(--border);
}
.receipt-meta__row:last-child { border-bottom: none; }
.receipt-meta__row span:first-child { color: var(--muted); font-weight: 600; }
.receipt-meta__row strong { font-weight: 800; color: var(--ink); }

.invoice-chip {
  display: inline-flex; align-items: center; gap: 5px;
  background: var(--brand-soft); color: var(--brand-dark);
  border-radius: 5px; padding: 2px 8px;
  font-size: .78rem; font-weight: 800;
}

/* ── Divider ───────────────────────────────────────── */
.receipt-divider {
  border: none;
  border-top: 2px dashed var(--border);
  margin: 14px 0;
}

/* ── Items ─────────────────────────────────────────── */
.receipt-items { display: flex; flex-direction: column; gap: 10px; margin-bottom: 4px; }
.receipt-item {
  display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;
}
.receipt-item__name { font-size: .86rem; font-weight: 700; color: var(--ink); }
.receipt-item__qty  { font-size: .76rem; color: var(--muted); margin-top: 2px; }
.receipt-item__subtotal {
  font-size: .88rem; font-weight: 800;
  color: var(--ink); white-space: nowrap;
  flex-shrink: 0;
}

/* ── Summary ───────────────────────────────────────── */
.receipt-summary { margin-top: 4px; }
.receipt-summary__row {
  display: flex; justify-content: space-between;
  font-size: .82rem; color: var(--muted);
  padding: 4px 0;
}
.receipt-summary__total {
  display: flex; justify-content: space-between; align-items: baseline;
  font-size: 1.05rem; font-weight: 900; color: var(--ink);
  padding: 10px 0 8px;
  border-top: 2px solid var(--ink);
  border-bottom: 2px solid var(--ink);
  margin: 8px 0;
}
.receipt-summary__total span:last-child { color: var(--mint-dark); }
.receipt-summary__paid { display: flex; justify-content: space-between; font-size: .84rem; padding: 3px 0; }
.receipt-summary__change {
  display: flex; justify-content: space-between;
  font-size: .88rem; font-weight: 800;
  color: var(--mint-dark); padding: 3px 0;
}

/* ── Footer ────────────────────────────────────────── */
.receipt-footer {
  text-align: center;
  font-size: .8rem; color: var(--muted);
  padding: 14px 0 4px;
  line-height: 1.5;
}

/* ── Action buttons ────────────────────────────────── */
.receipt-actions {
  display: flex; gap: 10px;
  width: 100%; max-width: 400px;
  margin-top: 14px;
}
.btn-back {
  flex: 1; padding: 11px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  background: var(--surface); color: var(--muted);
  font-size: .9rem; font-weight: 700;
  font-family: var(--font); cursor: pointer;
  text-decoration: none; text-align: center;
  transition: border-color .15s, color .15s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-back:hover { border-color: var(--brand); color: var(--brand); }
.btn-print {
  flex: 1; padding: 11px;
  border: none; border-radius: var(--radius-sm);
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: #fff; font-size: .9rem; font-weight: 700;
  font-family: var(--font); cursor: pointer;
  box-shadow: 0 4px 12px rgba(37,99,235,.25);
  transition: opacity .15s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-print:hover { opacity: .88; }

/* ══════════════════════════════════════════════════════
   PRINT STYLES
   Sembunyikan semua UI kecuali struk itu sendiri
   ══════════════════════════════════════════════════════ */
@media print {
  /* Sembunyikan seluruh shell layout */
  .app-sidebar,
  .app-topbar,
  .app-footer,
  .app-flash-wrap,
  .receipt-actions,
  .no-print {
    display: none !important;
  }

  /* Reset body & shell */
  body, .app-body { background: #fff !important; }
  .app-shell      { display: block !important; }
  .app-workspace  { display: block !important; }
  .app-main       { padding: 0 !important; margin: 0 !important; }
  .app-content    { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }

  /* Struk jadi full width, tanpa border/shadow */
  .receipt-wrap   { align-items: flex-start !important; }
  .receipt-paper  {
    max-width: 100% !important;
    width: 100% !important;
    box-shadow: none !important;
    border: none !important;
    border-radius: 0 !important;
  }

  /* Header gradient tidak tercetak bagus di semua printer — fallback ke putih */
  .receipt-store {
    background: #fff !important;
    color: #000 !important;
    border-bottom: 2px solid #000;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .receipt-store__logo-fallback {
    background: #eee !important;
    color: #000 !important;
  }

  /* Paksa warna teks hitam untuk keterbacaan printer */
  .invoice-chip   { background: #eee !important; color: #000 !important; }
  .receipt-summary__total span:last-child { color: #000 !important; }

  @page {
    margin: 10mm;
    size: 80mm auto; /* thermal printer width standar */
  }
}
</style>

<div class="receipt-wrap">

  <!-- ── Receipt Paper ── -->
  <div class="receipt-paper">

    <!-- Store Header -->
    <div class="receipt-store">
      <?php if (!empty($settings['store_logo'])): ?>
        <img src="<?= asset($settings['store_logo']) ?>" alt="Logo toko">
      <?php else: ?>
        <div class="receipt-store__logo-fallback">
          <?= strtoupper(substr($settings['store_name'] ?? APP_NAME, 0, 1)) ?>
        </div>
      <?php endif; ?>
      <div class="receipt-store__name"><?= e($settings['store_name'] ?? APP_NAME) ?></div>
      <?php if (!empty($settings['store_address'])): ?>
        <div class="receipt-store__address"><?= e($settings['store_address']) ?></div>
      <?php endif; ?>
    </div>

    <div class="receipt-body">

      <!-- Transaction Meta -->
      <div class="receipt-meta">
        <div class="receipt-meta__row">
          <span>Invoice</span>
          <span class="invoice-chip"><?= e($transaction['transaction_code']) ?></span>
        </div>
        <div class="receipt-meta__row">
          <span>Tanggal</span>
          <strong><?= date('d M Y, H:i', strtotime($transaction['created_at'])) ?></strong>
        </div>
        <div class="receipt-meta__row">
          <span>Kasir</span>
          <strong><?= e($transaction['cashier_name']) ?></strong>
        </div>
        <div class="receipt-meta__row">
          <span>Metode Bayar</span>
          <strong><?= strtoupper(e($transaction['payment_method'] ?? 'cash')) ?></strong>
        </div>
      </div>

      <hr class="receipt-divider">

      <!-- Items -->
      <div class="receipt-items">
        <?php foreach ($details as $item):
          $price = $item['quantity'] ? $item['subtotal'] / $item['quantity'] : 0;
        ?>
          <div class="receipt-item">
            <div>
              <div class="receipt-item__name"><?= e($item['product_name']) ?></div>
              <div class="receipt-item__qty"><?= $item['quantity'] ?> × <?= formatRupiah($price) ?></div>
            </div>
            <div class="receipt-item__subtotal"><?= formatRupiah($item['subtotal']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <hr class="receipt-divider">

      <!-- Summary -->
      <div class="receipt-summary">
        <div class="receipt-summary__row">
          <span>Subtotal</span>
          <span><?= formatRupiah($transaction['subtotal'] ?? $transaction['total_price']) ?></span>
        </div>
        <div class="receipt-summary__row">
          <span>Pajak</span>
          <span><?= formatRupiah($transaction['tax_amount'] ?? 0) ?></span>
        </div>
        <div class="receipt-summary__total">
          <span>TOTAL</span>
          <span><?= formatRupiah($transaction['total_price']) ?></span>
        </div>
        <div class="receipt-summary__paid">
          <span>Dibayar</span>
          <span><?= formatRupiah($transaction['paid_amount']) ?></span>
        </div>
        <div class="receipt-summary__change">
          <span>Kembalian</span>
          <span><?= formatRupiah($transaction['change_amount']) ?></span>
        </div>
      </div>

      <hr class="receipt-divider">

      <!-- Footer note -->
      <div class="receipt-footer">
        <?= e($settings['receipt_footer'] ?? 'Terima kasih sudah berbelanja.') ?>
      </div>

    </div><!-- /receipt-body -->
  </div><!-- /receipt-paper -->

  <!-- Action Buttons (hidden on print) -->
  <div class="receipt-actions no-print">
    <a href="<?= url('/kasir/transaction') ?>" class="btn-back">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Kembali
    </a>
    <button class="btn-print" onclick="window.print()">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Cetak Struk
    </button>
  </div>

</div>