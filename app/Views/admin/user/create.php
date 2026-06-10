<div class="mb-3">
<a href="<?= url('/admin/user') ?>" class="btn btn-sm btn-ghost">
<i class="ti ti-arrow-left" aria-hidden="true"></i> Kembali ke Daftar </a>
</div>
<div class="row justify-content-center mb-4">
<div class="col-md-8 col-lg-6 text-center">
<h2 class="text-dark m-0">
<i class="ti ti-user-plus text-brand" aria-hidden="true"></i> Tambah User Baru </h2>
<p class="text-muted mb-0">Daftarkan akun administrator atau kasir baru ke dalam sistem aktif toko</p>
</div>
</div>
<div class="page-hero">
<div class="page-title">
<h2><i class="ti ti-user-plus text-brand" aria-hidden="true"></i> Tambah User</h2>
<p>Buat akun <?= isSuperAdmin() ? 'admin atau kasir' : 'kasir' ?> dengan hak akses yang sesuai.</p>
</div>
</div>
<div class="row justify-content-center">
<div class="col-md-7 col-lg-6">
<div class="card surface">
<div class="card-header pt-4 px-4">
<h5 class="mb-0 text-dark">Informasi Akun</h5>
</div>
<div class="card-body">
<form action="<?= url('/admin/user/store') ?>" method="POST">
<?= csrf_field() ?>
<div class="mb-3">
<label for="name" class="form-label text-dark">Nama Lengkap <span class="text-error">*</span></label>
<input type="text" class="form-input " id="name" name="name" value="<?= old('name') ?>" required placeholder="Contoh: John Doe">
</div>
<div class="mb-3">
<label for="email" class="form-label text-dark">Alamat Email <span class="text-error">*</span></label>
<input type="email" class="form-input " id="email" name="email" value="<?= old('email') ?>" required placeholder="Contoh: kasir@pos.com">
</div>
<div class="mb-3">
<label for="password" class="form-label">Password <span class="text-error">*</span></label>
<input type="password" class="form-input" id="password" name="password" required minlength="8" placeholder="Minimal 8 karakter">
</div>
<div class="mb-3">
<label for="role" class="form-label">Role / Jabatan <span class="text-error">*</span></label>
<?php $selectedRole = old('role', 'kasir'); ?>
<select class="form-input" id="role" name="role" required>
<?php foreach ($availableRoles as $role): ?>
<option value="<?= e($role) ?>" <?= $selectedRole === $role ? 'selected' : '' ?>>
<?= e(roleLabel($role)) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<?php if (isSuperAdmin()): ?>
<div class="mb-3" id="tenantNameField">
<label for="tenant_name" class="form-label">Nama Tenant / Perusahaan</label>
<input type="text" class="form-input" id="tenant_name" name="tenant_name" value="<?= old('tenant_name') ?>" placeholder="Contoh: PT Tenant Indonesia">
<div class="form-text">Tenant baru dibuat otomatis bersama akun admin.</div>
</div>
<div class="mb-3" id="assignedAdminField">
<label for="assigned_admin_id" class="form-label">Admin Pengelola Kasir</label>
<select class="form-input" id="assigned_admin_id" name="assigned_admin_id">
<option value="">Pilih admin tenant</option>
<?php foreach ($admins as $admin): ?>
<option value="<?= $admin['id'] ?>">
<?= e($admin['tenant_name'] ?? 'Tenant') ?> - <?= e($admin['name']) ?> (<?= e($admin['email']) ?>) </option>
<?php endforeach; ?>
</select>
<div class="form-text">Kasir akan masuk ke tenant dan dikelola oleh admin ini.</div>
</div>
<?php endif; ?>
<div class="d-flex justify-content-end border-top pt-3">
<button type="submit" class="btn btn-primary px-4 py-2">
<i class="ti ti-check me-1" aria-hidden="true"></i> Simpan User </button>
</div>
</form>
</div>
</div>
</div>
</div>
<?php if (isSuperAdmin()): ?>
<script>
document.addEventListener('DOMContentLoaded', function () { const role = document.getElementById('role'); const tenantField = document.getElementById('tenantNameField'); const tenantInput = document.getElementById('tenant_name'); const adminField = document.getElementById('assignedAdminField'); const adminInput = document.getElementById('assigned_admin_id'); function syncFields() { const createsAdmin = role.value === 'admin'; tenantField.hidden = !createsAdmin; tenantInput.required = createsAdmin; adminField.hidden = createsAdmin; adminInput.required = !createsAdmin; } role.addEventListener('change', syncFields); syncFields();
});
</script>
<?php endif; ?>
