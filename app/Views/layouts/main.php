<?php
$pageTitle = $title ?? 'Dashboard';

$userId = $_SESSION['user_id'] ?? 'guest';
$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['name'] ?? 'Guest';

$currentRole = currentRole();
$isCashier = isRole(ROLE_KASIR);

$brandUrl = $isCashier ? '/kasir/transaction' : '/dashboard';
$userInitial = strtoupper(substr($userName, 0, 2));

$menus = [
    ROLE_SUPER_ADMIN => [
        ['Platform Overview', '/superadmin/dashboard', 'ti-layout-dashboard', 'nav-dashboard'],
        ['Toko', '/superadmin/stores', 'ti-building-store', 'nav-stores'],
        ['User Management', '/admin/user', 'ti-users', 'nav-users'],
        ['Laporan Lintas Toko', '/superadmin/reports', 'ti-chart-bar', 'nav-reports'],
        ['Audit Log', '/superadmin/audit', 'ti-shield-check', 'nav-audit'],
        ['Settings', '/settings', 'ti-settings', 'nav-settings'],
    ],
    ROLE_ADMIN => [
        ['Dashboard', '/dashboard', 'ti-home', 'nav-dashboard'],
        ['User Management', '/admin/user', 'ti-users', 'nav-users'],
        ['Product Management', '/admin/product', 'ti-package', 'nav-products'],
        ['Inventory / Stock', '/inventory', 'ti-packages', 'nav-inventory'],
        ['Expenses', '/admin/expense', 'ti-receipt-tax', 'nav-expenses'],
        ['Reports', '/reports', 'ti-chart-bar', 'nav-reports'],
    ],
    ROLE_KASIR => [
        ['POS / Cashier', '/kasir/transaction', 'ti-cash-register', 'nav-transaction'],
        ['My Transactions', '/kasir/my-transactions', 'ti-receipt', 'nav-my-transactions'],
        ['My Shift', '/kasir/shift', 'ti-clock', 'nav-shift'],
    ],
];

$currentMenus = $menus[$currentRole] ?? [];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>

<link href="<?= asset('css/design-system.css') ?>" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">

<script>
(function () {
    try {
        if (localStorage.getItem('posSidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    } catch (error) {}
})();
</script>

<style>
:root {
    --theme-bg: #f8fafc;
    --theme-bg-soft: #eef6ff;
    --theme-surface: #ffffff;
    --theme-glass: rgba(255, 255, 255, 0.78);
    --theme-border: #dbe6f3;

    --theme-ink: #172033;
    --theme-muted: #64748b;

    --theme-brand: #2563eb;
    --theme-brand-dark: #1e40af;
    --theme-brand-soft: #dbeafe;

    --theme-mint: #10b981;
    --theme-mint-dark: #047857;
    --theme-mint-soft: #dcfce7;

    --theme-amber: #f59e0b;
    --theme-amber-soft: #fef3c7;

    --theme-danger: #ef4444;
    --theme-danger-soft: #fee2e2;

    --theme-sidebar: #0f172a;
    --theme-sidebar-soft: #172033;

    --theme-shadow: 0 22px 70px rgba(15, 23, 42, 0.08);
    --theme-shadow-soft: 0 14px 42px rgba(15, 23, 42, 0.06);
}

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body.app-body {
    min-height: 100vh;
    margin: 0;
    overflow-x: hidden;
    background:
        radial-gradient(circle at 8% 0%, rgba(37, 99, 235, 0.15), transparent 34%),
        radial-gradient(circle at 92% 8%, rgba(16, 185, 129, 0.13), transparent 30%),
        linear-gradient(180deg, var(--theme-bg) 0%, var(--theme-bg-soft) 44%, var(--theme-bg) 100%);
    color: var(--theme-ink);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

/* ============================================================
   APP SHELL
   ============================================================ */

.app-shell {
    display: flex;
    min-height: 100vh;
}

.app-workspace {
    display: flex;
    flex: 1;
    min-width: 0;
    min-height: 100vh;
    flex-direction: column;
}

/* ============================================================
   SIDEBAR
   ============================================================ */

.app-sidebar {
    position: sticky;
    top: 0;
    z-index: 50;
    display: flex;
    width: 280px;
    height: 100vh;
    flex-direction: column;
    flex-shrink: 0;
    padding: 18px;
    overflow: hidden;
    background:
        radial-gradient(circle at 22% 0%, rgba(37, 99, 235, 0.34), transparent 34%),
        radial-gradient(circle at 100% 20%, rgba(16, 185, 129, 0.18), transparent 30%),
        linear-gradient(180deg, var(--theme-sidebar-soft) 0%, var(--theme-sidebar) 100%);
    color: #ffffff;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    transition: width 220ms ease, padding 220ms ease;
}

.app-sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 20px;
}

.app-sidebar-brand {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 12px;
    color: #ffffff;
    text-decoration: none;
}

.app-sidebar-brand:hover {
    color: #ffffff;
}

.app-brand-mark {
    display: grid;
    width: 46px;
    height: 46px;
    flex-shrink: 0;
    place-items: center;
    border-radius: 18px;
    background: linear-gradient(135deg, var(--theme-brand), var(--theme-mint));
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 950;
    box-shadow: 0 18px 50px rgba(37, 99, 235, 0.34);
}

.app-sidebar-text {
    min-width: 0;
    overflow: hidden;
    color: inherit;
    font-weight: 900;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.app-sidebar-toggle {
    appearance: none;
    -webkit-appearance: none;
    display: grid !important;
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    place-items: center;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.10) !important;
    color: rgba(255, 255, 255, 0.88) !important;
    cursor: pointer !important;
    pointer-events: auto !important;
    transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
    outline: none;
}

.app-sidebar-toggle i {
    display: block;
    color: inherit !important;
    font-size: 1.24rem;
    line-height: 1;
    pointer-events: none;
}

.app-sidebar-toggle:hover {
    background: rgba(37, 99, 235, 0.42) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.22) !important;
}

.app-sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    margin-bottom: 18px;
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(18px);
}

.avatar {
    display: grid;
    flex-shrink: 0;
    place-items: center;
    border-radius: 999px;
    font-weight: 950;
}

.avatar-md {
    width: 42px;
    height: 42px;
}

.avatar-green {
    background: var(--theme-mint-soft);
    color: var(--theme-mint-dark);
}

.app-sidebar-user strong {
    display: block;
    max-width: 176px;
    overflow: hidden;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 900;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.app-sidebar-user span {
    display: block;
    margin-top: 3px;
    color: rgba(255, 255, 255, 0.64);
    font-size: 0.75rem;
    font-weight: 700;
}

.app-sidebar-nav {
    display: grid;
    gap: 6px;
    padding-bottom: 12px;
    overflow-y: auto;
    overflow-x: hidden;
}

.app-sidebar-nav::-webkit-scrollbar {
    width: 6px;
}

.app-sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.18);
    border-radius: 999px;
}

