<?php
$settings = $settings ?? [];

$fields = [
    'store_name' => [
        'label' => 'Store Name',
        'type' => 'text',
        'placeholder' => 'Nama toko',
        'icon' => 'ti-building-store',
    ],
    'store_address' => [
        'label' => 'Store Address',
        'type' => 'text',
        'placeholder' => 'Alamat toko',
        'icon' => 'ti-map-pin',
    ],
    'currency_symbol' => [
        'label' => 'Currency Symbol',
        'type' => 'text',
        'placeholder' => 'Rp',
        'icon' => 'ti-cash',
    ],
    'tax_percentage' => [
        'label' => 'Tax Percentage',
        'type' => 'number',
        'placeholder' => '0',
        'icon' => 'ti-percentage',
    ],
    'receipt_footer' => [
        'label' => 'Receipt Footer',
        'type' => 'text',
        'placeholder' => 'Terima kasih sudah berbelanja',
        'icon' => 'ti-receipt',
    ],
    'system_timezone' => [
        'label' => 'System Timezone',
        'type' => 'text',
        'placeholder' => 'Asia/Jakarta',
        'icon' => 'ti-clock',
    ],
];
?>

<style>
.sa-settings {
    width: 100%;
}

.sa-settings * {
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
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}

.sa-form-input:focus {
    border-color: rgba(55, 138, 221, .55);
    box-shadow: 0 0 0 3px rgba(55, 138, 221, .12);
}

.sa-file-box {
    border: 1px dashed rgba(55, 138, 221, .35);
    background: rgba(55, 138, 221, .045);
    border-radius: 1rem;
    padding: 1rem;
    transition: border-color .18s ease, background-color .18s ease;
}

.sa-file-box:hover {
    border-color: rgba(55, 138, 221, .55);
    background: rgba(55, 138, 221, .07);
}

.sa-file-content {
    display: flex;
    align-items: center;
    gap: .85rem;
}

.sa-file-icon {
    width: 42px;
    height: 42px;
    border-radius: .85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(55, 138, 221, .12);
    color: #378add;
    font-size: 1.2rem;
    flex: 0 0 auto;
}

.sa-file-title {
    font-size: .85rem;
    font-weight: 750;
    color: var(--color-text-primary, #111827);
}

.sa-file-subtitle {
    margin-top: .15rem;
    font-size: .72rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-file-input {
    margin-top: .8rem;
    width: 100%;
    font-size: .82rem;
    color: var(--color-text-secondary, #6b7280);
}

.sa-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    padding-top: 1.1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, .08));
}

.sa-save-button {
    min-width: 150px;
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

.sa-save-button:hover {
    background: #2f7bc6;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(55, 138, 221, .22);
}

.sa-save-button:active {
    transform: translateY(0);
    box-shadow: none;
}

@media (max-width: 800px) {
    .sa-form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .sa-header {
        flex-direction: column;
    }

    .sa-form-actions {
        justify-content: stretch;
    }

    .sa-save-button {
        width: 100%;
    }
}
</style>

<div class="sa-settings">

    <div class="sa-header">
        <div class="sa-title">
            <span class="sa-eyebrow">System</span>
            <h2>
                <i class="ti ti-settings" aria-hidden="true"></i>
                Settings
            </h2>
            <p>System-level store and receipt settings.</p>
        </div>
    </div>

    <div class="sa-panel">
        <div class="sa-panel-header">
            <div class="sa-panel-title">Store Configuration</div>
            <div class="sa-panel-subtitle">Atur identitas toko, pajak, footer struk, dan zona waktu sistem.</div>
        </div>

        <form action="<?= url('/settings/update') ?>" method="POST" enctype="multipart/form-data">
            <div class="sa-form-body">
                <?= csrf_field() ?>

                <div class="sa-form-grid">

                    <div class="sa-form-group sa-form-group-full">
                        <label class="sa-form-label">Store Logo</label>

                        <div class="sa-file-box">
                            <div class="sa-file-content">
                                <div class="sa-file-icon">
                                    <i class="ti ti-photo-up" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <div class="sa-file-title">Upload logo toko</div>
                                    <div class="sa-file-subtitle">Format yang didukung: PNG, JPG, JPEG, atau WEBP.</div>
                                </div>
                            </div>

                            <input
                                type="file"
                                name="store_logo"
                                class="sa-file-input"
                                accept=".png,.jpg,.jpeg,.webp"
                            >
                        </div>
                    </div>

                    <?php foreach ($fields as $key => $field): ?>
                        <div class="sa-form-group">
                            <label class="sa-form-label">
                                <?= e($field['label']) ?>
                            </label>

                            <div class="sa-input-wrap">
                                <i class="ti <?= e($field['icon']) ?> sa-input-icon" aria-hidden="true"></i>

                                <input
                                    class="sa-form-input"
                                    type="<?= e($field['type']) ?>"
                                    name="<?= e($key) ?>"
                                    value="<?= e($settings[$key] ?? '') ?>"
                                    placeholder="<?= e($field['placeholder']) ?>"
                                    <?= $key === 'tax_percentage' ? 'min="0" step="0.01"' : '' ?>
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="sa-form-actions">
                    <button type="submit" class="sa-save-button">
                        <i class="ti ti-device-floppy" aria-hidden="true"></i>
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>