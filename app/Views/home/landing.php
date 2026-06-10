<?php
$metrics = [
    [
        'value' => '3x',
        'label' => 'Alur lebih ringkas',
    ],
    [
        'value' => '80mm',
        'label' => 'Struk siap print',
    ],
    [
        'value' => 'CSV',
        'label' => 'Laporan harian',
    ],
];

$features = [
    [
        'number' => '01',
        'icon' => 'ti-box-seam',
        'title' => 'Produk & Stok',
        'desc' => 'CRUD produk, stok rendah, dan update stok otomatis setelah checkout.',
        'color' => 'sky',
    ],
    [
        'number' => '02',
        'icon' => 'ti-shopping-cart',
        'title' => 'Keranjang Cepat',
        'desc' => 'Cari produk, edit quantity, quick cash, total, dan kembalian otomatis.',
        'color' => 'emerald',
    ],
    [
        'number' => '03',
        'icon' => 'ti-receipt',
        'title' => 'Struk Print',
        'desc' => 'Struk thermal 80mm yang mudah dicetak setelah transaksi selesai.',
        'color' => 'violet',
    ],
    [
        'number' => '04',
        'icon' => 'ti-chart-bar',
        'title' => 'Laporan Harian',
        'desc' => 'Dashboard, omzet, produk terlaris, uang diterima, dan export CSV.',
        'color' => 'amber',
    ],
];

$workflows = [
    [
        'number' => '1',
        'title' => 'Kelola produk dan stok',
        'desc' => 'Admin update produk sebelum toko mulai ramai.',
        'color' => 'sky',
    ],
    [
        'number' => '2',
        'title' => 'Checkout pelanggan',
        'desc' => 'Kasir pilih produk, input pembayaran, lalu sistem hitung kembalian.',
        'color' => 'emerald',
    ],
    [
        'number' => '3',
        'title' => 'Cetak dan export',
        'desc' => 'Struk siap print dan laporan harian siap diunduh.',
        'color' => 'amber',
    ],
];

