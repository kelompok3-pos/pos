<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= url('/admin/user') ?>" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-warning"></i> Edit Data User
        </h2>
        <p class="text-muted mb-0">Ubah informasi akun atau perbarui hak akses pengguna sistem</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                
                <form method="POST" action="<?= url('/admin/user/update') ?>">
                    <?= csrf_field() ?>

                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap</label>
                        <input type="text" class="form-control rounded" id="name" name="name" 
                               value="<?= e($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-dark">Alamat Email</label>
                        <input type="email" class="form-control rounded" id="email" name="email" 
                               value="<?= e($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-dark">Password Baru</label>
                        <input type="password" class="form-control rounded" id="password" name="password" 
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text text-muted">Abaikan kolom ini jika user tidak ingin mengganti password lamanya.</div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold text-dark">Role Jabatan</label>
                        <select class="form-select rounded" id="role" name="role" required>
                            <?php if (!empty($canAssignAdmin)): ?>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Administrator)</option>
                            <?php endif; ?>
                            <option value="kasir" <?= $user['role'] === 'kasir' ? 'selected' : '' ?>>Kasir (Staff Toko)</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
