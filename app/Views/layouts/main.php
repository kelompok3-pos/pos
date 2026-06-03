<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'POS App') ?> - <?= e(APP_NAME) ?></title>

    <script>
        window.tailwind = {
            config: {
                prefix: 'tw-',
                corePlugins: {
                    preflight: false
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (localStorage.getItem('posSidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body class="app-body"
      data-user-id="<?= e((string) ($_SESSION['user']['id'] ?? 'guest')) ?>"
      data-user-role="<?= e($_SESSION['user']['role'] ?? 'guest') ?>">
    <div class="app-shell">
        <aside class="app-sidebar">
            <div class="app-sidebar-header">
                <a class="app-sidebar-brand" href="<?= url('/dashboard') ?>" title="<?= e(APP_NAME) ?>">
                    <span class="app-brand-mark">P</span>
                    <span class="app-sidebar-text"><?= e(APP_NAME) ?></span>
                </a>
                <button type="button"
                        class="app-sidebar-toggle"
                        id="sidebarToggle"
                        data-tour="sidebar-toggle"
                        aria-label="Minimize sidebar"
                        aria-expanded="true"
                        title="Minimize sidebar">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
            </div>

            <?php if (isAuthenticated()): ?>
                <div class="app-sidebar-user">
                    <i class="bi bi-person-circle"></i>
                    <div class="app-sidebar-text">
                        <strong><?= e($_SESSION['user']['email']) ?></strong>
                        <span><?= e(roleLabel($_SESSION['user']['role'] ?? null)) ?></span>
                    </div>
                </div>

                <nav class="app-sidebar-nav" aria-label="Navigasi utama">
                    <div class="app-sidebar-label">Utama</div>
                    <a href="<?= url('/dashboard') ?>" class="app-sidebar-link" title="Dashboard" data-tour="nav-dashboard">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>

                    <?php if (isRole('admin')): ?>
                        <div class="app-sidebar-label">Admin</div>
                        <a href="<?= url('/admin/product') ?>" class="app-sidebar-link" title="Produk" data-tour="nav-products">
                            <i class="bi bi-box-seam"></i>
                            <span>Produk</span>
                        </a>
                        <a href="<?= url('/admin/product/create') ?>" class="app-sidebar-link" title="Tambah Produk" data-tour="nav-add-product">
                            <i class="bi bi-plus-square"></i>
                            <span>Tambah Produk</span>
                        </a>
                        <a href="<?= url('/admin/user') ?>" class="app-sidebar-link" title="User" data-tour="nav-users">
                            <i class="bi bi-people"></i>
                            <span>User</span>
                        </a>
                        <a href="<?= url('/admin/user/create') ?>" class="app-sidebar-link" title="Tambah User">
                            <i class="bi bi-person-plus"></i>
                            <span>Tambah User</span>
                        </a>
                    <?php endif; ?>

                    <?php if (isRole('kasir')): ?>
                        <div class="app-sidebar-label">Kasir</div>
                        <a href="<?= url('/kasir/transaction') ?>" class="app-sidebar-link" title="Transaksi" data-tour="nav-transaction">
                            <i class="bi bi-cart-check"></i>
                            <span>Transaksi</span>
                        </a>
                        <a href="<?= url('/kasir/product') ?>" class="app-sidebar-link" title="Lihat Produk" data-tour="nav-search-product">
                            <i class="bi bi-search"></i>
                            <span>Lihat Produk</span>
                        </a>
                    <?php endif; ?>

                    <div class="app-sidebar-label">Laporan</div>
                    <a href="<?= url('/report/daily/export') ?>" class="app-sidebar-link" title="Export CSV" data-tour="nav-export">
                        <i class="bi bi-download"></i>
                        <span>Export CSV</span>
                    </a>
                </nav>

                <a class="app-sidebar-logout" href="<?= url('/logout') ?>" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            <?php endif; ?>
        </aside>

        <div class="app-workspace">
            <header class="app-topbar">
                <div>
                    <span class="app-topbar-kicker"><?= e(roleLabel($_SESSION['user']['role'] ?? null)) ?> Panel</span>
                    <strong><?= e($title ?? 'Dashboard') ?></strong>
                </div>
                <div class="app-topbar-actions">
                    <?php if (isAuthenticated()): ?>
                        <button type="button" class="app-tour-start" id="startGuideTour" data-tour="tour-help">
                            <i class="bi bi-question-circle"></i> Guide Tour
                        </button>
                    <?php endif; ?>
                    <span><i class="bi bi-calendar3"></i> <?= date('d M Y') ?></span>
                </div>
            </header>

            <div class="container-fluid app-flash-wrap">
                <?php if ($successMsg = getFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show app-alert" role="alert">
                        <i class="bi bi-check-circle-fill"></i> <?= e($successMsg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errorMsg = getFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($errorMsg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>

            <main class="container-fluid app-main tw-relative">
                <div class="app-content">
                    <?php
                    if (isset($content) && file_exists($content)) {
                        require $content;
                    }
                    ?>
                </div>
            </main>

            <footer class="app-footer">
                <div class="container-fluid px-3 px-lg-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                    <small>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Kelompok 3</small>
                    <small class="text-muted">POS dashboard, transaksi, stok, dan laporan harian.</small>
                </div>
            </footer>
        </div>
    </div>

    <?php if (isAuthenticated()): ?>
        <div class="app-tour" id="appGuideTour" aria-hidden="true">
            <div class="app-tour-backdrop"></div>
            <div class="app-tour-highlight" id="tourHighlight"></div>
            <section class="app-tour-card" id="tourCard" role="dialog" aria-modal="true" aria-labelledby="tourTitle">
                <div class="app-tour-step" id="tourStepCount">Step 1/1</div>
                <h3 id="tourTitle"></h3>
                <p id="tourText"></p>
                <div class="app-tour-actions">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="tourSkip">Skip</button>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" id="tourBack">Back</button>
                        <button type="button" class="btn btn-primary btn-sm" id="tourNext">Next</button>
                    </div>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="<?= asset('js/script.js') ?>"></script>
</body>

</html>
