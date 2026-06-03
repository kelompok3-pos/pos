<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0"><i class="bi bi-people-fill text-primary"></i> Manajemen Pengguna</h2>
        <p class="text-muted fw-semibold mb-0">Daftar otoritas tim kasir dan administrator sistem POS</p>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-light py-3 border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock me-1"></i> Otoritas Akun Aktif</h5>
        <div class="position-relative" style="width: 250px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" class="form-control ps-5 rounded fw-semibold" id="searchUser" placeholder="Cari email akun...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3" style="width: 70px;">No</th>
                    <th>Alamat Email Karyawan</th>
                    <th>Hak Akses Posisi</th>
                    <th>Mulai Bergabung</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody" class="fw-semibold text-dark">
                <?php foreach ($users as $index => $u): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $index + 1 ?></td>
                        <td class="fw-bold"><?= e($u['email']) ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-primary px-3 py-2"><i class="bi bi-shield-shaded me-1"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge bg-success px-3 py-2"><i class="bi bi-person-badge me-1"></i> Kasir</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= date('d M Y • H:i', strtotime($u['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="<?= url('/admin/user/delete') ?>?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger px-2" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan akun <?= e($u['email']) ?>?')">
                                <i class="bi bi-trash-fill"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Fitur Live Search Email
    document.getElementById('searchUser').addEventListener('input', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');
        rows.forEach(row => {
            const email = row.children[1].textContent.toLowerCase();
            row.style.display = email.includes(value) ? '' : 'none';
        });
    });
</script>