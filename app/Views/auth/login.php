<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> - <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #111827;
        }

        .login-shell {
            background:
                radial-gradient(circle at 12% 8%, rgba(37, 99, 235, 0.12), transparent 30rem),
                radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.12), transparent 28rem),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 55%, #eef4ff 100%);
            min-height: 100vh;
            padding: 48px 0;
            position: relative;
        }

        .login-shell::before {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.035) 1px, transparent 1px);
            background-size: 32px 32px;
            content: "";
            inset: 0;
            pointer-events: none;
            position: absolute;
        }

        .login-panel {
            display: grid;
            gap: 28px;
            grid-template-columns: minmax(0, 1fr) minmax(360px, 430px);
            margin: 0 auto;
            max-width: 1120px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .login-brand {
            align-content: center;
            display: grid;
            min-height: 620px;
        }

        .login-logo {
            align-items: center;
            color: #111827;
            display: inline-flex;
            font-size: 1.05rem;
            gap: 12px;
            margin-bottom: 72px;
            text-decoration: none;
        }

        .login-logo span {
            align-items: center;
            background: #2563eb;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.24);
            color: #fff;
            display: inline-flex;
            font-weight: 900;
            height: 44px;
            justify-content: center;
            width: 44px;
        }

        .login-badge {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            color: #1d4ed8;
            display: inline-flex;
            font-size: 0.82rem;
            font-weight: 800;
            margin-bottom: 18px;
            padding: 8px 14px;
        }

        .login-copy h1 {
            color: #111827;
            font-size: clamp(2.4rem, 5vw, 4.7rem);
            font-weight: 950;
            letter-spacing: -0.06em;
            line-height: 0.98;
            margin: 0;
            max-width: 720px;
        }

        .login-copy p {
            color: #64748b;
            font-size: 1.08rem;
            line-height: 1.8;
            margin: 22px 0 0;
            max-width: 620px;
        }

        .login-metrics {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 44px;
            max-width: 620px;
        }

        .login-metrics div {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.06);
            padding: 18px;
        }

        .login-metrics strong {
            color: #111827;
            display: block;
            font-size: 1.25rem;
        }

        .login-metrics span {
            color: #64748b;
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 4px;
        }

        .login-card-wrap {
            align-items: center;
            display: flex;
            min-height: 620px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 30px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(24px);
            padding: 34px;
            width: 100%;
        }

        .login-alert {
            align-items: flex-start;
            border-radius: 16px;
            display: flex;
            font-weight: 700;
            gap: 10px;
            margin-bottom: 18px;
            padding: 12px 14px;
        }

        .login-alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .login-alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .login-card-icon {
            align-items: center;
            background: #2563eb;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.18);
            color: #fff;
            display: inline-flex;
            font-size: 1.4rem;
            height: 52px;
            justify-content: center;
            margin-bottom: 18px;
            width: 52px;
        }

        .login-card h2 {
            color: #111827;
            font-size: 2rem;
            font-weight: 950;
            letter-spacing: -0.04em;
            margin: 0;
        }

        .login-card p {
            color: #64748b;
            margin: 8px 0 0;
        }

        .login-field {
            margin-bottom: 16px;
        }

        .login-field label {
            color: #334155;
            display: block;
            font-size: 0.9rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .login-input-wrap {
            align-items: center;
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 14px;
            display: flex;
            gap: 10px;
            padding: 0 14px;
        }

        .login-input-wrap:focus-within {
            background: #fff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .login-input-wrap > i {
            color: #64748b;
        }

        .login-input-wrap input {
            background: transparent;
            border: 0;
            color: #111827;
            flex: 1;
            font: inherit;
            min-width: 0;
            outline: 0;
            padding: 14px 0;
        }

        .login-input-wrap button {
            background: transparent;
            border: 0;
            color: #64748b;
            cursor: pointer;
            padding: 0;
        }

        .login-submit {
            align-items: center;
            background: #2563eb;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.18);
            color: #fff;
            cursor: pointer;
            display: flex;
            font: inherit;
            font-weight: 900;
            justify-content: center;
            margin-top: 8px;
            padding: 14px 18px;
            width: 100%;
        }

        .login-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .login-submit:disabled {
            background: #94a3b8;
            cursor: wait;
        }

        .login-submit i {
            margin-left: 10px;
        }

        .login-hint {
            border-top: 1px solid #e2e8f0;
            display: grid;
            gap: 10px;
            margin-top: 24px;
            padding-top: 20px;
        }

        .login-hint div {
            align-items: center;
            display: flex;
            justify-content: space-between;
        }

        .login-hint span {
            color: #64748b;
            font-size: 0.86rem;
            font-weight: 800;
        }

        .login-hint code {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #0f172a;
            padding: 5px 9px;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 900px) {
            .login-panel {
                grid-template-columns: 1fr;
            }

            .login-brand,
            .login-card-wrap {
                min-height: auto;
            }

            .login-logo {
                margin-bottom: 42px;
            }
        }

        @media (max-width: 575.98px) {
            .login-shell {
                padding: 28px 0;
            }

            .login-card {
                border-radius: 22px;
                padding: 24px;
            }

            .login-metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <main class="login-shell">
        <div class="login-panel">
            <section class="login-brand">
                <a href="<?= url('/') ?>" class="login-logo" aria-label="Kembali ke landing page">
                    <span>P</span>
                    <strong><?= e(APP_NAME) ?></strong>
                </a>

                <div class="login-copy">
                    <span class="login-badge">Admin & Kasir Access</span>
                    <h1>Masuk cepat, lanjutkan operasional toko.</h1>
                    <p>
                        Akses dashboard, transaksi, laporan harian, stok rendah, dan cetak struk dari satu aplikasi POS.
                    </p>
                </div>

                <div class="login-metrics" aria-label="Ringkasan fitur POS">
                    <div>
                        <strong>CSV</strong>
                        <span>Export laporan</span>
                    </div>
                    <div>
                        <strong>80mm</strong>
                        <span>Print struk</span>
                    </div>
                    <div>
                        <strong>Live</strong>
                        <span>Update stok</span>
                    </div>
                </div>
            </section>

            <section class="login-card-wrap">
                <div class="login-card">
                    <?php if ($successMsg = getFlash('success')): ?>
                        <div class="login-alert login-alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= e($successMsg) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMsg = getFlash('error')): ?>
                        <div class="login-alert login-alert-error" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span><?= e($errorMsg) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <div class="login-card-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h2>Login</h2>
                        <p>Gunakan akun tim yang sudah terdaftar.</p>
                    </div>

                    <form id="loginForm" method="POST" action="<?= url('/login/authenticate') ?>">
                        <?= csrf_field() ?>

                        <div class="login-field">
                            <label for="email">Email</label>
                            <div class="login-input-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="<?= old('email') ?>"
                                       placeholder="admin@pos.com"
                                       autocomplete="email"
                                       required
                                       autofocus>
                            </div>
                        </div>

                        <div class="login-field">
                            <label for="password">Password</label>
                            <div class="login-input-wrap">
                                <i class="bi bi-key"></i>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       placeholder="Masukkan password"
                                       autocomplete="current-password"
                                       required>
                                <button type="button" id="togglePassword" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="login-submit">
                            <span>Masuk ke Dashboard</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <div class="login-hint">
                        <div>
                            <span>Admin</span>
                            <code>admin@pos.com</code>
                        </div>
                        <div>
                            <span>Kasir</span>
                            <code>kasir@pos.com</code>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                togglePassword.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
                togglePassword.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', function (event) {
                if (!loginForm.checkValidity()) {
                    return;
                }

                const button = loginForm.querySelector('.login-submit');
                if (!button) {
                    return;
                }

                button.disabled = true;
                button.querySelector('span').textContent = 'Memproses...';
            });
        }

        if (window.gsap) {
            gsap.from('.login-brand > *', {
                y: 24,
                opacity: 0,
                duration: 0.7,
                stagger: 0.08,
                ease: 'power3.out'
            });

            gsap.from('.login-card', {
                y: 28,
                opacity: 0,
                scale: 0.97,
                duration: 0.75,
                delay: 0.15,
                ease: 'power3.out'
            });
        }
    });
    </script>
</body>

</html>
