<div class="d-flex align-items-center gap-2 mb-3">
<a href="<?= url('/admin/user') ?>" class="btn btn-sm btn-ghost">
<i class="ti ti-arrow-left" aria-hidden="true"></i> Kembali </a>
</div>
<div class="flex-row flex-between mb-4">
<div>
<h2 class="text-dark m-0">
<i class="ti ti-edit text-warning" aria-hidden="true"></i> Edit Data User </h2>
<p class="text-muted mb-0">Ubah informasi akun atau perbarui hak akses pengguna sistem</p>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="card ">
<div class="card-body p-4">
<form method="POST" action="<?= url('/admin/user/update') ?>">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $user['id'] ?>">
<div class="mb-3">
<label for="name" class="form-label text-dark">Nama Lengkap</label>
<input type="text" class="form-input" id="name" name="name" value="<?= e($user['name']) ?>" required>
</div>
<div class="mb-3">
<label for="email" class="form-label text-dark">Alamat Email</label>
<input type="email" class="form-input" id="email" name="email" value="<?= e($user['email']) ?>" required>
</div>
<div class="mb-3">
<label for="password" class="form-label text-dark">Password Baru</label>
<input type="password" class="form-input" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
<div class="form-text text-muted">Abaikan kolom ini jika user tidak ingin mengganti password lamanya.</div>
</div>
<div class="mb-4">
<label class="form-label text-dark">Role Jabatan</label>
<input type="text" class="form-input" value="<?= e(roleLabel($user['role'])) ?>" disabled>
<div class="form-text">Role tidak dapat diubah setelah akun dibuat.</div>
</div>
<?php if (isSuperAdmin() && $user['role'] === 'kasir'): ?>
<div class="mb-4">
<label for="assigned_admin_id" class="form-label text-dark">Admin Pengelola Kasir</label>
<select class="form-input" id="assigned_admin_id" name="assigned_admin_id" required>
<?php foreach ($admins as $admin): ?>
<option value="<?= $admin['id'] ?>" <?= (int) $user['assigned_admin_id'] === (int) $admin['id'] ? 'selected' : '' ?>>
<?= e($admin['tenant_name'] ?? 'Tenant') ?> - <?= e($admin['name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<?php endif; ?>
<div class="flex-row justify-content-end border-top pt-3">
<button type="submit" class="btn btn-primary px-4">
<i class="ti ti-device-floppy" aria-hidden="true"></i> Simpan Perubahan </button>
</div>
</form>
</div>
</div>
</div>
</div>
