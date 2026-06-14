<style>
.sa-product-form-page {
    width: 100%;
}

.sa-product-form-page * {
    box-sizing: border-box;
}

.sa-back-wrap {
    margin-bottom: 1rem;
}

.sa-back-link {
    height: 38px;
    padding: 0 .8rem;
    border-radius: .7rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    text-decoration: none;
    font-size: .82rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-back-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.sa-title .sa-eyebrow {
    display: inline-flex;
    margin-bottom: .4rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    color: #378add;
    text-transform: uppercase;
}

.sa-title h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.sa-title p {
    margin: .35rem 0 0;
    font-size: .875rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-layout {
    display: grid;
    grid-template-columns: minmax(0, 720px);
    justify-content: center;
}

.sa-info-box {
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    border: 1px solid rgba(55, 138, 221, .16);
    background: rgba(55, 138, 221, .055);
    display: flex;
    gap: .8rem;
}

.sa-info-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1.1rem;
}

.sa-info-title {
    font-size: .88rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
}

.sa-info-text {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
    line-height: 1.45;
}

.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sa-panel-header {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-panel-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--color-text-primary, #111827);
    display: flex;
    align-items: center;
    gap: .45rem;
}

.sa-panel-subtitle {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-body {
    padding: 1.1rem;
}

.sa-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-group-full {
    grid-column: 1 / -1;
}

.sa-form-label {
    display: block;
    margin-bottom: .45rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-required {
    color: #b42318;
}

.sa-input-wrap {
    position: relative;
}

.sa-input-icon {
    position: absolute;
    left: .8rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-tertiary, #9ca3af);
    font-size: 1rem;
    pointer-events: none;
}

.sa-form-input {
    width: 100%;
    height: 44px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .8rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-form-help {
    margin-top: .4rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
    line-height: 1.45;
}

.sa-form-actions {
    display: flex;
    justify-content: space-between;
    gap: .75rem;
    padding-top: 1.1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-cancel-button,
.sa-save-button {
    height: 42px;
    padding: 0 .95rem;
    border-radius: .75rem;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.sa-cancel-button {
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
}

.sa-cancel-button:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-save-button {
    min-width: 150px;
    border: 0;
    background: #378add;
    color: #fff;
}

.sa-save-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
    color: #fff;
}

.sa-preview-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem;
    margin-top: 1rem;
}

.sa-preview-item {
    padding: .85rem;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: .9rem;
    background: var(--color-background-secondary, #f9fafb);
}

.sa-preview-label {
    display: block;
    font-size: .7rem;
    font-weight: 750;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .25rem;
}

.sa-preview-value {
    font-size: .9rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    word-break: break-word;
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-form-grid,
    .sa-preview-grid {
        grid-template-columns: 1fr;
    }

    .sa-info-box {
        flex-direction: column;
    }

    .sa-form-actions {
        flex-direction: column-reverse;
    }

    .sa-cancel-button,
    .sa-save-button,
    .sa-back-link {
        width: 100%;
    }
}
</style>

<div class="sa-product-form-page">

    <div class="sa-back-wrap">
        <a href="<?= url('/admin/product') ?>" class="sa-back-link">
            <i class="ti ti-arrow-left" aria-hidden="true"></i>
            Kembali ke Daftar Produk
        </a>
    </div>

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Product Management</span>
            <h2>
                <i class="ti ti-plus" aria-hidden="true"></i>
                Tambah Produk
            </h2>
            <p>Tambahkan produk baru dengan harga dan stok awal yang jelas.</p>
        </div>
    </div>

    <div class="sa-layout">
        <div>
            <div class="sa-info-box">
                <div class="sa-info-icon">
                    <i class="ti ti-info-circle" aria-hidden="true"></i>
                </div>

                <div>
                    <div class="sa-info-title">Informasi produk baru</div>
                    <div class="sa-info-text">
                        Pastikan nama produk jelas, harga sudah benar, dan stok awal sesuai jumlah barang yang tersedia.
                        Minimum stock digunakan sebagai batas peringatan stok menipis.
                    </div>
                </div>
            </div>

            <div class="sa-panel">
                <div class="sa-panel-header">
                    <div class="sa-panel-title">
                        <i class="ti ti-package" aria-hidden="true"></i>
                        Informasi Produk
                    </div>
                    <div class="sa-panel-subtitle">
                        Lengkapi data dasar produk sebelum disimpan ke sistem.
                    </div>
                </div>

                <form action="<?= url('/admin/product/store') ?>" method="POST">
                    <div class="sa-form-body">
                        <?= csrf_field() ?>

                        <div class="sa-form-grid">
                            <div class="sa-form-group sa-form-group-full">
                                <label for="name" class="sa-form-label">
                                    Nama Produk <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-package sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="text"
                                        class="sa-form-input"
                                        id="name"
                                        name="name"
                                        value="<?= old('name') ?>"
                                        required
                                        placeholder="Contoh: Kopi Arabica"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="price" class="sa-form-label">
                                    Harga (Rp) <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-cash sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="number"
                                        class="sa-form-input"
                                        id="price"
                                        name="price"
                                        value="<?= old('price') ?>"
                                        required
                                        min="0"
                                        placeholder="Contoh: 25000"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="stock" class="sa-form-label">
                                    Stok <span class="sa-required">*</span>
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-box-seam sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="number"
                                        class="sa-form-input"
                                        id="stock"
                                        name="stock"
                                        value="<?= old('stock') ?>"
                                        required
                                        min="0"
                                        placeholder="Contoh: 100"
                                    >
                                </div>
                            </div>

                            <div class="sa-form-group">
                                <label for="minimum_stock" class="sa-form-label">
                                    Minimum Stock
                                </label>

                                <div class="sa-input-wrap">
                                    <i class="ti ti-alert-triangle sa-input-icon" aria-hidden="true"></i>
                                    <input
                                        type="number"
                                        class="sa-form-input"
                                        id="minimum_stock"
                                        name="minimum_stock"
                                        value="<?= old('minimum_stock', '5') ?>"
                                        min="0"
                                        placeholder="Contoh: 5"
                                    >
                                </div>

                                <div class="sa-form-help">
                                    Sistem akan memberi peringatan jika stok produk mencapai batas ini.
                                </div>
                            </div>
                        </div>

                        <div class="sa-preview-grid">
                            <div class="sa-preview-item">
                                <span class="sa-preview-label">Status awal</span>
                                <div class="sa-preview-value">Aktif</div>
                            </div>

                            <div class="sa-preview-item">
                                <span class="sa-preview-label">Tipe data</span>
                                <div class="sa-preview-value">Produk toko</div>
                            </div>

                            <div class="sa-preview-item">
                                <span class="sa-preview-label">Monitoring</span>
                                <div class="sa-preview-value">Stok otomatis</div>
                            </div>
                        </div>

                        <div class="sa-form-actions">
                            <a href="<?= url('/admin/product') ?>" class="sa-cancel-button">
                                <i class="ti ti-arrow-left" aria-hidden="true"></i>
                                Kembali
                            </a>

                            <button type="submit" class="sa-save-button">
                                <i class="ti ti-check" aria-hidden="true"></i>
                                Simpan Produk
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>