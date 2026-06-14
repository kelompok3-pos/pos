<?php
// app/Views/kasir/shift/index.php

$shifts ??= [];

$activeShifts = array_filter($shifts, fn($s) => ($s['status'] ?? '') === 'open');
$closedShifts = array_filter($shifts, fn($s) => ($s['status'] ?? '') !== 'open');

$totalKas = array_sum(array_map(
    fn($s) => (float) ($s['closing_cash'] ?? 0),
    $shifts
));

$hasOpenShift = count($activeShifts) > 0;
?>

<style>
.sa-shift-page {
    width: 100%;
}

.sa-shift-page * {
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

.sa-status-pill {
    height: 34px;
    padding: 0 .75rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    white-space: nowrap;
}

.sa-status-open {
    background: #eaf7ef;
    color: #247244;
}

.sa-status-closed {
    background: #f3f4f6;
    color: #6b7280;
}

.sa-dot {
    width: .5rem;
    height: .5rem;
    border-radius: 999px;
    background: currentColor;
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

.sa-icon-purple {
    color: #8b5cf6;
    background: rgba(139, 92, 246, .12);
}

.sa-summary-label {
    display: block;
    font-size: .76rem;
    color: var(--color-text-secondary, #6b7280);
    margin-bottom: .3rem;
}

.sa-summary-value {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
    line-height: 1.25;
    word-break: break-word;
}

.sa-summary-value.sa-green {
    color: #1d9e75;
}

.sa-summary-value.sa-blue {
    color: #378add;
}

.sa-summary-sub {
    display: block;
    margin-top: .3rem;
    font-size: .72rem;
    color: var(--color-text-tertiary, #9ca3af);
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

.sa-open-shift-body {
    padding: 1.1rem;
}

.sa-open-info {
    display: flex;
    gap: .8rem;
    margin-bottom: 1rem;
}

.sa-open-icon {
    width: 42px;
    height: 42px;
    border-radius: .85rem;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 1.15rem;
}

.sa-open-title {
    font-size: .9rem;
    font-weight: 800;
    color: var(--color-text-primary, #111827);
}

.sa-open-text {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
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

.sa-open-button {
    height: 44px;
    padding: 0 1rem;
    border: 0;
    border-radius: .8rem;
    background: #378add;
    color: #fff;
    font-size: .84rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .18s ease, box-shadow .18s ease;
}

.sa-open-button:hover {
    background: #2f7bc6;
    box-shadow: 0 8px 18px rgba(55, 138, 221, .18);
}

.sa-section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    margin: 1.25rem 0 .75rem;
}

.sa-section-title {
    margin: 0;
    color: var(--color-text-primary, #111827);
    font-size: .95rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: .45rem;
}

.sa-section-count {
    color: var(--color-text-secondary, #6b7280);
    font-size: .78rem;
    font-weight: 700;
}

.sa-shift-list {
    display: grid;
    gap: .85rem;
}

.sa-shift-card {
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
    padding: 1rem;
    transition: border-color .18s ease, box-shadow .18s ease;
}

.sa-shift-card:hover {
    border-color: rgba(55, 138, 221, .25);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}

.sa-shift-card.is-open {
    border-color: rgba(29, 158, 117, .35);
    background: linear-gradient(135deg, rgba(29, 158, 117, .045), #fff);
}

.sa-shift-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
    margin-bottom: .85rem;
}

.sa-shift-id {
    color: var(--color-text-primary, #111827);
    font-size: .9rem;
    font-weight: 850;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
}

.sa-shift-time {
    color: var(--color-text-secondary, #6b7280);
    font-size: .76rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: .35rem;
    text-align: right;
    white-space: nowrap;
}

.sa-meta-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem;
}

.sa-meta-item {
    padding: .8rem;
    border-radius: .85rem;
    background: var(--color-background-secondary, #f9fafb);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .07));
}

.sa-meta-label {
    margin-bottom: .25rem;
    color: var(--color-text-secondary, #6b7280);
    font-size: .7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.sa-meta-value {
    color: var(--color-text-primary, #111827);
    font-size: .86rem;
    font-weight: 850;
    word-break: break-word;
}

.sa-meta-value.sa-muted {
    color: #9ca3af;
}

.sa-meta-value.sa-positive {
    color: #1d9e75;
}

.sa-meta-value.sa-negative {
    color: #b42318;
}

.sa-close-form-wrap {
    margin-top: .9rem;
    padding-top: .9rem;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-close-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: .75rem;
    align-items: end;
}

.sa-close-button {
    height: 44px;
    padding: 0 1rem;
    border: 1px solid rgba(180, 35, 24, .18);
    border-radius: .8rem;
    background: #fff;
    color: #b42318;
    font-size: .84rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color .18s ease, border-color .18s ease;
}

.sa-close-button:hover {
    background: #fef3f2;
    border-color: rgba(180, 35, 24, .35);
}

.sa-empty {
    padding: 2.7rem 1rem;
    text-align: center;
    color: var(--color-text-secondary, #6b7280);
    background: var(--color-background-primary, #fff);
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .10));
    border-radius: 1rem;
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

.sa-empty strong {
    display: block;
    color: var(--color-text-primary, #111827);
    font-size: .95rem;
    font-weight: 800;
}

.sa-empty p {
    margin: .35rem auto 0;
    max-width: 360px;
    font-size: .82rem;
    line-height: 1.5;
}

@media (max-width: 900px) {
    .sa-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-meta-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sa-form-grid,
    .sa-close-form {
        grid-template-columns: 1fr;
    }

    .sa-open-button,
    .sa-close-button {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .sa-header,
    .sa-shift-top,
    .sa-section-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .sa-summary-grid,
    .sa-meta-grid {
        grid-template-columns: 1fr;
    }

    .sa-shift-time {
        text-align: left;
        white-space: normal;
    }
}
</style>

<div class="sa-shift-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Cashier Shift</span>
            <h2>
                <i class="ti ti-clock-dollar" aria-hidden="true"></i>
                Shift Kasir
            </h2>
            <p>Kelola shift aktif dan riwayat shift Anda.</p>
        </div>

        <?php if ($hasOpenShift): ?>
            <span class="sa-status-pill sa-status-open">
                <span class="sa-dot"></span>
                <?= count($activeShifts) ?> shift aktif
            </span>
        <?php else: ?>
            <span class="sa-status-pill sa-status-closed">
                Tidak ada shift aktif
            </span>
        <?php endif; ?>
    </div>

    <div class="sa-summary-grid">
        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-blue">
                <i class="ti ti-calendar-day" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Shift Hari Ini</span>
            <div class="sa-summary-value sa-blue"><?= number_format(count($shifts)) ?></div>
            <span class="sa-summary-sub">total shift tercatat</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-green">
                <i class="ti ti-cash" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Total Kas Masuk</span>
            <div class="sa-summary-value sa-green"><?= formatRupiah($totalKas) ?></div>
            <span class="sa-summary-sub">dari shift selesai</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-orange">
                <i class="ti ti-player-play" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Shift Aktif</span>
            <div class="sa-summary-value"><?= number_format(count($activeShifts)) ?></div>
            <span class="sa-summary-sub">sedang berjalan</span>
        </div>

        <div class="sa-summary-card">
            <div class="sa-summary-icon sa-icon-purple">
                <i class="ti ti-circle-check" aria-hidden="true"></i>
            </div>
            <span class="sa-summary-label">Shift Selesai</span>
            <div class="sa-summary-value"><?= number_format(count($closedShifts)) ?></div>
            <span class="sa-summary-sub">sudah ditutup</span>
        </div>
    </div>

    <?php if (!$hasOpenShift): ?>
        <div class="sa-panel">
            <div class="sa-panel-header">
                <div>
                    <div class="sa-panel-title">
                        <i class="ti ti-player-play" aria-hidden="true"></i>
                        Buka Shift Baru
                    </div>
                    <div class="sa-panel-subtitle">
                        Masukkan kas awal sebelum mulai menerima transaksi.
                    </div>
                </div>
            </div>

            <div class="sa-open-shift-body">
                <div class="sa-open-info">
                    <div class="sa-open-icon">
                        <i class="ti ti-cash-register" aria-hidden="true"></i>
                    </div>

                    <div>
                        <div class="sa-open-title">Mulai operasional kasir</div>
                        <div class="sa-open-text">
                            Kas awal akan menjadi dasar pencatatan saat shift ditutup nanti.
                        </div>
                    </div>
                </div>

                <form action="<?= url('/kasir/shift/open') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="sa-form-grid">
                        <div class="sa-form-group">
                            <label class="sa-form-label" for="opening_cash">
                                Kas awal (Rp)
                            </label>

                            <div class="sa-input-wrap">
                                <i class="ti ti-cash sa-input-icon" aria-hidden="true"></i>
                                <input
                                    type="number"
                                    id="opening_cash"
                                    name="opening_cash"
                                    class="sa-form-input"
                                    placeholder="Contoh: 500000"
                                    min="0"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="sa-open-button">
                            <i class="ti ti-player-play" aria-hidden="true"></i>
                            Buka Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="sa-section-head">
        <h3 class="sa-section-title">
            <i class="ti ti-history" aria-hidden="true"></i>
            Riwayat Shift
        </h3>

        <span class="sa-section-count">
            <?= number_format(count($shifts)) ?> shift
        </span>
    </div>

    <?php if (empty($shifts)): ?>
        <div class="sa-empty">
            <div class="sa-empty-icon">
                <i class="ti ti-calendar-off" aria-hidden="true"></i>
            </div>

            <strong>Belum ada shift hari ini</strong>
            <p>Buka shift baru untuk mulai mencatat aktivitas kasir.</p>
        </div>
    <?php else: ?>
        <div class="sa-shift-list">
            <?php foreach ($shifts as $shift): ?>
                <?php
                $isOpen = ($shift['status'] ?? '') === 'open';
                $openingCash = (float) ($shift['opening_cash'] ?? 0);
                $closingCash = (float) ($shift['closing_cash'] ?? 0);
                $selisih = $closingCash - $openingCash;
                ?>
                <div class="sa-shift-card <?= $isOpen ? 'is-open' : '' ?>">

                    <div class="sa-shift-top">
                        <div class="sa-shift-id">
                            Shift #<?= (int) ($shift['id'] ?? 0) ?>

                            <span class="sa-status-pill <?= $isOpen ? 'sa-status-open' : 'sa-status-closed' ?>">
                                <?php if ($isOpen): ?>
                                    <span class="sa-dot"></span>
                                <?php endif; ?>
                                <?= $isOpen ? 'Aktif' : 'Selesai' ?>
                            </span>
                        </div>

                        <div class="sa-shift-time">
                            <i class="ti ti-clock" aria-hidden="true"></i>
                            <?= e(date('H:i, d M Y', strtotime($shift['opened_at'] ?? 'now'))) ?>

                            <?php if (!$isOpen && !empty($shift['closed_at'])): ?>
                                →
                                <?= e(date('H:i', strtotime($shift['closed_at']))) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sa-meta-grid">
                        <div class="sa-meta-item">
                            <div class="sa-meta-label">Kas Awal</div>
                            <div class="sa-meta-value">
                                <?= formatRupiah($openingCash) ?>
                            </div>
                        </div>

                        <div class="sa-meta-item">
                            <div class="sa-meta-label">Kas Akhir</div>
                            <div class="sa-meta-value <?= $isOpen ? 'sa-muted' : '' ?>">
                                <?= $isOpen ? '—' : formatRupiah($closingCash) ?>
                            </div>
                        </div>

                        <?php if (!$isOpen): ?>
                            <div class="sa-meta-item">
                                <div class="sa-meta-label">Selisih</div>
                                <div class="sa-meta-value <?= $selisih >= 0 ? 'sa-positive' : 'sa-negative' ?>">
                                    <?= ($selisih >= 0 ? '+' : '') . formatRupiah($selisih) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isOpen): ?>
                        <div class="sa-close-form-wrap">
                            <form
                                action="<?= url('/kasir/shift/close') ?>"
                                method="POST"
                                class="sa-close-form"
                            >
                                <?= csrf_field() ?>

                                <input type="hidden" name="id" value="<?= (int) ($shift['id'] ?? 0) ?>">

                                <div class="sa-form-group">
                                    <label
                                        class="sa-form-label"
                                        for="closing_cash_<?= (int) ($shift['id'] ?? 0) ?>"
                                    >
                                        Kas akhir (Rp)
                                    </label>

                                    <div class="sa-input-wrap">
                                        <i class="ti ti-cash sa-input-icon" aria-hidden="true"></i>
                                        <input
                                            type="number"
                                            id="closing_cash_<?= (int) ($shift['id'] ?? 0) ?>"
                                            name="closing_cash"
                                            class="sa-form-input"
                                            placeholder="Hitung kas di laci..."
                                            min="0"
                                            required
                                        >
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="sa-close-button"
                                    onclick="return confirm('Tutup shift ini?')"
                                >
                                    <i class="ti ti-player-stop" aria-hidden="true"></i>
                                    Tutup Shift
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>