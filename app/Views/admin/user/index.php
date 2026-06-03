<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-people-fill text-primary"></i> Manajemen User
        </h2>
        <p class="text-muted mb-0">Kelola akun administrator dan kasir aktif</p>
    </div>
    <a href="/admin/user/create" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 8px;">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-shield-lock me-1"></i> Daftar Akun Aktif
        </h5>
        <div class="position-relative" style="width: 250px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" class="form-control ps-5 rounded fw-semibold" id="searchUser" placeholder="Cari nama atau email..." style="border: 2px solid #cbd5e1; height: 38px;">
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
                        <tr>
                            <td class="text-muted ps-4 fw-bold"><?= $index + 1 ?></td>
                            <td class="fw-bold text-dark"><?= e($u['name']) ?></td>
                            <td class="text-muted fw-semibold"><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2 py-1 fw-bold">
                                        <i class="bi bi-shield-shaded me-1"></i> Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1 fw-bold">
                                        <i class="bi bi-person-badge me-1"></i> Kasir
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted fw-semibold small">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="/admin/user/edit?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning fw-bold px-3 py-1.5" style="border-radius: 6px;">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <form method="POST" action="/admin/user/delete" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan user <?= e($u['name']) ?>?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold px-3 py-1.5" style="border-radius: 6px;">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
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