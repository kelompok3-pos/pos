<?php
$products      ??= [];
$cart          ??= [];
$settings      ??= [];

$taxPercentage = (float) ($settings['tax_percentage'] ?? 0);
$subtotal      = array_reduce($cart, fn(float $s, array $i): float => $s + (float) ($i['subtotal'] ?? 0), 0.0);
$taxAmount     = round($subtotal * $taxPercentage / 100, 2);
$total         = $subtotal + $taxAmount;
$cartCount     = array_sum(array_column($cart, 'quantity'));

$cartQty = [];
foreach ($cart as $item) {
    $cartQty[$item['product_id']] = $item['quantity'];
}
?>

<style>
.sa-pos-page {
    width: 100%;
}

.sa-pos-page * {
    box-sizing: border-box;
}

.sa-pos-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 380px;
    height: calc(100vh - 96px);
    min-height: 680px;
    background: var(--color-background-secondary, #f8fafc);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
}

.sa-pos-left {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 1rem;
    gap: 1rem;
    overflow: hidden;
}

.sa-pos-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
}

.sa-pos-title .sa-eyebrow {
    display: inline-flex;
    margin-bottom: .35rem;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .08em;
    color: #378add;
    text-transform: uppercase;
}

.sa-pos-title h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.sa-pos-title p {
    margin: .3rem 0 0;
    font-size: .82rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-search-wrap {
    position: relative;
    max-width: 420px;
    width: 100%;
}

.sa-search-icon {
    position: absolute;
    left: .85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    pointer-events: none;
}

.sa-search-input {
    width: 100%;
    height: 44px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .85rem;
    padding: .55rem .75rem .55rem 2.45rem;
    font-size: .86rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-search-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-products-area {
    flex: 1;
    overflow-y: auto;
    padding-right: .25rem;
}

.sa-products-area::-webkit-scrollbar,
.sa-cart-list::-webkit-scrollbar {
    width: 5px;
}

.sa-products-area::-webkit-scrollbar-thumb,
.sa-cart-list::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 999px;
}

.sa-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(165px, 1fr));
    gap: .85rem;
}

