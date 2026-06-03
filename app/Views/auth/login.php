<!-- ============================================================ -->
<!-- HALAMAN LOGIN -->
<!-- ============================================================ -->

<div class="row justify-content-center min-vh-75">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shop fs-1 text-primary"></i>
                    <h4 class="mt-2 fw-bold"><?= APP_NAME ?></h4>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>

                <form method="POST" action="<?= url('/login/authenticate') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= old('email') ?>" placeholder="admin@pos.com"
                               required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