.app-sidebar-label {
    margin: 14px 10px 6px;
    color: rgba(255, 255, 255, 0.42);
    font-size: 0.7rem;
    font-weight: 950;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.app-sidebar-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 46px;
    padding: 0 13px;
    border: 1px solid transparent;
    border-radius: 16px;
    background: transparent;
    color: rgba(255, 255, 255, 0.72) !important;
    text-decoration: none;
    font-size: 0.92rem;
    font-weight: 800;
    transition:
        background 0.18s ease,
        color 0.18s ease,
        transform 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease;
}

.app-sidebar-link i {
    color: inherit !important;
}

.app-sidebar-link:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.10);
}

.app-sidebar-link.is-active,
.app-sidebar-link.active,
.app-sidebar-link[aria-current="page"] {
    background: rgba(37, 99, 235, 0.42) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.08),
        0 14px 34px rgba(37, 99, 235, 0.20);
}

.app-sidebar-link.is-active::before,
.app-sidebar-link.active::before,
.app-sidebar-link[aria-current="page"]::before {
    content: "";
    position: absolute;
    left: 7px;
    top: 50%;
    width: 4px;
    height: 22px;
    border-radius: 999px;
    background: var(--theme-mint);
    transform: translateY(-50%);
}

.nav-item__icon {
    width: 22px;
    flex-shrink: 0;
    font-size: 1.18rem;
    text-align: center;
}

.app-sidebar-logout {
    display: flex !important;
    align-items: center;
    gap: 12px;
    min-height: 46px;
    padding: 0 13px;
    margin-top: auto;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.08) !important;
    color: rgba(255, 255, 255, 0.84) !important;
    text-decoration: none;
    font-weight: 850;
    transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
}

.app-sidebar-logout-form {
    width: 100%;
    margin-top: auto;
}

.app-sidebar-logout-form .app-sidebar-logout {
    width: 100%;
    margin-top: 0;
    cursor: pointer;
}

.app-sidebar-logout i,
.app-sidebar-logout span {
    color: inherit !important;
}

.app-sidebar-logout:hover {
    background: rgba(239, 68, 68, 0.24) !important;
    color: #ffffff !important;
    border-color: rgba(239, 68, 68, 0.36) !important;
}

/* ============================================================
   COLLAPSED STATE
   ============================================================ */

html.sidebar-collapsed .app-sidebar {
    width: 94px !important;
    padding-inline: 14px !important;
}

html.sidebar-collapsed .app-sidebar-text,
html.sidebar-collapsed .app-sidebar-user .app-sidebar-text,
html.sidebar-collapsed .app-sidebar-link span,
html.sidebar-collapsed .app-sidebar-label,
html.sidebar-collapsed .app-sidebar-logout span {
    display: none !important;
}

