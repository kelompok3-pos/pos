<?php
$products      ??= [];
$cart          ??= [];
$settings      ??= [];
$taxPercentage = (float) ($settings['tax_percentage'] ?? 0);
$subtotal      = array_reduce($cart, fn(float $s, array $i): float => $s + (float) $i['subtotal'], 0.0);
$taxAmount     = round($subtotal * $taxPercentage / 100, 2);
$total         = $subtotal + $taxAmount;
$cartCount     = array_sum(array_column($cart, 'quantity'));

$cartQty = [];
foreach ($cart as $item) {
    $cartQty[$item['product_id']] = $item['quantity'];
}
?>

<style>
.pos-wrap {
  --brand:       #2563eb;
  --brand-dark:  #1e40af;
  --brand-soft:  #dbeafe;
  --mint-dark:   #047857;
  --mint-soft:   #dcfce7;
  --amber:       #f59e0b;
  --danger:      #ef4444;
  --ink:         #172033;
  --muted:       #64748b;
  --border:      #dbe6f3;
  --surface:     #ffffff;
  --bg:          #f8fafc;
  --radius:      12px;
  --radius-sm:   8px;
  --shadow:      0 1px 3px rgba(15,23,42,.07), 0 1px 2px rgba(15,23,42,.05);
  --shadow-md:   0 4px 16px rgba(15,23,42,.10);
  --font:        Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
}

.pos-wrap {
  display: grid;
  grid-template-columns: 1fr 360px;
  height: calc(100vh - 78px);
  overflow: hidden;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: var(--bg);
  box-shadow: var(--shadow-md);
  font-family: var(--font);
  color: var(--ink);
}

.pos-left {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 18px 18px 18px 20px;
  gap: 14px;
  background: var(--bg);
}

.pos-search-wrap { position: relative; }

.pos-search-wrap input {
  width: 100%;
  padding: 10px 16px 10px 42px;
  border: 1.5px solid var(--border);
  border-radius: 40px;
  font-size: .88rem;
  background: var(--surface);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  font-family: var(--font);
  color: var(--ink);
  box-sizing: border-box;
}

.pos-search-wrap input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}

.pos-search-wrap svg {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  width: 16px;
  height: 16px;
  pointer-events: none;
}

.pos-products {
  flex: 1;
  overflow-y: auto;
  padding: 4px 8px 4px 4px;
}

.pos-products::-webkit-scrollbar { width: 4px; }
.pos-products::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
  gap: 12px;
}

