<!-- ============================================================ -->
<!-- ADMIN: MANAJEMEN USER -->
<!-- ============================================================ -->
<div class="page-hero">
<div class="page-title">
<h2 class="text-dark m-0">
<i class="ti ti-users text-brand" aria-hidden="true"></i> Manajemen User </h2>
<p class="text-muted mb-0">Kelola akun tim aktif sesuai hak akses</p>
</div>
<a href="<?= url('/admin/user/create') ?>" class="btn btn-primary px-4 py-2">
<i class="ti ti-user-plus" aria-hidden="true"></i> Tambah User </a>
</div>
<div class="card surface">
<div class="card-body border-bottom">
<form method="GET" action="<?= url('/admin/user') ?>" class="row g-2">
<?php if (isSuperAdmin()): ?>
<div class="col-md-3">
<select name="role" class="form-input">
<option value="">All roles</option>
<?php foreach (['super_admin', 'admin', 'kasir'] as $role): ?>
<option value="<?= $role ?>" <?= ($_GET['role'] ?? '') === $role ? 'selected' : '' ?>><?= e(roleLabel($role)) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-3">
<select name="status" class="form-input">
<option value="">All status</option>
<option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
<option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
</select>
</div>
<div class="col-md-2"><button class="btn btn-outline">Filter</button></div>
<?php endif; ?>
</form>
</div>
<div class="card-header py-3 d-flex align-items-center justify-content-between">
<h5 class="mb-0 text-dark">
<i class="ti ti-shield-lock me-1" aria-hidden="true"></i> Daftar Akun Aktif </h5>
<div class="input-search-wrapper">
<i class="ti ti-search input-search-icon" aria-hidden="true"></i>
<input type="text" class="form-input-search" id="searchUser" placeholder="Cari nama atau email...">
</div>
</div>
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="">
<tr>
<th class="ps-4">#</th>
<th>Nama</th>
<th>Email</th>
<?php if (isSuperAdmin()): ?><th>Role</th><?php endif; ?>
<th>Terdaftar</th>
<?php if (isSuperAdmin()): ?><th>Tenant</th><th>Admin Pengelola</th><th>Dibuat Oleh</th><th>Status</th><?php endif; ?>
<th class="text-center">Aksi</th>
</tr>
</thead>
<tbody id="userTableBody">
<?php if (empty($users)): ?>
<tr>
<td colspan="<?= isSuperAdmin() ? 10 : 5 ?>" class="text-center text-muted py-5 ">
<i class="ti ti-users text-h1 text-secondary d-block mb-2" aria-hidden="true"></i> Belum ada data user aktif di database. </td>
</tr>
<?php else: ?>
<?php foreach ($users as $index => $u): ?>
<?php $isCurrentUser = (int) $u['id'] === (int) ($_SESSION['user_id'] ?? 0); $canManageUser = canManageUser($u); $canDeleteUser = !$isCurrentUser && $canManageUser && $u['deleted_at'] === null; ?>
<tr>
<td class="text-muted ps-4 "><?= $index + 1 ?></td>
<td class="text-dark"><?= e($u['name']) ?></td>
<td class="text-muted "><?= e($u['email']) ?></td>
<?php if (isSuperAdmin()): ?><td>
<?php if ($u['role'] === 'super_admin'): ?>
<span class="badge badge-neutral">
<i class="ti ti-shield-lock me-1" aria-hidden="true"></i>
<?= e(roleLabel($u['role'])) ?>
</span>
<?php elseif ($u['role'] === 'admin'): ?>
<span class="badge badge-primary">
<i class="ti ti-shield me-1" aria-hidden="true"></i>
<?= e(roleLabel($u['role'])) ?>
</span>
<?php else: ?>
<span class="badge badge-success">
<i class="ti ti-user me-1" aria-hidden="true"></i>
<?= e(roleLabel($u['role'])) ?>
</span>
<?php endif; ?>
</td>
<td class="text-muted small">
<?= date('d M Y', strtotime($u['created_at'])) ?>
</td><?php endif; ?>
<?php if (isSuperAdmin()): ?><td><?= e($u['tenant_name'] ?? '-') ?></td>
<td><?= e($u['assigned_admin_name'] ?? '-') ?></td>
<td><?= e($u['created_by_name'] ?? '-') ?></td>
<td><span class="badge <?= $u['deleted_at'] === null ? 'badge-success' : 'badge-neutral' ?>"><?= $u['deleted_at'] === null ? 'Active' : 'Inactive' ?></span></td><?php endif; ?>
<td class="text-center">
<?php if ($canManageUser && $u['deleted_at'] === null): ?>
<a href="<?= url('/admin/user/edit') ?>?id=<?= e((string) $u['id']) ?>" class="btn btn-sm btn-outline btn-icon" aria-label="Edit user">
<i class="ti ti-edit" aria-hidden="true"></i>
</a>
<?php endif; ?>
<?php if ($canDeleteUser): ?>
<form action="<?= url('/admin/user/delete') ?>" method="POST" class="d-inline">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= e((string) $u['id']) ?>">
<button type="submit" class="btn btn-sm btn-danger btn-icon" aria-label="Deactivate user" onclick="return confirm('Nonaktifkan user <?= e($u['name']) ?>?')">
<i class="ti ti-trash" aria-hidden="true"></i>
</button>
</form>
<?php endif; ?>
<?php if ($canManageUser && $u['deleted_at'] === null): ?>
<form action="<?= url('/admin/user/reset-password') ?>" method="POST" class="d-inline" onsubmit="return setResetPassword(this)">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= e((string) $u['id']) ?>">
<input type="hidden" name="password">
<button type="submit" class="btn btn-sm btn-outline btn-icon" title="Reset Password" aria-label="Reset password"><i class="ti ti-key" aria-hidden="true"></i></button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<script> document.getElementById('searchUser').addEventListener('input', function() { const value = this.value.toLowerCase(); const rows = document.querySelectorAll('#userTableBody tr'); rows.forEach(row => { const text = row.textContent.toLowerCase(); row.style.display = text.includes(value) ? '' : 'none'; }); }); </script>
<script>
function setResetPassword(form) { const password = prompt('Masukkan password baru (minimal 8 karakter):'); if (!password) return false; form.elements.password.value = password; return true;
}
</script>
