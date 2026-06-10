<div class="page-hero"><div class="page-title"><h2><i class="ti ti-settings" aria-hidden="true"></i> Settings</h2><p>System-level store and receipt settings.</p></div></div>
<div class="card surface"><div class="card-body"><form action="<?= url('/settings/update') ?>" method="POST" enctype="multipart/form-data">
<?= csrf_field() ?>
<div class="mb-3"><label class="form-label">Store Logo</label><input type="file" name="store_logo" class="form-input" accept=".png,.jpg,.jpeg,.webp"></div>
<?php foreach (['store_name' => 'Store Name', 'store_address' => 'Store Address', 'currency_symbol' => 'Currency Symbol', 'tax_percentage' => 'Tax Percentage', 'receipt_footer' => 'Receipt Footer', 'system_timezone' => 'System Timezone'] as $key => $label): ?>
<div class="mb-3"><label class="form-label"><?= e($label) ?></label><input class="form-input" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>" <?= $key === 'tax_percentage' ? 'type="number" min="0" step="0.01"' : '' ?>></div>
<?php endforeach; ?>
<button class="btn btn-primary">Save Settings</button>
</form></div></div>