html.sidebar-collapsed .app-sidebar-header {
    justify-content: center !important;
}

html.sidebar-collapsed .app-sidebar-brand,
html.sidebar-collapsed .app-sidebar-link,
html.sidebar-collapsed .app-sidebar-logout {
    justify-content: center !important;
}

html.sidebar-collapsed .app-sidebar-user {
    justify-content: center !important;
    padding-inline: 10px !important;
}

html.sidebar-collapsed .app-sidebar-toggle {
    position: static !important;
}

html.sidebar-collapsed .app-sidebar-link.is-active::before,
html.sidebar-collapsed .app-sidebar-link.active::before,
html.sidebar-collapsed .app-sidebar-link[aria-current="page"]::before {
    left: 5px;
    height: 20px;
}

/* ============================================================
   TOPBAR
   ============================================================ */

.app-topbar {
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    min-height: 78px;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 30px;
    border-bottom: 1px solid rgba(219, 230, 243, 0.88);
    background: rgba(248, 250, 252, 0.84);
    backdrop-filter: blur(22px);
}

.app-topbar-kicker {
    display: block;
    margin-bottom: 4px;
    color: var(--theme-brand-dark);
    font-size: 0.72rem;
    font-weight: 950;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.app-topbar strong {
    display: block;
    color: var(--theme-ink);
    font-size: 1.25rem;
    font-weight: 950;
    letter-spacing: -0.035em;
}

.app-topbar-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.app-topbar-actions span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 14px;
    border: 1px solid var(--theme-border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--theme-muted);
    font-size: 0.86rem;
    font-weight: 850;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
}

/* ============================================================
   FLASH, MAIN, FOOTER
   ============================================================ */

.app-flash-wrap {
    padding: 18px 30px 0;
}

.app-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 980px;
    padding: 14px 16px;
    border-radius: 18px;
    font-weight: 800;
    box-shadow: var(--theme-shadow-soft);
}

.toast-success {
    border: 1px solid rgba(16, 185, 129, 0.18);
    background: rgba(220, 252, 231, 0.94);
    color: var(--theme-mint-dark);
}

.toast-error {
    border: 1px solid rgba(239, 68, 68, 0.18);
    background: rgba(254, 226, 226, 0.94);
    color: #b91c1c;
}

.app-main {
    flex: 1;
    padding: 26px 30px 36px;
}

.app-content {
    width: 100%;
    max-width: 1440px;
    margin: 0 auto;
}

.app-sidebar,
.app-topbar,
.app-content {
    opacity: 1 !important;
}

.app-footer {
    padding: 18px 30px;
    border-top: 1px solid rgba(219, 230, 243, 0.88);
    background: rgba(255, 255, 255, 0.50);
    color: var(--theme-muted);
    backdrop-filter: blur(16px);
}