.sa-product-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    min-width: 0;
    display: flex;
    flex-direction: column;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-product-card:hover {
    border-color: rgba(55, 138, 221, .35);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-product-card.is-out {
    opacity: .58;
}

.sa-product-image {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    background: #f1f5f9;
    display: block;
}

.sa-product-placeholder {
    width: 100%;
    aspect-ratio: 4 / 3;
    background: linear-gradient(135deg, rgba(55, 138, 221, .14), rgba(29, 158, 117, .10));
    color: #378add;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.sa-product-body {
    padding: .85rem .85rem .65rem;
    flex: 1;
}

.sa-product-name {
    min-height: 2.4em;
    color: var(--color-text-primary, #111827);
    font-size: .86rem;
    font-weight: 800;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sa-product-price {
    margin-top: .4rem;
    color: #185fa5;
    font-size: .9rem;
    font-weight: 850;
}

.sa-product-stock {
    margin-top: .2rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .72rem;
    font-weight: 700;
}

.sa-product-stock.is-low {
    color: #ba7517;
}

.sa-product-footer {
    padding: 0 .85rem .85rem;
}

.sa-add-button,
.sa-empty-button {
    width: 100%;
    height: 36px;
    border: 0;
    border-radius: .7rem;
    font-size: .8rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
}

.sa-add-button {
    background: rgba(55, 138, 221, .10);
    color: #2f7bc6;
    cursor: pointer;
    transition: background-color .18s ease, color .18s ease;
}

.sa-add-button:hover {
    background: #378add;
    color: #fff;
}

.sa-empty-button {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

.sa-card-qty {
    display: grid;
    grid-template-columns: 36px 1fr 36px;
    align-items: center;
    height: 36px;
    border-radius: .7rem;
    background: rgba(55, 138, 221, .10);
    overflow: hidden;
}

.sa-card-qty button {
    height: 36px;
    border: 0;
    background: transparent;
    color: #2f7bc6;
    font-size: 1rem;
    font-weight: 900;
    cursor: pointer;
    transition: background-color .18s ease, color .18s ease;
}

.sa-card-qty button:hover {
    background: #378add;
    color: #fff;
}

.sa-card-qty span {
    text-align: center;
    color: #185fa5;
    font-size: .85rem;
    font-weight: 850;
}

.sa-pos-right {
    background: var(--color-background-primary, #fff);
    border-left: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}

.sa-cart-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-cart-title {
    margin: 0;
    color: var(--color-text-primary, #111827);
    font-size: 1rem;
    font-weight: 850;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
}

.sa-cart-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .25rem .6rem;
    border-radius: 999px;
    background: #378add;
    color: #fff;
    font-size: .72rem;
    font-weight: 800;
}

.sa-cashier-info {
    margin-top: .35rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: .35rem;
}

.sa-cart-list {
    flex: 1;
    overflow-y: auto;
    padding: .9rem;
    display: grid;
    align-content: start;
    gap: .7rem;
}

.sa-cart-empty,
.sa-products-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--color-text-secondary, #6b7280);
}

.sa-empty-icon {
    width: 54px;
    height: 54px;
    margin: 0 auto .75rem;
    border-radius: 1rem;
    background: rgba(55, 138, 221, .10);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.55rem;
}

.sa-cart-empty strong,
.sa-products-empty strong {
    display: block;
    color: var(--color-text-primary, #111827);
    font-weight: 850;
}

.sa-cart-empty p,
.sa-products-empty p {
    margin: .35rem auto 0;
    max-width: 320px;
    font-size: .82rem;
    line-height: 1.5;
}

.sa-cart-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: .75rem;
    padding: .8rem;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: .9rem;
    background: var(--color-background-secondary, #f9fafb);
}

.sa-cart-name {
    color: var(--color-text-primary, #111827);
    font-size: .82rem;
    font-weight: 800;
    line-height: 1.35;
}

.sa-cart-price {
    margin-top: .18rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .72rem;
    font-weight: 700;
}

.sa-cart-controls {
    display: flex;
    align-items: center;
    gap: .35rem;
    margin-top: .55rem;
}

.sa-cart-step,
.sa-cart-ok {
    height: 28px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: #fff;
    border-radius: .55rem;
    color: var(--color-text-primary, #111827);
    font-size: .78rem;
    font-weight: 800;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}

.sa-cart-step {
    width: 28px;
}

.sa-cart-ok {
    padding: 0 .55rem;
}

.sa-cart-step:hover,
.sa-cart-ok:hover {
    background: #378add;
    border-color: #378add;
    color: #fff;
}

.sa-cart-qty {
    width: 42px;
    height: 28px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    border-radius: .55rem;
    background: #fff;
    color: var(--color-text-primary, #111827);
    text-align: center;
    font-size: .78rem;
    font-weight: 800;
}

.sa-cart-side {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: .45rem;
}

.sa-remove-button {
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: .55rem;
    background: transparent;
    color: #9ca3af;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color .18s ease, color .18s ease;
}

.sa-remove-button:hover {
    background: #fef3f2;
    color: #b42318;
}

.sa-cart-subtotal {
    color: #185fa5;
    font-size: .82rem;
    font-weight: 850;
    white-space: nowrap;
}

.sa-cart-summary {
    padding: .9rem 1rem 0;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-summary-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .45rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .82rem;
    font-weight: 700;
}

.sa-summary-row.is-total {
    margin-top: .75rem;
    padding-top: .75rem;
    border-top: 1px dashed var(--color-border-tertiary, rgba(0, 0, 0, .16));
    color: var(--color-text-primary, #111827);
    font-size: 1rem;
    font-weight: 900;
}

.sa-summary-row.is-total span:last-child {
    color: #185fa5;
}

.sa-cart-footer {
    padding: 1rem;
}

.sa-checkout-button {
    width: 100%;
    height: 46px;
    border: 0;
    border-radius: .85rem;
    background: #378add;
    color: #fff;
    font-size: .92rem;
    font-weight: 850;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    cursor: pointer;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-checkout-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .22);
}

.sa-checkout-button:disabled {
    background: #e5e7eb;
    color: #9ca3af;
    box-shadow: none;
    cursor: not-allowed;
}

.sa-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, .72);
    backdrop-filter: blur(5px);
}

.sa-modal-backdrop.is-open {
    display: flex;
}

.sa-modal {
    width: min(440px, 100%);
    max-height: calc(100vh - 2rem);
    overflow: hidden;
    background: #fff;
    color: var(--color-text-primary, #111827);
    border-radius: 1.1rem;
    box-shadow: 0 30px 90px rgba(2, 6, 23, .45);
}

.sa-modal-head {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #eff6ff, #fff);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.sa-modal-title-wrap {
    display: flex;
    align-items: center;
    gap: .8rem;
}

.sa-modal-icon {
    width: 42px;
    height: 42px;
    border-radius: .85rem;
    background: rgba(55, 138, 221, .14);
    color: #2f7bc6;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1.25rem;
}

.sa-modal-head h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 850;
}

.sa-modal-head p {
    margin: .15rem 0 0;
    color: #6b7280;
    font-size: .78rem;
}

.sa-modal-close {
    width: 34px;
    height: 34px;
    border: 1px solid #e5e7eb;
    border-radius: .75rem;
    background: #fff;
    color: #6b7280;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, color .18s ease;
}

.sa-modal-close:hover {
    background: #fef3f2;
    border-color: #fecaca;
    color: #b42318;
}

.sa-modal-body {
    padding: 1.1rem;
    overflow-y: auto;
}

.sa-modal-total {
    padding: 1rem;
    border: 1px solid rgba(55, 138, 221, .24);
    border-radius: .95rem;
    background: rgba(55, 138, 221, .08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-modal-total span:first-child {
    color: #6b7280;
    font-size: .76rem;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.sa-modal-total span:last-child {
    color: #185fa5;
    font-size: 1.25rem;
    font-weight: 900;
}

.sa-input-label {
    margin-bottom: .4rem;
    color: #6b7280;
    font-size: .75rem;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .05em;
}

.sa-modal-input {
    width: 100%;
    height: 46px;
    border: 1px solid #d1d5db;
    border-radius: .8rem;
    padding: .55rem .8rem;
    color: #111827;
    font-size: 1rem;
    font-weight: 800;
    outline: none;
    margin-bottom: .8rem;
}

.sa-modal-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-quick-cash-list {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .5rem;
    margin-bottom: 1rem;
}

.sa-quick-cash {
    height: 36px;
    border: 1px solid rgba(55, 138, 221, .22);
    border-radius: .7rem;
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    font-size: .76rem;
    font-weight: 800;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-quick-cash:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .40);
}

.sa-change-box {
    padding: .85rem 1rem;
    border: 1px solid rgba(29, 158, 117, .22);
    border-radius: .85rem;
    background: rgba(29, 158, 117, .10);
    color: #247244;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    font-size: .85rem;
    font-weight: 800;
}

.sa-change-box strong {
    font-size: 1rem;
}

.sa-change-box.is-short {
    border-color: #fecaca;
    background: #fef3f2;
    color: #b42318;
}

.sa-modal-foot {
    padding: 1rem 1.1rem 1.1rem;
    border-top: 1px solid #e5e7eb;
    background: #f8fafc;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}

.sa-modal-cancel,
.sa-modal-confirm {
    height: 42px;
    border-radius: .75rem;
    font-size: .84rem;
    font-weight: 800;
    cursor: pointer;
}

.sa-modal-cancel {
    border: 1px solid #d1d5db;
    background: #fff;
    color: #6b7280;
}

.sa-modal-confirm {
    border: 0;
    background: #378add;
    color: #fff;
}

.sa-modal-confirm:hover {
    background: #2f7bc6;
}

.sa-modal-confirm:disabled {
    background: #d1d5db;
    color: #6b7280;
    cursor: not-allowed;
}

body.sa-modal-open {
    overflow: hidden;
}

@media (max-width: 1100px) {
    .sa-pos-shell {
        grid-template-columns: 1fr;
        height: auto;
        min-height: 0;
        overflow: visible;
    }

    .sa-pos-right {
        border-left: 0;
        border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
        max-height: 680px;
    }
}

@media (max-width: 640px) {
    .sa-pos-left {
        padding: .85rem;
    }

    .sa-pos-header {
        flex-direction: column;
    }

    .sa-search-wrap {
        max-width: none;
    }

    .sa-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
    }

    .sa-modal-backdrop {
        align-items: flex-end;
        padding: .75rem;
    }

    .sa-modal {
        border-radius: 1rem 1rem .75rem .75rem;
    }

    .sa-modal-foot {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sa-pos-page">

    <div class="sa-pos-shell">

        <div class="sa-pos-left">

            <div class="sa-pos-header">
                <div class="sa-pos-title">
                    <span class="sa-eyebrow">Cashier POS</span>
                    <h2>
                        <i class="ti ti-cash-register" aria-hidden="true"></i>
                        Transaksi Kasir
                    </h2>
                    <p>Pilih produk, atur quantity, lalu proses pembayaran pelanggan.</p>
                </div>

                <div class="sa-search-wrap">
                    <i class="ti ti-search sa-search-icon" aria-hidden="true"></i>
                    <input
                        type="text"
                        id="posSearch"
                        class="sa-search-input"
                        placeholder="Cari produk..."
                    >
                </div>
            </div>

            <div class="sa-products-area">
                <div class="sa-product-grid" id="productGrid">

                    <?php if (!$products): ?>
                        <div class="sa-products-empty">
                            <div class="sa-empty-icon">
                                <i class="ti ti-package-off" aria-hidden="true"></i>
                            </div>
                            <strong>Belum ada produk tersedia</strong>
                            <p>Produk aktif akan muncul di area ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $prod): ?>
                            <?php
                            $productId = (int) ($prod['id'] ?? 0);
                            $stock = (int) ($prod['stock'] ?? 0);
                            $inCart = $cartQty[$productId] ?? 0;
                            $isOut = $stock <= 0;
                            $isLow = !$isOut && $stock <= 5;
                            $imgPath = !empty($prod['image']) ? '/storage/products/' . e($prod['image']) : null;
                            ?>

                            <div
                                class="sa-product-card <?= $isOut ? 'is-out' : '' ?>"
                                data-name="<?= strtolower(e($prod['name'] ?? '')) ?>"
                            >
                                <?php if ($imgPath): ?>
                                    <img
                                        src="<?= $imgPath ?>"
                                        alt="<?= e($prod['name'] ?? 'Produk') ?>"
                                        class="sa-product-image"
                                        loading="lazy"
                                    >
                                <?php else: ?>
                                    <div class="sa-product-placeholder">
                                        <i class="ti ti-shopping-bag" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="sa-product-body">
                                    <div class="sa-product-name">
                                        <?= e($prod['name'] ?? '-') ?>
                                    </div>

                                    <div class="sa-product-price">
                                        <?= formatRupiah($prod['price'] ?? 0) ?>
                                    </div>

                                    <div class="sa-product-stock <?= $isLow ? 'is-low' : '' ?>">
                                        <?= $isOut ? 'Stok habis' : 'Stok: ' . $stock . ($isLow ? ' ⚠️' : '') ?>
                                    </div>
                                </div>

                                <div class="sa-product-footer">
                                    <?php if ($isOut): ?>
                                        <div class="sa-empty-button">
                                            Habis
                                        </div>
                                    <?php elseif ($inCart > 0): ?>
                                        <div class="sa-card-qty">
                                            <form action="<?= url('/kasir/transaction/update') ?>" method="POST" style="display:contents">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= $productId ?>">
                                                <input type="hidden" name="quantity" class="qty-card-<?= $productId ?>" value="<?= (int) $inCart ?>">

                                                <button type="button" onclick="changeCardQty(<?= $productId ?>, -1, this)" aria-label="Kurang">−</button>
                                                <span id="qty-label-<?= $productId ?>"><?= (int) $inCart ?></span>
                                                <button type="button" onclick="changeCardQty(<?= $productId ?>, 1, this)" aria-label="Tambah">+</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <form action="<?= url('/kasir/transaction/add') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= $productId ?>">

                                            <button type="submit" class="sa-add-button">
                                                <i class="ti ti-plus" aria-hidden="true"></i>
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

        <div class="sa-pos-right">

            <div class="sa-cart-header">
                <h3 class="sa-cart-title">
                    <span>
                        <i class="ti ti-shopping-cart" aria-hidden="true"></i>
                        Keranjang
                    </span>

                    <span class="sa-cart-badge">
                        <?= number_format((int) $cartCount) ?> item
                    </span>
                </h3>

                <div class="sa-cashier-info">
                    <i class="ti ti-user-circle" aria-hidden="true"></i>
                    <?= e($_SESSION['name'] ?? 'Kasir') ?>
                </div>
            </div>

            <div class="sa-cart-list">
                <?php if (!$cart): ?>
                    <div class="sa-cart-empty">
                        <div class="sa-empty-icon">
                            <i class="ti ti-shopping-cart-off" aria-hidden="true"></i>
                        </div>
                        <strong>Keranjang kosong</strong>
                        <p>Pilih produk dari daftar untuk memulai transaksi.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="sa-cart-item">
                            <div>
                                <div class="sa-cart-name">
                                    <?= e($item['name'] ?? '-') ?>
                                </div>

                                <div class="sa-cart-price">
                                    <?= formatRupiah($item['price'] ?? 0) ?> / pcs
                                </div>

                                <div class="sa-cart-controls">
                                    <form action="<?= url('/kasir/transaction/update') ?>" method="POST" style="display:contents">
                                        <?= csrf_field() ?>

                                        <input type="hidden" name="product_id" value="<?= e((string) ($item['product_id'] ?? '')) ?>">

                                        <button type="button" class="sa-cart-step" onclick="cartStep(this, -1)" aria-label="Kurang">−</button>

                                        <input
                                            type="number"
                                            name="quantity"
                                            class="sa-cart-qty"
                                            value="<?= (int) ($item['quantity'] ?? 1) ?>"
                                            min="1"
                                        >

                                        <button type="button" class="sa-cart-step" onclick="cartStep(this, 1)" aria-label="Tambah">+</button>

                                        <button type="submit" class="sa-cart-ok">
                                            OK
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="sa-cart-side">
                                <form action="<?= url('/kasir/transaction/remove') ?>" method="POST">
                                    <?= csrf_field() ?>

                                    <input type="hidden" name="product_id" value="<?= e((string) ($item['product_id'] ?? '')) ?>">

                                    <button class="sa-remove-button" aria-label="Hapus">
                                        <i class="ti ti-x" aria-hidden="true"></i>
                                    </button>
                                </form>

                                <div class="sa-cart-subtotal">
                                    <?= formatRupiah($item['subtotal'] ?? 0) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="sa-cart-summary">
                <div class="sa-summary-row">
                    <span>Subtotal</span>
                    <span><?= formatRupiah($subtotal) ?></span>
                </div>

                <div class="sa-summary-row">
                    <span>Pajak (<?= e((string) $taxPercentage) ?>%)</span>
                    <span><?= formatRupiah($taxAmount) ?></span>
                </div>

                <div class="sa-summary-row is-total">
                    <span>Total</span>
                    <span><?= formatRupiah($total) ?></span>
                </div>
            </div>

            <div class="sa-cart-footer">
                <button
                    class="sa-checkout-button"
                    id="btnCheckout"
                    <?= !$cart ? 'disabled' : '' ?>
                    onclick="openPosModal()"
                >
                    <i class="ti ti-check" aria-hidden="true"></i>
                    Bayar Sekarang
                </button>
            </div>

        </div>
    </div>

</div>

<?php if ($cart): ?>
<div class="sa-modal-backdrop" id="posPaymentModal" aria-hidden="true">
    <div class="sa-modal" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle" aria-describedby="paymentModalDescription">

        <div class="sa-modal-head">
            <div class="sa-modal-title-wrap">
                <span class="sa-modal-icon" aria-hidden="true">
                    <i class="ti ti-credit-card" aria-hidden="true"></i>
                </span>

                <div>
                    <h4 id="paymentModalTitle">Konfirmasi Pembayaran</h4>
                    <p id="paymentModalDescription">Masukkan uang yang diterima dari pelanggan.</p>
                </div>
            </div>

            <button type="button" class="sa-modal-close" onclick="closePosModal()" aria-label="Tutup dialog pembayaran">
                <i class="ti ti-x" aria-hidden="true"></i>
            </button>
        </div>

        <form action="<?= url('/kasir/transaction/checkout') ?>" method="POST" id="paymentForm">
            <?= csrf_field() ?>

            <input type="hidden" name="payment_method" value="cash">

            <div class="sa-modal-body">
                <div class="sa-modal-total">
                    <span>Total</span>
                    <span><?= formatRupiah($total) ?></span>
                </div>

                <div class="sa-input-label">Jumlah Bayar</div>

                <input
                    type="number"
                    name="paid_amount"
                    id="paidAmount"
                    class="sa-modal-input"
                    min="<?= e((string) $total) ?>"
                    step="1"
                    inputmode="numeric"
                    placeholder="Masukkan jumlah uang"
                    required
                >

                <div class="sa-quick-cash-list" aria-label="Pilihan nominal pembayaran cepat">
                    <?php
                    $quickCashAmounts = array_unique([
                        (int) ceil($total / 10000) * 10000,
                        (int) ceil($total / 50000) * 50000,
                        (int) ceil($total / 100000) * 100000,
                    ]);
                    foreach ($quickCashAmounts as $amount):
                    ?>
                        <button type="button" class="sa-quick-cash" data-amount="<?= (int) $amount ?>">
                            <?= formatRupiah($amount) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="sa-change-box" id="changeBox" aria-live="polite">
                    <span id="changeLabel">Kembalian</span>
                    <strong id="changePreview">Rp 0</strong>
                </div>
            </div>

            <div class="sa-modal-foot">
                <button type="button" class="sa-modal-cancel" onclick="closePosModal()">
                    Batal
                </button>

                <button type="submit" class="sa-modal-confirm" id="confirmPayment" disabled>
                    Konfirmasi & Cetak
                </button>
            </div>
        </form>

    </div>
</div>
<?php endif; ?>

<script>
(function () {
    document.getElementById('posSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();

        document.querySelectorAll('.sa-product-card').forEach(card => {
            card.style.display = card.dataset.name.includes(q) ? '' : 'none';
        });
    });

    window.cartStep = function (btn, delta) {
        const input = btn.parentElement.querySelector('input[name="quantity"]');
        if (!input) return;

        const next = parseInt(input.value || '1', 10) + delta;
        if (next >= 1) input.value = next;
    };

    window.changeCardQty = function (productId, delta, btn) {
        const hidden = document.querySelector('.qty-card-' + productId);
        if (!hidden) return;

        const label = document.getElementById('qty-label-' + productId);

        let value = parseInt(hidden.value || '1', 10) + delta;
        if (value < 1) value = 1;

        hidden.value = value;

        if (label) {
            label.textContent = value;
        }

        hidden.closest('form').submit();
    };

    const TOTAL = <?= json_encode($total) ?>;
    const fmt = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    });

    window.openPosModal = function () {
        const modal = document.getElementById('posPaymentModal');

        if (modal) {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('sa-modal-open');
        }

        const paid = document.getElementById('paidAmount');

        if (paid) {
            paid.value = '';
            paid.focus();
        }

        updateChange();
    };

    window.closePosModal = function () {
        const modal = document.getElementById('posPaymentModal');

        if (modal) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('sa-modal-open');
        }

        document.getElementById('btnCheckout')?.focus();
    };

    function updateChange() {
        const paid = document.getElementById('paidAmount');
        const preview = document.getElementById('changePreview');
        const box = document.getElementById('changeBox');
        const label = document.getElementById('changeLabel');
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

    document.querySelectorAll('.sa-quick-cash').forEach(button => {
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

        if (e.key === 'Escape' && modal?.classList.contains('is-open')) {
            closePosModal();
        }
    });
})();
</script>