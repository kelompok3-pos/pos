<?php
$transactions ??= [];
$date ??= '';

$txCount = count($transactions);
$txTotal = array_sum(array_column($transactions, 'total_price'));
?>

<style>
.sa-mytx-page {
    width: 100%;
}

.sa-mytx-page * {
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

.sa-filter-panel,
.sa-panel {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sa-filter-body {
    padding: 1rem 1.1rem;
}

.sa-filter-grid {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) auto auto;
    gap: .75rem;
    align-items: end;
}

.sa-form-group {
    min-width: 0;
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

.sa-form-input {
    width: 100%;
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem .55rem 2.35rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-filter-button,
.sa-reset-link {
    height: 42px;
    padding: 0 .9rem;
    border-radius: .75rem;
    font-size: .84rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    white-space: nowrap;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
}

.sa-filter-button {
    border: 0;
    background: #378add;
    color: #fff;
}

.sa-filter-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-reset-link {
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
}

.sa-reset-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
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

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .3rem;
}

.sa-summary-value {
    font-size: 1.25rem;
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

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    min-width: 820px;
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

.sa-table th:last-child {
    text-align: center;
}

.sa-table td {
    padding: .9rem 1rem;
    border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .06));
    color: var(--color-text-primary, #111827);
    vertical-align: middle;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-number {
    color: var(--color-text-secondary, #6b7280);
    font-weight: 750;
}

.sa-invoice-chip {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    max-width: 170px;
    padding: .38rem .6rem;
    border-radius: .65rem;
    background: rgba(55, 138, 221, .12);
    color: #185fa5;
    font-size: .76rem;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sa-total {
    color: #1d9e75;
    font-weight: 850;
    white-space: nowrap;
}

.sa-date-cell {
    color: var(--color-text-secondary, #6b7280);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.sa-date-cell strong {
    display: block;
    margin-bottom: .12rem;
    color: var(--color-text-primary, #111827);
    font-size: .84rem;
    font-weight: 800;
}

.sa-action-cell {
    text-align: center;
}

.sa-view-link {
    height: 34px;
    padding: 0 .75rem;
    border-radius: .65rem;
    border: 1px solid rgba(55, 138, 221, .25);
    background: rgba(55, 138, 221, .08);
    color: #2f7bc6;
    font-size: .78rem;
    font-weight: 750;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    text-decoration: none;
    white-space: nowrap;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-view-link:hover {
    background: rgba(55, 138, 221, .14);
    border-color: rgba(55, 138, 221, .4);
    color: #2f7bc6;
    text-decoration: none;
}

.sa-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

.sa-empty-icon {
    width: 54px;
    height: 54px;
    margin: 0 auto .8rem;
    border-radius: 1rem;
    background: rgba(55, 138, 221, .10);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.55rem;
}

.sa-empty strong {
    display: block;
    color: var(--color-text-primary, #111827);
    font-size: .95rem;
    font-weight: 800;
}

.sa-empty p {
    margin: .35rem auto 0;
    max-width: 380px;
    color: var(--color-text-secondary, #6b7280);
    font-size: .82rem;
    line-height: 1.5;
}

@media (max-width: 900px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-filter-grid {
        grid-template-columns: 1fr;
    }

    .sa-filter-button,
    .sa-reset-link {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-summary-grid {
        grid-template-columns: 1fr;
    }

    .sa-panel-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="sa-mytx-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Cashier Workspace</span>
            <h2>
                <i class="ti ti-receipt" aria-hidden="true"></i>
                Transaksi Saya
            </h2>
            <p>Riwayat transaksi yang kamu buat sebagai kasir.</p>
        </div>
    </div>

    <div class="sa-filter-panel">
        <form method="GET" action="<?= url('/kasir/my-transactions') ?>">
            <div class="sa-filter-body">
                <div class="sa-filter-grid">
                    <div class="sa-form-group">
                        <label class="sa-form-label" for="date">Tanggal Transaksi</label>
                        <div class="sa-input-wrap">
                            <i class="ti ti-calendar sa-input-icon" aria-hidden="true"></i>
                            <input
                                type="date"
                                id="date"
                                name="date"
                                class="sa-form-input"
                                value="<?= e($date) ?>"
                            >
                        </div>
                    </div>

                    <button type="submit" class="sa-filter-button">
                        <i class="ti ti-filter" aria-hidden="true"></i>
                        Filter
                    </button>

                    <?php if ($date): ?>
                        <a href="<?= url('/kasir/my-transactions') ?>" class="sa-reset-link">
                            <i class="ti ti-x" aria-hidden="true"></i>
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-blue">
                <i class="ti ti-receipt" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Transaksi</span>
            <div class="sa-summary-value"><?= number_format($txCount) ?></div>
            <span class="sa-summary-sub">transaksi tercatat</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-green">
                <i class="ti ti-cash" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Pendapatan</span>
            <div class="sa-summary-value"><?= formatRupiah($txTotal) ?></div>
            <span class="sa-summary-sub">dari transaksi terpilih</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-orange">
                <i class="ti ti-calendar-search" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Filter Aktif</span>
            <div class="sa-summary-value">
                <?= $date ? e(date('d M Y', strtotime($date))) : 'Semua' ?>
            </div>
            <span class="sa-summary-sub">
                <?= $date ? 'tanggal tertentu' : 'tanpa filter tanggal' ?>
            </span>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div>
                <div class="sa-panel-title">
                    <i class="ti ti-list-details" aria-hidden="true"></i>
                    Daftar Transaksi
                </div>
                <div class="sa-panel-subtitle">
                    Lihat detail struk dari setiap transaksi yang sudah dibuat.
                </div>
            </div>
        </div>

        <?php if (!$transactions): ?>
            <div class="sa-empty">
                <div class="sa-empty-icon">
                    <i class="ti ti-inbox" aria-hidden="true"></i>
                </div>

                <strong>Belum ada transaksi</strong>

                <p>
                    <?= $date
                        ? 'Tidak ada transaksi pada tanggal ' . e(date('d M Y', strtotime($date))) . '.'
                        : 'Transaksi yang kamu buat akan muncul di halaman ini.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="sa-table-wrap">
                <table class="sa-table">
                    <colgroup>
                        <col style="width: 70px">
                        <col style="width: 220px">
                        <col style="width: 170px">
                        <col style="width: 190px">
                        <col style="width: 150px">
                    </colgroup>

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
                            $createdAt = $row['created_at'] ?? date('Y-m-d H:i:s');
                            $dt = new DateTime($createdAt);
                            $tgl = $dt->format('d M Y');
                            $jam = $dt->format('H:i');
                            ?>

                            <tr>
                                <td class="sa-number">
                                    <?= $i + 1 ?>
                                </td>

                                <td>
                                    <span class="sa-invoice-chip">
                                        <i class="ti ti-file-invoice" aria-hidden="true"></i>
                                        <?= e($row['transaction_code'] ?? '-') ?>
                                    </span>
                                </td>

                                <td class="sa-total">
                                    <?= formatRupiah($row['total_price'] ?? 0) ?>
                                </td>

                                <td class="sa-date-cell">
                                    <strong><?= e($tgl) ?></strong>
                                    <?= e($jam) ?> WIB
                                </td>

                                <td class="sa-action-cell">
                                    <a
                                        class="sa-view-link"
                                        href="<?= url('/kasir/transaction/receipt') ?>?id=<?= e((string) ($row['id'] ?? '')) ?>"
                                    >
                                        <i class="ti ti-eye" aria-hidden="true"></i>
                                        Lihat Struk
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>