$reports = [
    [
        'label' => 'Omzet',
        'value' => 'Rp 420.000',
    ],
    [
        'label' => 'Produk Terjual',
        'value' => '18 pcs',
    ],
    [
        'label' => 'Uang Diterima',
        'value' => 'Rp 500.000',
    ],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($title ?? APP_NAME) ?> - POS Modern</title>

<link href="<?= asset('css/design-system.css') ?>" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

<script>
tailwind.config = {
    corePlugins: {
        preflight: false
    },
    theme: {
        extend: {
            colors: {
                ink: '#172033',
                muted: '#64748B',
                soft: '#F8FAFC',
                surface: '#FFFFFF',
                brand: '#2563EB',
                brandDark: '#1E40AF',
                cyanSoft: '#E0F2FE',
                mintSoft: '#DCFCE7',
                amberSoft: '#FEF3C7',
                violetSoft: '#EDE9FE'
            },
            boxShadow: {
                soft: '0 20px 60px rgba(15, 23, 42, 0.10)',
                panel: '0 16px 45px rgba(15, 23, 42, 0.08)',
                glow: '0 0 0 1px rgba(37, 99, 235, 0.10), 0 24px 80px rgba(37, 99, 235, 0.18)'
            }
        }
    }
}
</script>

<style>
:root {
    --app-bg: #f8fafc;
    --app-bg-2: #eef6ff;
    --app-ink: #172033;
    --app-muted: #64748b;
    --app-surface: #ffffff;
    --app-border: #dbe6f3;
    --app-brand: #2563eb;
    --app-brand-dark: #1e40af;
    --app-brand-soft: #dbeafe;
    --app-mint: #10b981;
    --app-amber: #f59e0b;
    --app-violet: #7c3aed;
}

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    overflow-x: hidden;
    background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34%),
        radial-gradient(circle at 85% 12%, rgba(16, 185, 129, 0.14), transparent 30%),
        linear-gradient(180deg, #f8fafc 0%, #eef6ff 46%, #f8fafc 100%);
    color: var(--app-ink);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.page-bg {
    min-height: 100vh;
}

.loader {
    background:
        radial-gradient(circle at center, rgba(37, 99, 235, 0.08), transparent 42%),
        var(--app-surface);
}

.loader-orbit {
    animation: spin 1.45s linear infinite;
    border: 1px solid rgba(37, 99, 235, 0.18);
    border-top-color: var(--app-brand);
    border-radius: 999px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.hero-shell {
    min-height: calc(100vh - 80px);
}

.hero-title {
    font-size: clamp(3rem, 7vw, 6.6rem);
    letter-spacing: -0.065em;
    line-height: 0.96;
}

.hero-gradient-text {
    background: linear-gradient(135deg, #172033 0%, #2563eb 58%, #10b981 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.nav-glass {
    background: rgba(248, 250, 252, 0.82);
    backdrop-filter: blur(22px);
}

.glass-panel {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(219, 230, 243, 0.92);
    backdrop-filter: blur(24px);
}

.dashboard-preview {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 32px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
    box-shadow: 0 32px 90px rgba(15, 23, 42, 0.14);
}

.dashboard-preview::before {
    position: absolute;
    inset: 0;
    pointer-events: none;
    content: "";
    background:
        radial-gradient(circle at 20% 0%, rgba(37, 99, 235, 0.10), transparent 34%),
        radial-gradient(circle at 100% 30%, rgba(16, 185, 129, 0.10), transparent 28%);
}

.preview-sidebar {
    background: linear-gradient(180deg, #172033 0%, #0f172a 100%);
    border-radius: 24px;
}

.preview-line {
    height: 8px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
}

.metric-pill {
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.76);
    box-shadow: 0 14px 38px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
}

.floating-card {
    animation: floatCard 4.8s ease-in-out infinite;
    background: rgba(255, 255, 255, 0.82);
    backdrop-filter: blur(18px);
}

.floating-card:nth-child(2) {
    animation-delay: -1.4s;
}

@keyframes floatCard {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.feature-card {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 26px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
    transition:
        transform 0.22s ease,
        box-shadow 0.22s ease,
        border-color 0.22s ease;
    backdrop-filter: blur(18px);
}

.feature-card::before {
    position: absolute;
    inset: 0;
    pointer-events: none;
    content: "";
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.07), transparent 48%);
    opacity: 0;
    transition: opacity 0.22s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    border-color: rgba(37, 99, 235, 0.26);
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
}

.feature-card:hover::before {
    opacity: 1;
}

.section-dark {
    background:
        radial-gradient(circle at 12% 10%, rgba(37, 99, 235, 0.38), transparent 34%),
        radial-gradient(circle at 85% 25%, rgba(16, 185, 129, 0.22), transparent 28%),
        linear-gradient(135deg, #0f172a 0%, #172033 52%, #0b1220 100%);
}

.report-panel {
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(22px);
}

@media (max-width: 768px) {
    .hero-shell {
        min-height: auto;
    }

    .dashboard-preview {
        border-radius: 24px;
    }

    .hero-title {
        letter-spacing: -0.055em;
    }
}
</style>
</head>

<body>

<div id="loader" class="loader fixed inset-0 z-50 grid place-items-center">
    <div class="w-[min(420px,calc(100vw-40px))] text-center">
        <div class="relative mx-auto mb-6 grid h-20 w-20 place-items-center rounded-3xl bg-brand text-3xl font-black text-white shadow-glow">
            <div class="loader-orbit absolute h-28 w-28"></div>
            P
        </div>

        <div class="loader-copy">
            <div class="text-sm font-black uppercase tracking-[0.2em] text-brand">
                Preparing workspace
            </div>

            <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] text-ink">
                Loading modern POS experience
            </h1>

            <p class="mt-3 text-sm font-semibold leading-6 text-muted">
                Sinkronisasi produk, stok, transaksi, dan laporan harian.
            </p>
        </div>

        <div class="mt-7 h-2 overflow-hidden rounded-full bg-slate-200">
            <div id="loaderBar" class="h-full w-0 rounded-full bg-brand"></div>
        </div>
    </div>
</div>

<div class="page-bg min-h-screen">

<header class="nav-glass sticky top-0 z-40 border-b border-slate-200/80">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
        <a href="<?= url('/') ?>" class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand text-xl font-black text-white shadow-glow">
                P
            </span>
            <span class="text-xl font-black tracking-[-0.04em] text-ink">
                <?= e(APP_NAME) ?>
            </span>
        </a>

        <div class="flex items-center gap-2">
            <a href="#fitur" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Fitur
            </a>
            <a href="#workflow" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Workflow
            </a>
            <a href="#laporan" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Laporan
            </a>
            <a href="<?= url('/login') ?>" class="btn btn-primary">
                Login
            </a>
        </div>
    </nav>
</header>

<main>

<section class="hero-shell relative overflow-hidden">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[0.94fr_1.06fr] lg:items-center">
        <div class="hero-copy">
            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/70 px-4 py-2 text-sm font-black text-blue-700 shadow-sm backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                POS modern untuk operasional toko harian
            </div>

            <h1 class="hero-title max-w-4xl font-black">
                <span class="hero-gradient-text">Kasir cepat, stok terkendali, laporan siap.</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-muted">
                Satu dashboard formal dan mudah dipakai untuk admin dan kasir: kelola produk, checkout, cetak struk,
                dan export laporan harian tanpa alur yang rumit.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="<?= url('/login') ?>" class="btn btn-primary btn-lg">
                    Masuk Dashboard
                </a>
                <a href="#fitur" class="btn btn-outline btn-lg bg-white/60 backdrop-blur">
                    Lihat Fitur
                </a>
            </div>

            <div class="mt-10 grid max-w-xl grid-cols-3 gap-3">
                <?php foreach ($metrics as $metric): ?>
                    <div class="metric-pill p-4">
                        <strong class="block text-2xl font-black text-ink">
                            <?= e($metric['value']) ?>
                        </strong>
                        <span class="text-xs font-bold text-muted">
                            <?= e($metric['label']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="hero-visual relative">
            <div class="dashboard-preview shadow-soft">
                <div class="relative flex items-center justify-between border-b border-slate-200/90 px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-red-400"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                        <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                    </div>

                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">
                        Live Store
                    </span>
                </div>

                <div class="relative grid gap-4 p-4 md:grid-cols-[180px_1fr]">
                    <aside class="preview-sidebar hidden p-4 md:block">
                        <div class="mb-6 flex items-center gap-2">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-blue-600 text-sm font-black text-white">
                                P
                            </span>
                            <div class="h-3 w-20 rounded-full bg-white/30"></div>
                        </div>

                        <div class="space-y-3">
                            <div class="h-10 rounded-xl bg-white/15"></div>
                            <div class="h-10 rounded-xl bg-white/10"></div>
                            <div class="h-10 rounded-xl bg-white/10"></div>
                            <div class="h-10 rounded-xl bg-white/10"></div>
                        </div>
                    </aside>

                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                                <span class="text-xs font-black uppercase text-blue-700">
                                    Revenue
                                </span>
                                <strong class="mt-2 block text-2xl font-black text-ink">
                                    Rp 420K
                                </strong>
                            </div>

                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                <span class="text-xs font-black uppercase text-emerald-700">
                                    Transaksi
                                </span>
                                <strong class="mt-2 block text-2xl font-black text-ink">
                                    3 trx
                                </strong>
                            </div>

                            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                                <span class="text-xs font-black uppercase text-amber-700">
                                    Low Stock
                                </span>
                                <strong class="mt-2 block text-2xl font-black text-ink">
                                    2 item
                                </strong>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white/78 p-5 shadow-panel backdrop-blur">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-black text-ink">
                                        Transaksi Berjalan
                                    </h3>
                                    <p class="text-sm font-semibold text-muted">
                                        Total dan kembalian dihitung otomatis.
                                    </p>
                                </div>

                                <span class="rounded-xl bg-brand px-3 py-2 text-xs font-black text-white shadow-sm">
                                    Checkout
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-700">
                                    <span>Kopi Arabica x2</span>
                                    <span>Rp 50.000</span>
                                </div>

                                <div class="flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-700">
                                    <span>Susu Segar x1</span>
                                    <span>Rp 12.000</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <span class="text-xs font-black uppercase text-muted">
                                            Paid
                                        </span>
                                        <strong class="block text-xl font-black text-ink">
                                            Rp 100K
                                        </strong>
                                    </div>

                                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                        <span class="text-xs font-black uppercase text-emerald-700">
                                            Change
                                        </span>
                                        <strong class="block text-xl font-black text-emerald-600">
                                            Rp 38K
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="floating-card absolute -right-2 top-12 hidden rounded-2xl border border-slate-200 p-4 shadow-panel lg:block">
                <span class="text-xs font-black uppercase text-muted">
                    Receipt
                </span>
                <strong class="block text-lg font-black text-ink">
                    Ready to print
                </strong>
            </div>

            <div class="floating-card absolute -left-4 bottom-16 hidden rounded-2xl border border-slate-200 p-4 shadow-panel lg:block">
                <span class="text-xs font-black uppercase text-muted">
                    Stock
                </span>
                <strong class="block text-lg font-black text-emerald-600">
                    Auto synced
                </strong>
            </div>
        </div>
    </div>
</section>

<section id="fitur" class="border-y border-slate-200/80 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <div class="mb-10 max-w-3xl">
            <span class="text-sm font-black uppercase tracking-[0.16em] text-brand">
                Fitur utama
            </span>
            <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] text-ink md:text-6xl">
                Lengkap, tapi tetap ringan dipakai setiap hari.
            </h2>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($features as $feature): ?>
                <?php
                    $featureColorMap = [
                        'sky' => 'bg-blue-50 text-blue-700 border-blue-100',
                        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'violet' => 'bg-violet-50 text-violet-700 border-violet-100',
                        'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
                    ];

                    $featureColor = $featureColorMap[$feature['color']] ?? $featureColorMap['sky'];
                ?>

                <article class="feature-card p-6">
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl border <?= e($featureColor) ?>">
                            <i class="ti <?= e($feature['icon']) ?> text-xl"></i>
                        </div>

                        <span class="text-sm font-black text-slate-300">
                            <?= e($feature['number']) ?>
                        </span>
                    </div>

                    <h3 class="relative z-10 text-xl font-black text-ink">
                        <?= e($feature['title']) ?>
                    </h3>

                    <p class="relative z-10 mt-3 text-sm font-semibold leading-6 text-muted">
                        <?= e($feature['desc']) ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="workflow" class="py-20">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
        <div>
            <span class="text-sm font-black uppercase tracking-[0.16em] text-brand">
                Workflow
            </span>

            <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] text-ink md:text-6xl">
                Dari stok sampai struk dalam satu alur.
            </h2>

            <p class="mt-5 text-lg font-semibold leading-8 text-muted">
                Admin menyiapkan produk dan user. Kasir menjalankan transaksi. Sistem merapikan stok,
                pembayaran, kembalian, struk, dan laporan.
            </p>
        </div>

        <div class="grid gap-4">
            <?php foreach ($workflows as $workflow): ?>
                <?php
                    $workflowColorMap = [
                        'sky' => 'bg-blue-50 text-blue-700 border-blue-100',
                        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
                    ];

                    $workflowColor = $workflowColorMap[$workflow['color']] ?? $workflowColorMap['sky'];
                ?>

                <div class="glass-panel rounded-3xl p-5 shadow-panel">
                    <div class="flex items-center gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border font-black <?= e($workflowColor) ?>">
                            <?= e($workflow['number']) ?>
                        </span>

                        <div>
                            <strong class="block font-black text-ink">
                                <?= e($workflow['title']) ?>
                            </strong>

                            <span class="text-sm font-semibold text-muted">
                                <?= e($workflow['desc']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="laporan" class="section-dark py-20 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-2 lg:items-center">
        <div>
            <span class="text-sm font-black uppercase tracking-[0.16em] text-blue-300">
                Report-ready
            </span>

            <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] md:text-6xl">
                Data harian lebih mudah dibaca.
            </h2>

            <p class="mt-5 text-lg font-semibold leading-8 text-slate-300">
                Lihat pendapatan, transaksi, produk terjual, uang diterima, kembalian,
                dan export CSV untuk audit harian.
            </p>
        </div>

        <div class="report-panel rounded-3xl p-5">
            <div class="grid gap-3">
                <?php foreach ($reports as $report): ?>
                    <div class="flex justify-between rounded-2xl bg-white/8 p-4">
                        <span class="font-bold text-slate-300">
                            <?= e($report['label']) ?>
                        </span>
                        <strong>
                            <?= e($report['value']) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>

                <div class="flex justify-between rounded-2xl bg-blue-500 p-4 shadow-glow">
                    <span class="font-bold">
                        Export CSV
                    </span>
                    <strong>
                        Ready
                    </strong>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<footer class="border-t border-slate-200/80 py-8">
    <div class="mx-auto flex max-w-7xl flex-col justify-between gap-3 px-5 text-sm font-semibold text-muted sm:flex-row">
        <span>
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Kelompok 3.
        </span>

        <a href="<?= url('/login') ?>" class="font-black text-ink">
            Masuk ke aplikasi
        </a>
    </div>
</footer>

</div>

<script>
window.addEventListener('load', function () {
    const loader = document.getElementById('loader');

    if (typeof gsap === 'undefined') {
        if (loader) {
            loader.style.display = 'none';
        }
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    const loaderTl = gsap.timeline({
        defaults: {
            ease: 'power3.out'
        },
        onComplete: function () {
            if (loader) {
                loader.style.display = 'none';
            }
        }
    });

    loaderTl
        .from('.loader-copy > *', {
            y: 18,
            opacity: 0,
            duration: 0.45,
            stagger: 0.08
        })
        .to('#loaderBar', {
            width: '100%',
            duration: 0.8,
            ease: 'power2.inOut'
        }, '-=0.1')
        .to('#loader', {
            opacity: 0,
            y: -18,
            duration: 0.55,
            ease: 'power2.inOut'
        });

    gsap.from('.hero-copy > *', {
        y: 24,
        opacity: 0,
        duration: 0.75,
        stagger: 0.08,
        delay: 1.25,
        ease: 'power3.out'
    });

    gsap.from('.dashboard-preview', {
        y: 34,
        opacity: 0,
        scale: 0.97,
        duration: 0.85,
        delay: 1.35,
        ease: 'power3.out'
    });

    gsap.from('.feature-card', {
        scrollTrigger: {
            trigger: '#fitur',
            start: 'top 75%'
        },
        y: 28,
        opacity: 0,
        duration: 0.6,
        stagger: 0.08,
        ease: 'power3.out'
    });

    gsap.from('#workflow .glass-panel', {
        scrollTrigger: {
            trigger: '#workflow',
            start: 'top 75%'
        },
        x: 26,
        opacity: 0,
        duration: 0.58,
        stagger: 0.08,
        ease: 'power3.out'
    });
});
</script>

</body>
</html>