<div class="mb-3">
    <a href="/admin/user" class="btn btn-sm btn-secondary fw-bold px-3" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

<div class="row justify-content-center mb-4">
    <div class="col-md-8 col-lg-6 text-center">
        <h2 class="fw-bold text-dark m-0">
            <i class="bi bi-person-plus-fill text-primary"></i> Tambah User Baru
        </h2>
        <p class="text-muted mb-0">Daftarkan akun administrator atau kasir baru ke dalam sistem aktif toko</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-4">
                
                <form action="/admin/user/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-semibold" id="name" name="name"
                               value="<?= old('name') ?>" style="border-radius: 8px; border: 2px solid #cbd5e1;" 
                               required placeholder="Contoh: John Doe">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control fw-semibold" id="email" name="email"
                               value="<?= old('email') ?>" style="border-radius: 8px; border: 2px solid #cbd5e1;" 
                               required placeholder="Contoh: kasir@pos.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control fw-semibold" id="password" name="password"
                               style="border-radius: 8px; border: 2px solid #cbd5e1;" 
                               required minlength="6" placeholder="Minimal 6 karakter">
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-bold text-dark">Role / Jabatan <span class="text-danger">*</span></label>
                        <select class="form-select fw-semibold" id="role" name="role" style="border-radius: 8px; border: 2px solid #cbd5e1;" required>
                            <option value="" disabled <?= old('role') ? '' : 'selected' ?>>-- Pilih Role Jabatan --</option>
                            <option value="kasir" <?= old('role') === 'kasir' ? 'selected' : '' ?>>Kasir</option>
                            <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 8px;">
                            <i class="bi bi-check-lg me-1"></i> Simpan User
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>