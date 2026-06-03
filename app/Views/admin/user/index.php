<!-- ============================================================ -->
<!-- ADMIN: MANAJEMEN USER -->
<!-- ============================================================ -->

<div class="page-hero">
    <div class="page-title">
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-people-fill text-primary"></i> Manajemen User
        </h2>
        <p class="text-muted mb-0">Kelola akun tim aktif sesuai hak akses</p>
    </div>
    <a href="/admin/user/create" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 8px;">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
</div>

<div class="card modern-card">
    <div class="card-header bg-light py-3 border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-shield-lock me-1"></i> Daftar Akun Aktif
        </h5>
        <div class="position-relative" style="width: 250px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" class="form-control ps-5" id="searchUser" placeholder="Cari nama atau email...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;" class="ps-4">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5 fw-bold">
                            <i class="bi bi-people fs-1 text-secondary d-block mb-2"></i>
                            Belum ada data user aktif di database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $u): ?>
                        <?php
                            $isCurrentUser = (int) $u['id'] === (int) ($_SESSION['user']['id'] ?? 0);
                            $canDeleteUser = !$isCurrentUser
                                && $u['role'] !== 'super_admin'
                                && ($u['role'] !== 'admin' || isSuperAdmin());
                        ?>
                        <tr>
                            <td class="text-muted ps-4 fw-bold"><?= $index + 1 ?></td>
                            <td class="fw-bold text-dark"><?= e($u['name']) ?></td>
                            <td class="text-muted fw-semibold"><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'super_admin'): ?>
                                    <span class="badge bg-dark">
                                        <i class="bi bi-shield-lock me-1"></i> <?= e(roleLabel($u['role'])) ?>
                                    </span>
                                <?php elseif ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-shield-shaded me-1"></i> <?= e(roleLabel($u['role'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-person-badge me-1"></i> <?= e(roleLabel($u['role'])) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted fw-semibold small">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($u['role'] !== 'super_admin' && ($u['role'] !== 'admin' || isSuperAdmin())): ?>
                                    <a href="<?= url('/admin/user/edit') ?>?id=<?= e((string) $u['id']) ?>"
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($canDeleteUser): ?>
                                    <form action="<?= url('/admin/user/delete') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $u['id']) ?>">
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Nonaktifkan user <?= e($u['name']) ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="bi bi-lock"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchUser').addEventListener('input', function() {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll('#userTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>