.product-card {
  background: var(--surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  border: 2px solid transparent;
  transition: border-color .18s, box-shadow .18s;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(37,99,235,.15), var(--shadow-md);
}

.product-card.out-of-stock { opacity: .5; pointer-events: none; }

.product-card__img {
  width: 100%;
  aspect-ratio: 4/3;
  object-fit: cover;
  background: var(--bg);
  border-radius: calc(var(--radius) - 2px) calc(var(--radius) - 2px) 0 0;
  display: block;
}

.product-card__img-placeholder {
  width: 100%;
  aspect-ratio: 4/3;
  background: linear-gradient(135deg, var(--brand-soft) 0%, #bfdbfe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  border-radius: calc(var(--radius) - 2px) calc(var(--radius) - 2px) 0 0;
}

.product-card__body {
  padding: 10px 12px 8px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.product-card__name {
  font-size: .84rem;
  font-weight: 700;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  color: var(--ink);
}

.product-card__price { font-size: .94rem; font-weight: 800; color: var(--brand-dark); }
.product-card__stock { font-size: .72rem; color: var(--muted); }
.product-card__stock.low { color: var(--amber); font-weight: 600; }

.product-card__footer { padding: 0 10px 10px; }

.btn-add {
  width: 100%;
  padding: 7px;
  border: none;
  border-radius: var(--radius-sm);
  background: var(--brand-soft);
  color: var(--brand-dark);
  font-size: .8rem;
  font-weight: 700;
  cursor: pointer;
  transition: background .15s, color .15s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  font-family: var(--font);
}

.btn-add:hover { background: var(--brand); color: #fff; }

.qty-inline {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--brand-soft);
  border-radius: var(--radius-sm);
  overflow: hidden;
}

.qty-inline button {
  border: none;
  background: transparent;
  color: var(--brand-dark);
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  padding: 5px 10px;
  line-height: 1;
  transition: background .12s;
}

.qty-inline button:hover { background: var(--brand); color: #fff; }

.qty-inline span {
  font-size: .85rem;
  font-weight: 700;
  color: var(--brand-dark);
}

.empty-products {
  text-align: center;
  padding: 60px 20px;
  color: var(--muted);
  grid-column: 1 / -1;
}

.empty-products svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: .35; }

.pos-right {
  background: var(--surface);
  border-left: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
}

.cart-header {
  padding: 18px 18px 14px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.cart-header h3 {
  font-size: .98rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: var(--ink);
}

.cart-badge {
  background: var(--brand);
  color: #fff;
  border-radius: 20px;
  font-size: .72rem;
  padding: 2px 9px;
  font-weight: 700;
}

.cashier-info {
  font-size: .76rem;
  color: var(--muted);
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cart-items::-webkit-scrollbar { width: 4px; }
.cart-items::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.cart-empty {
  text-align: center;
  color: var(--muted);
  padding: 40px 0;
  font-size: .86rem;
  line-height: 1.6;
}

.cart-empty svg {
  width: 40px;
  height: 40px;
  margin-bottom: 10px;
  opacity: .3;
  display: block;
  margin-inline: auto;
}

.cart-item {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: start;
  gap: 8px;
  padding: 10px;
  background: var(--bg);
  border-radius: var(--radius-sm);
  border: 1px solid var(--border);
}

.cart-item__name { font-size: .82rem; font-weight: 700; line-height: 1.3; color: var(--ink); }
.cart-item__price-each { font-size: .73rem; color: var(--muted); margin-top: 2px; }
.cart-item__subtotal { font-size: .86rem; font-weight: 800; color: var(--brand-dark); text-align: right; }

.cart-item__controls {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 7px;
}

.cart-item__controls button {
  width: 24px;
  height: 24px;
  border: 1.5px solid var(--border);
  background: var(--surface);
  border-radius: 6px;
  font-size: .9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  transition: background .12s, border-color .12s;
}

.cart-item__controls button:hover {
  background: var(--brand);
  border-color: var(--brand);
  color: #fff;
}

.cart-item__controls .qty-val {
  font-size: .8rem;
  font-weight: 700;
  min-width: 22px;
  text-align: center;
  border: 1.5px solid var(--border);
  border-radius: 6px;
  padding: 2px 0;
  background: var(--surface);
  color: var(--ink);
  font-family: var(--font);
}

.btn-remove {
  border: none;
  background: transparent;
  color: #cbd5e1;
  cursor: pointer;
  padding: 2px;
  line-height: 1;
}

.btn-remove:hover { color: var(--danger); }

.cart-summary {
  padding: 12px 16px 0;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: .83rem;
  color: var(--muted);
  margin-bottom: 5px;
}

.summary-row.total {
  font-size: 1.02rem;
  font-weight: 900;
  color: var(--ink);
  margin-top: 8px;
  border-top: 2px dashed var(--border);
  padding-top: 10px;
}

.summary-row.total span:last-child { color: var(--brand-dark); }

.cart-footer { padding: 14px 14px 16px; flex-shrink: 0; }

.btn-checkout {
  width: 100%;
  padding: 13px;
  border: none;
  border-radius: var(--radius);
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: #fff;
  font-size: .95rem;
  font-weight: 800;
  cursor: pointer;
  transition: opacity .15s, transform .15s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: var(--font);
  box-shadow: 0 6px 20px rgba(37,99,235,.28);
}

.btn-checkout:hover { opacity: .92; transform: translateY(-1px); }

.btn-checkout:disabled {
  background: #e2e8f0;
  color: #94a3b8;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

/* ── Modal — backdrop solid, tidak transparan ── */
.pos-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.65);
  z-index: 9999;
  display: none;
  align-items: center;
  justify-content: center;
}

.pos-modal-backdrop.open {
  display: flex;
}

.pos-modal {
  background: var(--surface);
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
  margin: 16px;
  overflow: hidden;
  box-shadow: 0 24px 70px rgba(15,23,42,.30);
  animation: modalIn .18s ease;
}

@keyframes modalIn {
  from { opacity: 0; transform: scale(.96) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}

.pos-modal-head {
  padding: 20px 20px 16px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.pos-modal-head h4 { font-size: 1.02rem; font-weight: 800; color: var(--ink); }

.pos-modal-close {
  border: none;
  background: none;
  color: var(--muted);
  cursor: pointer;
  font-size: 1.4rem;
  line-height: 1;
  padding: 4px;
}

.pos-modal-body { padding: 20px; }

.pos-modal-total {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  font-size: 1.45rem;
  font-weight: 900;
  margin-bottom: 20px;
  color: var(--ink);
}

.pos-modal-total span:last-child { color: var(--brand-dark); }

.pos-input-label {
  font-size: .76rem;
  font-weight: 800;
  color: var(--muted);
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: .05em;
}

.pos-input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: .98rem;
  outline: none;
  margin-bottom: 14px;
  font-family: var(--font);
  color: var(--ink);
  transition: border-color .2s, box-shadow .2s;
  box-sizing: border-box;
}

.pos-input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}

.change-box {
  background: var(--mint-soft);
  border-radius: var(--radius-sm);
  padding: 11px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: .88rem;
  color: var(--mint-dark);
  font-weight: 700;
}

.change-box strong { font-size: 1.08rem; color: var(--mint-dark); }

.pos-modal-foot {
  padding: 0 20px 20px;
  display: flex;
  gap: 10px;
}

.pos-modal-foot button {
  flex: 1;
  padding: 12px;
  border-radius: var(--radius-sm);
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  border: none;
  font-family: var(--font);
}

.btn-modal-cancel {
  background: var(--bg);
  color: var(--muted);
  border: 1.5px solid var(--border) !important;
}

.btn-modal-confirm {
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: #fff;
  box-shadow: 0 4px 14px rgba(37,99,235,.25);
}

.btn-modal-confirm:hover { opacity: .9; }

/* Modal berada di luar .pos-wrap, jadi token warna harus tersedia di sini. */
.pos-modal-backdrop {
  --brand: #2563eb;
  --brand-dark: #1e40af;
  --mint-dark: #047857;
  --mint-soft: #dcfce7;
  --danger: #dc2626;
  --ink: #172033;
  --muted: #64748b;
  --border: #dbe6f3;
  --surface: #ffffff;
  --bg: #f8fafc;
  --radius-sm: 8px;
  --font: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
  padding: 20px;
  background: rgba(15, 23, 42, .8);
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
  font-family: var(--font);
}

.pos-modal {
  max-width: 440px;
  max-height: calc(100vh - 40px);
  margin: 0;
  background: #ffffff;
  color: var(--ink);
  border: 1px solid rgba(255,255,255,.8);
  box-shadow: 0 30px 90px rgba(2,6,23,.5);
}

.pos-modal-head {
  padding: 20px 22px;
  background: linear-gradient(135deg, #eff6ff, #ffffff);
  border-bottom-color: #dbe6f3;
}

.pos-modal-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pos-modal-icon {
  width: 42px;
  height: 42px;
  flex: 0 0 42px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: #dbeafe;
  color: var(--brand-dark);
}

.pos-modal-head h4 { margin: 0 0 2px; }

.pos-modal-head p {
  margin: 0;
  color: var(--muted);
  font-size: .78rem;
}

.pos-modal-close {
  width: 34px;
  height: 34px;
  border: 1px solid #dbe6f3;
  border-radius: 10px;
  background: #ffffff;
  display: grid;
  place-items: center;
  transition: background .15s, color .15s, border-color .15s;
}

.pos-modal-close:hover {
  background: #fef2f2;
  color: var(--danger);
  border-color: #fecaca;
}

.pos-modal-body {
  padding: 22px;
  overflow-y: auto;
}

.pos-modal-total {
  align-items: center;
  padding: 16px;
  border: 1px solid #bfdbfe;
  border-radius: 14px;
  background: #eff6ff;
}

.pos-modal-total span:first-child {
  font-size: .8rem;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .06em;
}

.pos-input {
  padding: 13px 15px;
  background: #ffffff;
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 10px;
}

.quick-cash-list {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin-bottom: 16px;
}

.quick-cash {
  padding: 8px 6px;
  border: 1px solid #dbe6f3;
  border-radius: 9px;
  background: #f8fafc;
  color: var(--brand-dark);
  font: 700 .76rem var(--font);
  cursor: pointer;
  transition: background .15s, border-color .15s, transform .15s;
}

.quick-cash:hover {
  background: #dbeafe;
  border-color: #93c5fd;
  transform: translateY(-1px);
}

.change-box { border: 1px solid #bbf7d0; }

.change-box.is-short {
  background: #fef2f2;
  border-color: #fecaca;
  color: var(--danger);
}

.change-box.is-short strong { color: var(--danger); }

.pos-modal-foot {
  padding: 16px 22px 22px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}

.btn-modal-confirm:disabled {
  background: #cbd5e1;
  color: #64748b;
  box-shadow: none;
  cursor: not-allowed;
  opacity: 1;
}

body.pos-modal-open { overflow: hidden; }

@media (max-width: 520px) {
  .pos-modal-backdrop { padding: 12px; align-items: flex-end; }
  .pos-modal { max-height: calc(100vh - 24px); border-radius: 18px 18px 12px 12px; }
  .pos-modal-head, .pos-modal-body { padding: 18px; }
  .pos-modal-foot { padding: 14px 18px 18px; }
  .pos-modal-total { font-size: 1.3rem; }
}
</style>

<!-- ═══════════════ POS LAYOUT ═══════════════ -->
<div class="pos-wrap">

  <!-- ─── LEFT: Product Grid ─── -->
  <div class="pos-left">

    <div class="pos-search-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
      </svg>
      <input type="text" id="posSearch" placeholder="Cari produk...">
    </div>

    <div class="pos-products">
      <div class="product-grid" id="productGrid">

        <?php if (!$products): ?>
          <div class="empty-products">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
            </svg>
            <p>Belum ada produk tersedia.</p>
          </div>
        <?php else: ?>
          <?php foreach ($products as $prod): ?>
            <?php
            $inCart  = $cartQty[$prod['id']] ?? 0;
            $isOut   = (int) $prod['stock'] <= 0;
            $isLow   = !$isOut && (int) $prod['stock'] <= 5;
            $imgPath = !empty($prod['image']) ? '/storage/products/' . e($prod['image']) : null;
            ?>

            <div class="product-card <?= $isOut ? 'out-of-stock' : '' ?>"
                 data-name="<?= strtolower(e($prod['name'])) ?>">

              <?php if ($imgPath): ?>
                <img src="<?= $imgPath ?>" alt="<?= e($prod['name']) ?>" class="product-card__img" loading="lazy">
              <?php else: ?>
                <div class="product-card__img-placeholder">🛍️</div>
              <?php endif; ?>

              <div class="product-card__body">
                <div class="product-card__name"><?= e($prod['name']) ?></div>
                <div class="product-card__price"><?= formatRupiah($prod['price']) ?></div>
                <div class="product-card__stock <?= $isLow ? 'low' : '' ?>">
                  <?= $isOut ? 'Stok habis' : 'Stok: ' . $prod['stock'] . ($isLow ? ' ⚠️' : '') ?>
                </div>
              </div>

              <div class="product-card__footer">
                <?php if ($isOut): ?>
                  <div class="btn-add" style="background:#f1f5f9;color:#94a3b8;cursor:not-allowed;">Habis</div>
                <?php elseif ($inCart > 0): ?>
                  <div class="qty-inline">
                    <form action="<?= url('/kasir/transaction/update') ?>" method="POST" style="display:contents">
                      <?= csrf_field() ?>
                      <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                      <input type="hidden" name="quantity" class="qty-card-<?= $prod['id'] ?>" value="<?= $inCart ?>">
                      <button type="button" onclick="changeCardQty(<?= $prod['id'] ?>, -1, this)" aria-label="Kurang">−</button>
                      <span id="qty-label-<?= $prod['id'] ?>"><?= $inCart ?></span>
                      <button type="button" onclick="changeCardQty(<?= $prod['id'] ?>, 1, this)" aria-label="Tambah">+</button>
                    </form>
                  </div>
                <?php else: ?>
                  <form action="<?= url('/kasir/transaction/add') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                    <button type="submit" class="btn-add">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 5v14M5 12h14"/>
                      </svg>
                      Tambah
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- ─── RIGHT: Cart ─── -->
  <div class="pos-right">

    <div class="cart-header">
      <h3>
        Keranjang
        <span class="cart-badge"><?= $cartCount ?> item</span>
      </h3>
      <div class="cashier-info">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="8" r="4"/>
          <path d="M6 20v-2a4 4 0 018 0v2"/>
        </svg>
        <?php echo e($_SESSION['name'] ?? 'Kasir'); ?>
      </div>
    </div>

    <div class="cart-items">
      <?php if (!$cart): ?>
        <div class="cart-empty">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Keranjang kosong.<br>Pilih produk untuk memulai.
        </div>
      <?php else: ?>
        <?php foreach ($cart as $item): ?>
          <div class="cart-item">
            <div>
              <div class="cart-item__name"><?= e($item['name']) ?></div>
              <div class="cart-item__price-each"><?= formatRupiah($item['price']) ?> / pcs</div>
              <div class="cart-item__controls">
                <form action="<?= url('/kasir/transaction/update') ?>" method="POST" style="display:contents">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                  <button type="button" onclick="cartStep(this, -1)" aria-label="Kurang">−</button>
                  <input type="number" name="quantity" class="qty-val"
                         value="<?= $item['quantity'] ?>" min="1">
                  <button type="button" onclick="cartStep(this, 1)" aria-label="Tambah">+</button>
                  <button type="submit" style="margin-left:4px;padding:3px 8px;border:1.5px solid var(--border);border-radius:6px;background:var(--surface);font-size:.73rem;cursor:pointer;font-family:var(--font);">OK</button>
                </form>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
              <form action="<?= url('/kasir/transaction/remove') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <button class="btn-remove" aria-label="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </form>
              <div class="cart-item__subtotal"><?= formatRupiah($item['subtotal']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="cart-summary">
      <div class="summary-row">
        <span>Subtotal</span>
        <span><?= formatRupiah($subtotal) ?></span>
      </div>
      <div class="summary-row">
        <span>Pajak (<?= $taxPercentage ?>%)</span>
        <span><?= formatRupiah($taxAmount) ?></span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span><?= formatRupiah($total) ?></span>
      </div>
    </div>

    <div class="cart-footer">
      <button class="btn-checkout" id="btnCheckout"
              <?= !$cart ? 'disabled' : '' ?>
              onclick="openPosModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
          <path d="M5 13l4 4L19 7"/>
        </svg>
        Bayar Sekarang
      </button>
    </div>

  </div>
</div>

<!-- ═══════════════ PAYMENT MODAL ═══════════════ -->
<?php if ($cart): ?>
<div class="pos-modal-backdrop" id="posPaymentModal" aria-hidden="true">
  <div class="pos-modal" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle" aria-describedby="paymentModalDescription">
    <div class="pos-modal-head">
      <div class="pos-modal-title-wrap">
        <span class="pos-modal-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M3 10h18M7 15h2"/>
          </svg>
        </span>
        <div>
          <h4 id="paymentModalTitle">Konfirmasi Pembayaran</h4>
          <p id="paymentModalDescription">Masukkan uang yang diterima dari pelanggan.</p>
        </div>
      </div>
      <button type="button" class="pos-modal-close" onclick="closePosModal()" aria-label="Tutup dialog pembayaran">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form action="<?= url('/kasir/transaction/checkout') ?>" method="POST" id="paymentForm">
      <?= csrf_field() ?>
      <input type="hidden" name="payment_method" value="cash">

      <div class="pos-modal-body">
        <div class="pos-modal-total">
          <span>Total</span>
          <span><?= formatRupiah($total) ?></span>
        </div>

        <div class="pos-input-label">Jumlah Bayar</div>
        <input type="number" name="paid_amount" id="paidAmount"
               class="pos-input"
               min="<?= $total ?>"
               step="1"
               inputmode="numeric"
               placeholder="Masukkan jumlah uang"
               required>

        <div class="quick-cash-list" aria-label="Pilihan nominal pembayaran cepat">
          <?php
          $quickCashAmounts = array_unique([
              (int) ceil($total / 10000) * 10000,
              (int) ceil($total / 50000) * 50000,
              (int) ceil($total / 100000) * 100000,
          ]);
          foreach ($quickCashAmounts as $amount):
          ?>
            <button type="button" class="quick-cash" data-amount="<?= $amount ?>"><?= formatRupiah($amount) ?></button>
          <?php endforeach; ?>
        </div>

        <div class="change-box" id="changeBox" aria-live="polite">
          <span id="changeLabel">Kembalian</span>
          <strong id="changePreview">Rp 0</strong>
        </div>
      </div>

      <div class="pos-modal-foot">
        <button type="button" class="btn-modal-cancel" onclick="closePosModal()">Batal</button>
        <button type="submit" class="btn-modal-confirm" id="confirmPayment" disabled>Konfirmasi & Cetak</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
(function () {
  /* ── Search ── */
  document.getElementById('posSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
  });

  /* ── Cart qty stepper ── */
  window.cartStep = function (btn, delta) {
    const input = btn.parentElement.querySelector('input[name="quantity"]');
    if (!input) return;
    const next = parseInt(input.value || '1', 10) + delta;
    if (next >= 1) input.value = next;
  };

  /* ── Product card qty stepper ── */
  window.changeCardQty = function (productId, delta, btn) {
    const hidden = document.querySelector('.qty-card-' + productId);
    if (!hidden) return;
    const label = document.getElementById('qty-label-' + productId);
    let value = parseInt(hidden.value || '1', 10) + delta;
    if (value < 1) value = 1;
    hidden.value = value;
    if (label) label.textContent = value;
    hidden.closest('form').submit();
  };

  /* ── Modal ── */
  const TOTAL = <?= json_encode($total) ?>;
  const fmt = new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
  });

  window.openPosModal = function () {
    const modal = document.getElementById('posPaymentModal');
    if (modal) {
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('pos-modal-open');
    }
    const paid = document.getElementById('paidAmount');
    if (paid) { paid.value = ''; paid.focus(); }
    updateChange();
  };

  window.closePosModal = function () {
    const modal = document.getElementById('posPaymentModal');
    if (modal) {
      modal.classList.remove('open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('pos-modal-open');
    }
    document.getElementById('btnCheckout')?.focus();
  };

  function updateChange() {
    const paid    = document.getElementById('paidAmount');
    const preview = document.getElementById('changePreview');
    const box     = document.getElementById('changeBox');
    const label   = document.getElementById('changeLabel');
    const confirm = document.getElementById('confirmPayment');
    if (!paid || !preview || !box || !label || !confirm) return;

    const amount = Number(paid.value || 0);
    const enough = amount >= TOTAL;
    box.classList.toggle('is-short', amount > 0 && !enough);
    label.textContent = enough ? 'Kembalian' : 'Kekurangan';
    preview.textContent = fmt.format(enough ? amount - TOTAL : Math.max(TOTAL - amount, 0));
    confirm.disabled = !enough;
  }

  document.getElementById('paidAmount')?.addEventListener('input', updateChange);

  document.querySelectorAll('.quick-cash').forEach(button => {
    button.addEventListener('click', function () {
      const paid = document.getElementById('paidAmount');
      if (!paid) return;
      paid.value = this.dataset.amount || '';
      paid.focus();
      updateChange();
    });
  });

  document.getElementById('posPaymentModal')?.addEventListener('click', function (e) {
    if (e.target === this) closePosModal();
  });

  document.addEventListener('keydown', e => {
    const modal = document.getElementById('posPaymentModal');
    if (e.key === 'Escape' && modal?.classList.contains('open')) closePosModal();
  });
})();
</script>
