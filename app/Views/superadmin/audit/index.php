<?php
$rows = $rows ?? [];
$storeOptions = $storeOptions ?? [];
?>

<style>
.sa-audit-page {
    width: 100%;
}

.sa-audit-page * {
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
}

.sa-title p {
    margin: .35rem 0 0;
    font-size: .875rem;
    color: var(--color-text-secondary, #6b7280);
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
}

.sa-panel-subtitle {
    margin-top: .2rem;
    font-size: .78rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-filter-body {
    padding: 1rem 1.1rem;
}

.sa-filter-grid {
    display: grid;
    grid-template-columns: minmax(180px, 1.3fr) minmax(140px, .8fr) minmax(140px, .8fr) minmax(140px, .8fr) minmax(160px, .8fr);
    gap: .85rem;
    align-items: end;
}

.sa-form-group {
    min-width: 0;
}

.sa-form-label {
    display: block;
    margin-bottom: .4rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-input {
    width: 100%;
    height: 42px;
    border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .14));
    background: var(--color-background-primary, #fff);
    color: var(--color-text-primary, #111827);
    border-radius: .75rem;
    padding: .55rem .75rem;
    font-size: .85rem;
    outline: none;
    transition: border-color .16s ease, box-shadow .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-filter-button {
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
    transition: background-color .18s ease, transform .18s ease, box-shadow .18s ease;
}

.sa-filter-button:hover {
    background: #2f7bc6;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(55, 138, 221, .22);
}

.sa-filter-button:active {
    transform: translateY(0);
    box-shadow: none;
}

.sa-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.sa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .815rem;
    min-width: 1050px;
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

.sa-table tbody tr {
    transition: background-color .16s ease;
}

.sa-table tbody tr:hover td {
    background: rgba(55, 138, 221, .045);
}

.sa-table tbody tr:last-child td {
    border-bottom: none;
}

.sa-time {
    font-weight: 700;
    white-space: nowrap;
}

.sa-meta-muted {
    color: var(--color-text-secondary, #6b7280);
}

.sa-user-cell,
.sa-store-cell {
    font-weight: 650;
    white-space: nowrap;
}

.sa-action-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .32rem .65rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 800;
    line-height: 1;
    background: #f3f4f6;
    color: #4b5563;
    white-space: nowrap;
}

.sa-action-dot {
    width: .42rem;
    height: .42rem;
    border-radius: 50%;
    background: #9ca3af;
}

.sa-target {
    font-weight: 700;
    white-space: nowrap;
}

.sa-details {
    max-width: 320px;
}

.sa-details summary {
    cursor: pointer;
    font-size: .76rem;
    font-weight: 700;
    color: #378add;
    list-style: none;
}

.sa-details summary::-webkit-details-marker {
    display: none;
}

.sa-details summary:hover {
    color: #2f7bc6;
}

.sa-audit-json {
    margin: .6rem 0 0;
    max-width: 320px;
    max-height: 180px;
    overflow: auto;
    padding: .75rem;
    border-radius: .75rem;
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, .08);
    color: #334155;
    font-size: .72rem;
    line-height: 1.45;
    white-space: pre-wrap;
    word-break: break-word;
}

.sa-ip {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: .78rem;
    white-space: nowrap;
    color: var(--color-text-secondary, #6b7280);
}

.sa-empty {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--color-text-tertiary, #9ca3af);
}

@media (max-width: 1100px) {
    .sa-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sa-audit-page">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">Security</span>
            <h2>Audit Log</h2>
            <p>Jejak aktivitas append-only seluruh platform.</p>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div class="sa-panel-title">Filter Audit</div>
            <div class="sa-panel-subtitle">Cari aktivitas berdasarkan toko, aksi, dan rentang tanggal.</div>
        </div>

        <div class="sa-filter-body">
            <form method="GET" action="<?= url('/superadmin/audit') ?>" class="sa-filter-grid">
                <div class="sa-form-group">
                    <label class="sa-form-label">Toko</label>
                    <select class="sa-form-input" name="store_id">
                        <option value="">Semua toko</option>

                        <?php foreach ($storeOptions as $store): ?>
                            <option
                                value="<?= (int) $store['id'] ?>"
                                <?= (int) ($_GET['store_id'] ?? 0) === (int) $store['id'] ? 'selected' : '' ?>
                            >
                                <?= e($store['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sa-form-group">
                    <label class="sa-form-label">Aksi</label>
                    <input
                        class="sa-form-input"
                        name="action"
                        value="<?= e($_GET['action'] ?? '') ?>"
                        placeholder="CREATE"
                    >
                </div>

                <div class="sa-form-group">
                    <label class="sa-form-label">Dari</label>
                    <input
                        class="sa-form-input"
                        type="date"
                        name="from"
                        value="<?= e($_GET['from'] ?? '') ?>"
                    >
                </div>

                <div class="sa-form-group">
                    <label class="sa-form-label">Sampai</label>
                    <input
                        class="sa-form-input"
                        type="date"
                        name="to"
                        value="<?= e($_GET['to'] ?? '') ?>"
                    >
                </div>

                <div class="sa-form-group">
                    <button type="submit" class="sa-filter-button">
                        <i class="ti ti-filter" aria-hidden="true"></i>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div class="sa-panel-title">Riwayat Aktivitas</div>
            <div class="sa-panel-subtitle">Menampilkan maksimal 50 aktivitas terbaru sesuai filter.</div>
        </div>

        <div class="sa-table-wrap">
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Toko</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Target</th>
                        <th>Perubahan</th>
                        <th>IP</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td>
                                <span class="sa-time">
                                    <?= e($row['created_at'] ?? '-') ?>
                                </span>
                            </td>

                            <td>
                                <span class="sa-store-cell">
                                    <?= e($row['store_name'] ?? 'Global') ?>
                                </span>
                            </td>

                            <td>
                                <span class="sa-user-cell">
                                    <?= e($row['user_name'] ?? 'System') ?>
                                </span>
                            </td>

                            <td>
                                <span class="sa-action-pill">
                                    <span class="sa-action-dot"></span>
                                    <?= e(strtoupper($row['action'] ?? '-')) ?>
                                </span>
                            </td>

                            <td>
                                <span class="sa-target">
                                    <?= e($row['target_table'] ?? '-') ?>
                                    #<?= e((string) ($row['target_id'] ?? '-')) ?>
                                </span>
                            </td>

                            <td>
                                <details class="sa-details">
                                    <summary>Lihat data</summary>
                                    <pre class="sa-audit-json"><?= e($row['new_value'] ?? $row['old_value'] ?? '{}') ?></pre>
                                </details>
                            </td>

                            <td>
                                <span class="sa-ip">
                                    <?= e($row['ip_address'] ?? '-') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="7" class="sa-empty">
                                Belum ada audit log.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>