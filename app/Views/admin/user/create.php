<!-- ============================================================ -->
<!-- ADMIN: FORM TAMBAH USER -->
<!-- ============================================================ -->

<div class="page-hero">
    <div class="page-title">
        <h2><i class="bi bi-person-plus text-primary"></i> Tambah User</h2>
        <p>Buat akun kasir<?= !empty($canCreateAdmin) ? ' atau admin' : '' ?> dengan hak akses yang sesuai.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card modern-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 fw-bold text-dark">Informasi Akun</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/user/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= old('name') ?>" required
                               placeholder="Contoh: John Doe">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= old('email') ?>" required
                               placeholder="Contoh: kasir@pos.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password"
                               required minlength="8"
                               placeholder="Minimal 8 karakter">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role / Jabatan <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="kasir" selected>Kasir</option>
                            <?php if (!empty($canCreateAdmin)): ?>
                                <option value="admin">Admin</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= url('/admin/user') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