.app-footer .container-fluid {
    width: 100%;
    max-width: 1440px;
    margin: 0 auto;
    font-size: 0.84rem;
    font-weight: 700;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 991.98px) {
    .app-shell {
        flex-direction: column;
    }

    .app-sidebar {
        position: relative;
        width: 100%;
        height: auto;
        min-height: auto;
        border-right: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .app-sidebar-toggle {
        display: none !important;
    }

    .app-sidebar-nav {
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    }

    .app-sidebar-logout {
        margin-top: 16px;
    }

    html.sidebar-collapsed .app-sidebar {
        width: 100% !important;
    }

    html.sidebar-collapsed .app-sidebar-text,
    html.sidebar-collapsed .app-sidebar-user .app-sidebar-text,
    html.sidebar-collapsed .app-sidebar-link span,
    html.sidebar-collapsed .app-sidebar-label,
    html.sidebar-collapsed .app-sidebar-logout span {
        display: inline !important;
    }
}

@media (max-width: 575.98px) {
    .app-topbar {
        align-items: flex-start;
        flex-direction: column;
        padding: 16px;
    }

    .app-topbar-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .app-topbar-actions span {
        width: 100%;
        justify-content: center;
    }

    .app-flash-wrap,
    .app-main,
    .app-footer {
        padding-inline: 16px;
    }

    .app-sidebar {
        padding: 16px;
    }

    .app-sidebar-nav {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body
    class="app-body role-<?= e(str_replace('_', '-', $currentRole)) ?>"
    data-user-id="<?= e((string) $userId) ?>"
    data-user-role="<?= e($userRole) ?>"
>
<div class="app-shell">

    <aside class="app-sidebar">
        <div class="app-sidebar-header">
            <a
                class="app-sidebar-brand"
                href="<?= url($brandUrl) ?>"
                title="<?= e(APP_NAME) ?>"
            >
                <span class="app-brand-mark">P</span>
                <span class="app-sidebar-text"><?= e(APP_NAME) ?></span>
            </a>

            <button
                type="button"
                class="app-sidebar-toggle"
                id="sidebarToggle"
                data-tour="sidebar-toggle"
                aria-label="Minimize sidebar"
                aria-expanded="true"
                title="Minimize sidebar"
            >
                <i class="ti ti-layout-sidebar-left-collapse" aria-hidden="true"></i>
            </button>
        </div>

        <?php if (isAuthenticated()): ?>
            <div class="app-sidebar-user">
                <div class="avatar avatar-md avatar-green">
                    <?= e($userInitial) ?>
                </div>

                <div class="app-sidebar-text">
                    <strong><?= e($userName) ?></strong>
                    <span><?= e(roleLabel($userRole)) ?></span>
                </div>
            </div>

            <nav class="app-sidebar-nav" aria-label="Navigasi utama">
                <div class="app-sidebar-label">Menu</div>

                <?php foreach ($currentMenus as [$label, $path, $icon, $tour]): ?>
                    <?php
                    $itemUrl = url($path);
                    $itemPath = parse_url($itemUrl, PHP_URL_PATH) ?: $path;
                    $isActive = rtrim($currentPath, '/') === rtrim($itemPath, '/');
                    ?>

                    <a
                        href="<?= $itemUrl ?>"
                        class="nav-item app-sidebar-link <?= $isActive ? 'is-active' : '' ?>"
                        title="<?= e($label) ?>"
                        data-tour="<?= e($tour) ?>"
                        <?= $isActive ? 'aria-current="page"' : '' ?>
                    >
                        <i class="ti <?= e($icon) ?> nav-item__icon" aria-hidden="true"></i>
                        <span><?= e($label) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form class="app-sidebar-logout-form" action="<?= url('/logout') ?>" method="POST">
            <?= csrf_field() ?>
            <button class="app-sidebar-logout" type="submit" title="Logout">
                <i class="ti ti-logout" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
            </form>
        <?php endif; ?>
    </aside>

    <div class="app-workspace">
        <header class="app-topbar">
            <div>
                <span class="app-topbar-kicker">
                    <?= e(roleLabel($userRole)) ?> Panel
                </span>

                <strong><?= e($pageTitle) ?></strong>
            </div>

            <div class="app-topbar-actions">
                <?php if (isAuthenticated()): ?>
                    <button
                        type="button"
                        class="app-tour-start"
                        id="startGuideTour"
                        data-tour="tour-help"
                        aria-label="Mulai panduan menu"
                    >
                        <i class="ti ti-route" aria-hidden="true"></i>
                        Guide Tour
                    </button>
                <?php endif; ?>

                <span>
                    <i class="ti ti-calendar" aria-hidden="true"></i>
                    <?= date('d M Y') ?>
                </span>
            </div>
        </header>

        <div class="container-fluid app-flash-wrap">
            <?php if ($successMsg = getFlash('success')): ?>
                <div class="toast toast-success app-alert" role="alert">
                    <i class="ti ti-check" aria-hidden="true"></i>
                    <?= e($successMsg) ?>
                    <button
                        type="button"
                        class="btn btn-ghost btn-icon btn-sm"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    >
                        <i class="ti ti-x" aria-hidden="true"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg = getFlash('error')): ?>
                <div class="toast toast-error app-alert" role="alert">
                    <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                    <?= e($errorMsg) ?>
                    <button
                        type="button"
                        class="btn btn-ghost btn-icon btn-sm"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    >
                        <i class="ti ti-x" aria-hidden="true"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <main class="container-fluid app-main">
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
                <small>
                    &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Kelompok 3
                </small>

                <small class="text-muted">
                    POS dashboard, transaksi, stok, dan laporan harian.
                </small>
            </div>
        </footer>
    </div>
</div>

<?php if (isAuthenticated()): ?>
    <div class="app-tour" id="appGuideTour" aria-hidden="true">
        <div class="app-tour-backdrop"></div>
        <div class="app-tour-highlight" id="tourHighlight" aria-hidden="true"></div>

        <section
            class="app-tour-card"
            id="tourCard"
            role="dialog"
            aria-modal="true"
            aria-labelledby="tourTitle"
            aria-describedby="tourText"
        >
            <div class="app-tour-step" id="tourStepCount">Langkah 1 dari 1</div>
            <h3 id="tourTitle">Panduan Penggunaan</h3>
            <p id="tourText">Ikuti langkah penggunaan aplikasi melalui menu yang disorot.</p>

            <div class="app-tour-actions">
                <button type="button" class="btn btn-ghost btn-sm" id="tourSkip">
                    Lewati
                </button>

                <div>
                    <button type="button" class="btn btn-secondary btn-sm" id="tourBack">
                        Kembali
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="tourNext">
                        Berikutnya
                    </button>
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
