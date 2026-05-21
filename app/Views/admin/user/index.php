<!-- ============================================================ -->
<!-- ADMIN: MANAJEMEN USER -->
<!-- ============================================================ -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-people-fill text-primary"></i> Manajemen User
        </h2>
        <p class="text-muted mb-0">Kelola akun administrator dan kasir aktif</p>
    </div>
    <a href="/admin/user/create" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Tambah User
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-light py-3 border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-shield-lock me-1"></i> Daftar Akun Aktif
        </h5>
        <div class="position-relative" style="width: 250px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" class="form-control ps-5 rounded" id="searchUser" placeholder="Cari nama atau email...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1"></i>
                            <p class="mb-0 mt-2">Belum ada user.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $u): ?>
                        <tr>
                            <td class="text-muted"><?= $index + 1 ?></td>
                            <td class="fw-semibold"><?= e($u['name']) ?></td>
                            <td class="text-muted"><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-shield-shaded me-1"></i> Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-person-badge me-1"></i> Kasir
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <a href="/admin/user/delete?id=<?= $u['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Nonaktifkan user <?= e($u['name']) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
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