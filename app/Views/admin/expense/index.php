<?php
$summary = $summary ?? [];
$expenses = $expenses ?? [];
$categories = $categories ?? [];
$from = $from ?? date('Y-m-01');
$to = $to ?? date('Y-m-d');
$selectedCategory = $selectedCategory ?? '';

$grandTotal = array_sum(array_column($summary, 'total'));

$topCategories = array_slice($summary, 0, 3);
?>

<style>
.sa-expense {
    width: 100%;
}

.sa-expense * {
    box-sizing: border-box;
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

.sa-export-link {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-export-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.sa-summary-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    padding: 1rem;
    min-height: 112px;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-summary-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-summary-icon {
    width: 38px;
    height: 38px;
    border-radius: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: .75rem;
    font-size: 1.15rem;
}

.sa-icon-blue {
    color: #378add;
    background: rgba(55, 138, 221, .11);
}

.sa-icon-green {
    color: #1d9e75;
    background: rgba(29, 158, 117, .11);
}

.sa-icon-orange {
    color: #ba7517;
    background: rgba(186, 117, 23, .12);
}

.sa-icon-red {
    color: #e24b4a;
    background: rgba(226, 75, 74, .12);
}

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .3rem;
}

.sa-summary-value {
    font-size: 1.22rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    line-height: 1.25;
    word-break: break-word;
}

.sa-summary-sub {
    display: block;
    margin-top: .3rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-main-grid {
    display: grid;
    grid-template-columns: minmax(320px, .9fr) minmax(0, 1.1fr);
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: start;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
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
    gap: .85rem;
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

.sa-textarea-icon {
    top: .8rem;
    transform: none;
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

textarea.sa-form-input {
    height: auto;
    min-height: 96px;
    resize: vertical;
    padding-top: .75rem;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-submit-button {
    width: 100%;
    height: 42px;
    border: 0;
    border-radius: .75rem;
    background: #378add;
    color: #fff;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-submit-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    min-width: 940px;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: .825rem;
}

.sa-table th {
    padding: .75rem 1rem;
    text-align: left;
    font-size: .72rem;
    font-weight: 800;
    color: var(--color-text-secondary, #6b7280);
    background: var(--color-background-secondary, #f9fafb);
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
    white-space: nowrap;
}

.sa-table td {
    padding: .9rem 1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .06));
    color: var(--color-text-primary, #111827);
    vertical-align: top;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-date-cell {
    color: var(--color-text-secondary, #6b7280);
    font-weight: 700;
    white-space: nowrap;
}

.sa-desc-cell {
    line-height: 1.45;
    word-break: break-word;
}

.sa-money-cell {
    color: #b42318;
    font-weight: 850;
    white-space: nowrap;
}

.sa-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .32rem .65rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}

.sa-badge-muted {
    background: #f3f4f6;
    color: #6b7280;
}

.sa-actions {
    display: inline-flex;
    align-items: flex-start;
    gap: .4rem;
    flex-wrap: wrap;
}

.sa-edit-details {
    position: relative;
}

.sa-edit-summary {
    list-style: none;
    height: 34px;
    padding: 0 .7rem;
    border-radius: .65rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    font-size: .78rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-edit-summary::-webkit-details-marker {
    display: none;
}

.sa-edit-summary:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
}

.sa-edit-popover {
    position: absolute;
    top: calc(100% + .5rem);
    right: 0;
    z-index: 20;
    width: min(320px, 86vw);
    padding: .85rem;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .12));
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 18px 50px rgba(15, 23, 42, .12);
    display: grid;
    gap: .6rem;
}

.sa-edit-input {
    width: 100%;
    height: 38px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    border-radius: .7rem;
    padding: .45rem .65rem;
    font-size: .8rem;
    outline: none;
}

.sa-edit-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-edit-save {
    height: 36px;
    border: 0;
    border-radius: .65rem;
    background: #378add;
    color: #fff;
    font-size: .78rem;
    font-weight: 750;
    cursor: pointer;
}

.sa-delete-button {
    height: 34px;
    padding: 0 .7rem;
    border: 1px solid rgba(180, 35, 24, .18);
    border-radius: .65rem;
    background: #fff;
    color: #b42318;
    font-size: .78rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-delete-button:hover {
    background: #fef3f2;
    border-color: rgba(180, 35, 24, .35);
}

.sa-empty {
    padding: 2.4rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty i {
    display: block;
    margin-bottom: .5rem;
    font-size: 2rem;
    color: #9ca3af;
}

@media (max-width: 1100px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-main-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-export-link {
        width: 100%;
    }

    .sa-summary-grid,
    .sa-form-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="sa-expense">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Store Finance</span>
            <h2>
                <i class="ti ti-receipt-tax" aria-hidden="true"></i>
                Pengeluaran Toko
            </h2>
            <p>Catat, koreksi, dan pantau biaya operasional toko.</p>
        </div>

        <a
            class="sa-export-link"
            href="<?= url('/admin/expense/export?' . http_build_query(['from' => $from, 'to' => $to])) ?>"
        >
            <i class="ti ti-download" aria-hidden="true"></i>
            Export CSV
        </a>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-red">
                <i class="ti ti-cash-banknote" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Periode</span>
            <div class="sa-summary-value"><?= formatRupiah($grandTotal) ?></div>
            <span class="sa-summary-sub"><?= e($from) ?> hingga <?= e($to) ?></span>
        </div>

        <?php foreach ($topCategories as $index => $item): ?>
            <?php
            $iconClass = ['sa-icon-blue', 'sa-icon-orange', 'sa-icon-green'][$index] ?? 'sa-icon-blue';
            ?>
            <div class="sa-summary-card">
                <div class="sa-summary-icon <?= $iconClass ?>">
                    <i class="ti ti-category" aria-hidden="true"></i>
                </div>
                <span class="sa-summary-label"><?= e(ucfirst($item['category'])) ?></span>
                <div class="sa-summary-value"><?= formatRupiah($item['total']) ?></div>
                <span class="sa-summary-sub">pengeluaran kategori</span>
            </div>
        <?php endforeach; ?>

        <?php for ($i = count($topCategories); $i < 3; $i++): ?>
            <div class="sa-summary-card">
                <div class="sa-summary-icon sa-icon-blue">
                    <i class="ti ti-category" aria-hidden="true"></i>
                </div>
                <span class="sa-summary-label">Kategori</span>
                <div class="sa-summary-value">-</div>
                <span class="sa-summary-sub">belum ada data</span>
            </div>
        <?php endfor; ?>
    </div>

    <div class="sa-main-grid">

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-plus" aria-hidden="true"></i>
                        Tambah Pengeluaran
                    </div>
                    <div class="sa-panel-subtitle">Nominal dicatat ke toko aktif.</div>
                </div>
            </div>

            <form action="<?= url('/admin/expense/store') ?>" method="POST">
                <div class="sa-form-body">
                    <?= csrf_field() ?>

                    <div class="sa-form-grid">
                        <div class="sa-form-group">
                            <label class="sa-form-label">Kategori</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-category sa-input-icon" aria-hidden="true"></i>
                                <select class="sa-form-input" name="category" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= e($category) ?>">
                                            <?= e(ucfirst($category)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="sa-form-group">
                            <label class="sa-form-label">Tanggal</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                                <input
                                    class="sa-form-input"
                                    type="date"
                                    name="expense_date"
                                    value="<?= date('Y-m-d') ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="sa-form-group sa-form-group-full">
                            <label class="sa-form-label">Nominal</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-cash sa-input-icon" aria-hidden="true"></i>
                                <input
                                    class="sa-form-input"
                                    type="number"
                                    name="amount"
                                    min="1"
                                    placeholder="Contoh: 250000"
                                    required
                                >
                            </div>
                        </div>

                        <div class="sa-form-group sa-form-group-full">
                            <label class="sa-form-label">Deskripsi</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-notes sa-input-icon sa-textarea-icon" aria-hidden="true"></i>
                                <textarea
                                    class="sa-form-input"
                                    name="description"
                                    rows="3"
                                    placeholder="Contoh: pembelian alat kebersihan"
                                    required
                                ></textarea>
                            </div>
                        </div>

                        <div class="sa-form-group sa-form-group-full">
                            <button class="sa-submit-button" type="submit">
                                <i class="ti ti-plus" aria-hidden="true"></i>
                                Simpan Pengeluaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-filter" aria-hidden="true"></i>
                        Filter Laporan
                    </div>
                    <div class="sa-panel-subtitle">Batasi data yang ditampilkan.</div>
                </div>
            </div>

            <form method="GET" action="<?= url('/admin/expense') ?>">
                <div class="sa-form-body">
                    <div class="sa-form-grid">
                        <div class="sa-form-group">
                            <label class="sa-form-label">Dari</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                                <input
                                    class="sa-form-input"
                                    type="date"
                                    name="from"
                                    value="<?= e($from) ?>"
                                >
                            </div>
                        </div>

                        <div class="sa-form-group">
                            <label class="sa-form-label">Sampai</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                                <input
                                    class="sa-form-input"
                                    type="date"
                                    name="to"
                                    value="<?= e($to) ?>"
                                >
                            </div>
                        </div>

                        <div class="sa-form-group sa-form-group-full">
                            <label class="sa-form-label">Kategori</label>
                            <div class="sa-input-wrap">
                                <i class="ti ti-category sa-input-icon" aria-hidden="true"></i>
                                <select class="sa-form-input" name="category">
                                    <option value="">Semua kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option
                                            value="<?= e($category) ?>"
                                            <?= $selectedCategory === $category ? 'selected' : '' ?>
                                        >
                                            <?= e(ucfirst($category)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="sa-form-group sa-form-group-full">
                            <button class="sa-submit-button" type="submit">
                                <i class="ti ti-filter" aria-hidden="true"></i>
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-list-details" aria-hidden="true"></i>
                    Daftar Pengeluaran
                </div>
                <div class="sa-panel-subtitle">Riwayat biaya operasional pada periode terpilih.</div>
            </div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table">
                <colgroup>
                    <col style="width: 140px">
                    <col style="width: 150px">
                    <col style="width: 330px">
                    <col style="width: 160px">
                    <col style="width: 160px">
                </colgroup>

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td class="sa-date-cell">
                                <?= e($expense['expense_date']) ?>
                            </td>

                            <td>
                                <span class="sa-badge sa-badge-muted">
                                    <?= e(ucfirst($expense['category'])) ?>
                                </span>
                            </td>

                            <td class="sa-desc-cell">
                                <?= e($expense['description']) ?>
                            </td>

                            <td class="sa-money-cell">
                                <?= formatRupiah($expense['amount']) ?>
                            </td>

                            <td>
                                <div class="sa-actions">
                                    <details class="sa-edit-details">
                                        <summary class="sa-edit-summary">
                                            <i class="ti ti-edit" aria-hidden="true"></i>
                                            Edit
                                        </summary>

                                        <form
                                            action="<?= url('/admin/expense/update') ?>"
                                            method="POST"
                                            class="sa-edit-popover"
                                        >
                                            <?= csrf_field() ?>

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $expense['id'] ?>"
                                            >

                                            <select class="sa-edit-input" name="category">
                                                <?php foreach ($categories as $category): ?>
                                                    <option
                                                        value="<?= e($category) ?>"
                                                        <?= $expense['category'] === $category ? 'selected' : '' ?>
                                                    >
                                                        <?= e(ucfirst($category)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <input
                                                class="sa-edit-input"
                                                type="number"
                                                name="amount"
                                                value="<?= e((string) $expense['amount']) ?>"
                                                min="1"
                                            >

                                            <input
                                                class="sa-edit-input"
                                                name="description"
                                                value="<?= e($expense['description']) ?>"
                                            >

                                            <input
                                                class="sa-edit-input"
                                                type="date"
                                                name="expense_date"
                                                value="<?= e($expense['expense_date']) ?>"
                                            >

                                            <button class="sa-edit-save" type="submit">
                                                Simpan
                                            </button>
                                        </form>
                                    </details>

                                    <form
                                        action="<?= url('/admin/expense/delete') ?>"
                                        method="POST"
                                        onsubmit="return confirm('Hapus pengeluaran ini?')"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $expense['id'] ?>"
                                        >

                                        <button class="sa-delete-button" type="submit">
                                            <i class="ti ti-trash" aria-hidden="true"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($expenses === []): ?>
                        <tr>
                            <td colspan="5" class="sa-empty">
                                <i class="ti ti-inbox" aria-hidden="true"></i>
                                Belum ada pengeluaran pada periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>