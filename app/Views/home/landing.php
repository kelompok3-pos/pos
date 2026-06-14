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
        'value' => 'Excel',
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
        'desc' => 'Dashboard, omzet, produk terlaris, uang diterima, dan export Excel.',
        'color' => 'amber',
    ],
];

$workflows = [
    [
        'number' => '1',
        'title' => 'Kelola produk dan stok',
        'desc' => 'Admin update produk, harga, dan batas stok sebelum toko mulai ramai.',
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
        'desc' => 'Struk siap print dan laporan harian siap diunduh untuk audit.',
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

$featureColorMap = [
    'sky' => 'bg-blue-50 text-blue-700 border-blue-100',
    'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
    'violet' => 'bg-violet-50 text-violet-700 border-violet-100',
    'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
];

$workflowColorMap = [
    'sky' => 'bg-blue-50 text-blue-700 border-blue-100',
    'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
    'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
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
                ink: '#111827',
                muted: '#6B7280',
                soft: '#F8FAFC',
                surface: '#FFFFFF',
                brand: '#378ADD',
                brandDark: '#185FA5',
                brandSoft: '#E6F1FB',
                mint: '#1D9E75',
                mintSoft: '#EAF7EF',
                amber: '#BA7517',
                amberSoft: '#FEF3C7',
                violet: '#8B5CF6',
                violetSoft: '#EDE9FE',
                danger: '#E24B4A'
            },
            boxShadow: {
                soft: '0 22px 70px rgba(15, 23, 42, 0.10)',
                panel: '0 16px 45px rgba(15, 23, 42, 0.08)',
                glow: '0 0 0 1px rgba(55, 138, 221, 0.12), 0 24px 80px rgba(55, 138, 221, 0.20)'
            }
        }
    }
}
</script>

<style>
:root {
    --lp-bg: #f8fafc;
    --lp-bg-2: #eef6ff;
    --lp-ink: #111827;
    --lp-muted: #6b7280;
    --lp-surface: #ffffff;
    --lp-border: #dbe6f3;
    --lp-brand: #378add;
    --lp-brand-dark: #185fa5;
    --lp-brand-soft: #e6f1fb;
    --lp-mint: #1d9e75;
    --lp-mint-soft: #eaf7ef;
    --lp-amber: #ba7517;
    --lp-violet: #8b5cf6;
    --lp-danger: #e24b4a;
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
        radial-gradient(circle at 12% 8%, rgba(37, 99, 235, 0.12), transparent 30rem),
        radial-gradient(circle at 92% 12%, rgba(16, 185, 129, 0.12), transparent 28rem),
        linear-gradient(180deg, #ffffff 0%, #f8fafc 55%, #eef4ff 100%);
    color: var(--lp-ink);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

a {
    text-decoration: none;
}

.lp-page {
    min-height: 100vh;
}

.lp-noise {
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background-image:
        linear-gradient(rgba(15, 23, 42, 0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(15, 23, 42, 0.035) 1px, transparent 1px);
    background-size: 32px 32px;
}

.lp-loader {
    background:
        radial-gradient(circle at 50% 24%, rgba(55, 138, 221, 0.10), transparent 42%),
        #ffffff;
}

.lp-loader-orbit {
    animation: lpSpin 1.45s linear infinite;
    border: 1px solid rgba(55, 138, 221, 0.18);
    border-top-color: var(--lp-brand);
    border-radius: 999px;
}

@keyframes lpSpin {
    to {
        transform: rotate(360deg);
    }
}

.lp-nav {
    background: rgba(248, 250, 252, 0.82);
    backdrop-filter: blur(22px);
    transition: background-color .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.lp-nav.is-scrolled {
    background: rgba(255, 255, 255, 0.88);
    box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06);
}

.lp-logo-mark {
    position: relative;
    overflow: hidden;
}

.lp-logo-mark::after {
    position: absolute;
    inset: -60%;
    content: "";
    background: linear-gradient(120deg, transparent, rgba(255,255,255,.45), transparent);
    transform: translateX(-120%) rotate(20deg);
}

.lp-logo-mark:hover::after {
    animation: lpShine .8s ease;
}

@keyframes lpShine {
    to {
        transform: translateX(120%) rotate(20deg);
    }
}

.lp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 44px;
    padding: .72rem 1rem;
    border-radius: .9rem;
    font-size: .9rem;
    font-weight: 850;
    line-height: 1;
    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease, color .18s ease;
}

.lp-btn-primary {
    border: 0;
    background: var(--lp-brand);
    color: #fff;
    box-shadow: 0 10px 24px rgba(55, 138, 221, .22);
}

.lp-btn-primary:hover {
    background: #2f7bc6;
    color: #fff;
    box-shadow: 0 14px 32px rgba(55, 138, 221, .28);
}

.lp-btn-soft {
    border: 1px solid rgba(55, 138, 221, .22);
    background: rgba(255, 255, 255, .68);
    color: var(--lp-brand-dark);
    backdrop-filter: blur(16px);
}

.lp-btn-soft:hover {
    background: rgba(55, 138, 221, .10);
    color: var(--lp-brand-dark);
}

.lp-hero {
    min-height: calc(100vh - 80px);
}

.lp-hero-title {
    font-size: clamp(3rem, 7vw, 6.65rem);
    letter-spacing: -0.068em;
    line-height: 0.94;
}

.lp-gradient-text {
    background: linear-gradient(135deg, #111827 0%, #378add 58%, #1d9e75 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.lp-eyebrow {
    border: 1px solid rgba(55, 138, 221, 0.18);
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(18px);
}

.lp-metric-pill {
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 1.15rem;
    background: rgba(255, 255, 255, 0.75);
    box-shadow: 0 14px 38px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
}

.lp-dashboard-preview {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 2rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
    box-shadow: 0 32px 90px rgba(15, 23, 42, 0.14);
    transform-style: preserve-3d;
}

.lp-dashboard-preview::before {
    position: absolute;
    inset: 0;
    pointer-events: none;
    content: "";
    background:
        radial-gradient(circle at 20% 0%, rgba(55, 138, 221, 0.12), transparent 34%),
        radial-gradient(circle at 100% 30%, rgba(29, 158, 117, 0.11), transparent 28%);
}

.lp-preview-sidebar {
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
    border-radius: 1.5rem;
}

.lp-floating-card {
    background: rgba(255, 255, 255, 0.84);
    backdrop-filter: blur(18px);
    will-change: transform;
}

.lp-feature-card,
.lp-workflow-card {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(219, 230, 243, 0.95);
    border-radius: 1.6rem;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
    backdrop-filter: blur(18px);
    transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
}

.lp-feature-card::before,
.lp-workflow-card::before {
    position: absolute;
    inset: 0;
    pointer-events: none;
    content: "";
    background: linear-gradient(135deg, rgba(55, 138, 221, 0.08), transparent 48%);
    opacity: 0;
    transition: opacity .22s ease;
}

.lp-feature-card:hover,
.lp-workflow-card:hover {
    border-color: rgba(55, 138, 221, 0.26);
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
    background: rgba(255, 255, 255, .9);
}

.lp-feature-card:hover::before,
.lp-workflow-card:hover::before {
    opacity: 1;
}

.lp-dark-section {
    background:
        radial-gradient(circle at 12% 10%, rgba(55, 138, 221, 0.40), transparent 34%),
        radial-gradient(circle at 85% 25%, rgba(29, 158, 117, 0.24), transparent 28%),
        linear-gradient(135deg, #0f172a 0%, #111827 52%, #0b1220 100%);
}

.lp-report-panel {
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.07);
    backdrop-filter: blur(22px);
}

.lp-report-row {
    background: rgba(255, 255, 255, 0.08);
}

.lp-orb {
    position: absolute;
    border-radius: 999px;
    pointer-events: none;
    filter: blur(2px);
    opacity: .72;
}

.lp-orb-one {
    width: 18rem;
    height: 18rem;
    top: 10%;
    left: -7rem;
    background: rgba(55, 138, 221, .14);
}

.lp-orb-two {
    width: 14rem;
    height: 14rem;
    right: -4rem;
    top: 18%;
    background: rgba(29, 158, 117, .12);
}


@media (max-width: 768px) {
    .lp-hero {
        min-height: auto;
    }

    .lp-dashboard-preview {
        border-radius: 1.5rem;
    }

    .lp-hero-title {
        letter-spacing: -0.055em;
    }
}

@media (prefers-reduced-motion: reduce) {
    html {
        scroll-behavior: auto;
    }

    *,
    *::before,
    *::after {
        animation-duration: .001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .001ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
</head>

<body>

<div id="loader" class="lp-loader fixed inset-0 z-50 grid place-items-center">
    <div class="w-[min(420px,calc(100vw-40px))] text-center">
        <div class="relative mx-auto mb-6 grid h-20 w-20 place-items-center rounded-3xl bg-brand text-3xl font-black text-white shadow-glow lp-loader-mark">
            <div class="lp-loader-orbit absolute h-28 w-28"></div>
            P
        </div>

        <div class="lp-loader-copy">
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

<div class="lp-page relative min-h-screen">
<div class="lp-noise"></div>

<header id="mainNav" class="lp-nav sticky top-0 z-40 border-b border-slate-200/80">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
        <a href="<?= url('/') ?>" class="flex items-center gap-3">
            <span class="lp-logo-mark grid h-11 w-11 place-items-center rounded-2xl bg-brand text-xl font-black text-white shadow-glow">
                P
            </span>
            <span class="text-xl font-black tracking-[-0.04em] text-ink">
                <?= e(APP_NAME) ?>
            </span>
        </a>

        <div class="flex items-center gap-2">
            <a href="#fitur" class="nav-link hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Fitur
            </a>
            <a href="#workflow" class="nav-link hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Workflow
            </a>
            <a href="#laporan" class="nav-link hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-white/70 sm:inline-flex">
                Laporan
            </a>
            <a href="<?= url('/login') ?>" class="lp-btn lp-btn-primary">
                Login
            </a>
        </div>
    </nav>
</header>

<main>

<section class="lp-hero relative overflow-hidden">
    <div class="lp-orb lp-orb-one"></div>
    <div class="lp-orb lp-orb-two"></div>

    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[0.94fr_1.06fr] lg:items-center">
        <div class="lp-hero-copy">
            <div class="lp-eyebrow mb-5 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-black text-blue-700 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                POS modern untuk operasional toko harian
            </div>

            <h1 class="lp-hero-title max-w-4xl font-black">
                <span class="lp-gradient-text">Kasir cepat, stok terkendali, laporan siap.</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-muted">
                Satu dashboard formal dan mudah dipakai untuk admin dan kasir: kelola produk, checkout, cetak struk,
                dan export laporan harian tanpa alur yang rumit.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="<?= url('/login') ?>" class="lp-btn lp-btn-primary">
                    <i class="ti ti-login"></i>
                    Masuk Dashboard
                </a>
                <a href="#fitur" class="lp-btn lp-btn-soft">
                    <i class="ti ti-sparkles"></i>
                    Lihat Fitur
                </a>
            </div>

            <div class="mt-10 grid max-w-xl grid-cols-3 gap-3">
                <?php foreach ($metrics as $metric): ?>
                    <div class="lp-metric-pill p-4">
                        <strong class="metric-value block text-2xl font-black text-ink">
                            <?= e($metric['value']) ?>
                        </strong>
                        <span class="text-xs font-bold text-muted">
                            <?= e($metric['label']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lp-hero-visual relative">
            <div class="lp-dashboard-preview shadow-soft" id="dashboardPreview">
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
                    <aside class="lp-preview-sidebar hidden p-4 md:block">
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
                            <div class="preview-stat rounded-2xl border border-blue-100 bg-blue-50 p-4">
                                <span class="text-xs font-black uppercase text-blue-700">Revenue</span>
                                <strong class="mt-2 block text-2xl font-black text-ink">Rp 420K</strong>
                            </div>

                            <div class="preview-stat rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                <span class="text-xs font-black uppercase text-emerald-700">Transaksi</span>
                                <strong class="mt-2 block text-2xl font-black text-ink">3 trx</strong>
                            </div>

                            <div class="preview-stat rounded-2xl border border-amber-100 bg-amber-50 p-4">
                                <span class="text-xs font-black uppercase text-amber-700">Low Stock</span>
                                <strong class="mt-2 block text-2xl font-black text-ink">2 item</strong>
                            </div>
                        </div>

                        <div class="preview-checkout rounded-3xl border border-slate-200 bg-white/78 p-5 shadow-panel backdrop-blur">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-black text-ink">Transaksi Berjalan</h3>
                                    <p class="text-sm font-semibold text-muted">Total dan kembalian dihitung otomatis.</p>
                                </div>

                                <span class="rounded-xl bg-brand px-3 py-2 text-xs font-black text-white shadow-sm">
                                    Checkout
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="preview-line flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-700">
                                    <span>Kopi Arabica x2</span>
                                    <span>Rp 50.000</span>
                                </div>

                                <div class="preview-line flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-700">
                                    <span>Susu Segar x1</span>
                                    <span>Rp 12.000</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div class="preview-box rounded-2xl border border-slate-200 bg-white p-4">
                                        <span class="text-xs font-black uppercase text-muted">Paid</span>
                                        <strong class="block text-xl font-black text-ink">Rp 100K</strong>
                                    </div>

                                    <div class="preview-box rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                        <span class="text-xs font-black uppercase text-emerald-700">Change</span>
                                        <strong class="block text-xl font-black text-emerald-600">Rp 38K</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-floating-card absolute -right-2 top-12 hidden rounded-2xl border border-slate-200 p-4 shadow-panel lg:block">
                <span class="text-xs font-black uppercase text-muted">Receipt</span>
                <strong class="block text-lg font-black text-ink">Ready to print</strong>
            </div>

            <div class="lp-floating-card absolute -left-4 bottom-16 hidden rounded-2xl border border-slate-200 p-4 shadow-panel lg:block">
                <span class="text-xs font-black uppercase text-muted">Stock</span>
                <strong class="block text-lg font-black text-emerald-600">Auto synced</strong>
            </div>
        </div>
    </div>
</section>

<section id="fitur" class="border-b border-slate-200/80 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <div class="section-heading mb-10 max-w-3xl">
            <span class="text-sm font-black uppercase tracking-[0.16em] text-brand">
                Fitur utama
            </span>
            <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] text-ink md:text-6xl">
                Lengkap, tapi tetap ringan dipakai setiap hari.
            </h2>
            <p class="mt-4 text-base font-semibold leading-7 text-muted">
                Desainnya dibuat untuk alur toko kecil sampai menengah: jelas, cepat, dan minim distraksi.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($features as $feature): ?>
                <?php $featureColor = $featureColorMap[$feature['color']] ?? $featureColorMap['sky']; ?>

                <article class="lp-feature-card p-6">
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
        <div class="section-heading">
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
                <?php $workflowColor = $workflowColorMap[$workflow['color']] ?? $workflowColorMap['sky']; ?>

                <div class="lp-workflow-card p-5">
                    <div class="relative z-10 flex items-center gap-4">
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

<section id="laporan" class="lp-dark-section py-20 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-2 lg:items-center">
        <div class="section-heading">
            <span class="text-sm font-black uppercase tracking-[0.16em] text-blue-300">
                Report-ready
            </span>

            <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] md:text-6xl">
                Data harian lebih mudah dibaca.
            </h2>

            <p class="mt-5 text-lg font-semibold leading-8 text-slate-300">
                Lihat pendapatan, transaksi, produk terjual, uang diterima, kembalian,
                dan export Excel untuk audit harian.
            </p>
        </div>

        <div class="lp-report-panel rounded-3xl p-5">
            <div class="grid gap-3">
                <?php foreach ($reports as $report): ?>
                    <div class="lp-report-row report-row flex justify-between rounded-2xl p-4">
                        <span class="font-bold text-slate-300">
                            <?= e($report['label']) ?>
                        </span>
                        <strong>
                            <?= e($report['value']) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>

                <div class="report-row flex justify-between rounded-2xl bg-brand p-4 shadow-glow">
                    <span class="font-bold">
                        Export Excel
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
    const nav = document.getElementById('mainNav');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (nav) {
        const syncNav = () => {
            nav.classList.toggle('is-scrolled', window.scrollY > 12);
        };

        syncNav();
        window.addEventListener('scroll', syncNav, { passive: true });
    }

    if (typeof gsap === 'undefined' || reduceMotion) {
        if (loader) loader.style.display = 'none';
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    gsap.set('body', { opacity: 1 });

    const loaderTl = gsap.timeline({
        defaults: { ease: 'power3.out' },
        onComplete: function () {
            if (loader) loader.style.display = 'none';
        }
    });

    loaderTl
        .from('.lp-loader-mark', {
            y: 18,
            opacity: 0,
            scale: 0.92,
            duration: 0.45
        })
        .from('.lp-loader-copy > *', {
            y: 18,
            opacity: 0,
            duration: 0.42,
            stagger: 0.07
        }, '-=0.18')
        .to('#loaderBar', {
            width: '100%',
            duration: 0.75,
            ease: 'power2.inOut'
        }, '-=0.1')
        .to('#loader', {
            autoAlpha: 0,
            y: -18,
            duration: 0.5,
            ease: 'power2.inOut'
        });

    const heroTl = gsap.timeline({
        delay: 1.05,
        defaults: { ease: 'power3.out' }
    });

    heroTl
        .from('.lp-nav', {
            y: -18,
            opacity: 0,
            duration: 0.55
        })
        .from('.lp-hero-copy > *', {
            y: 28,
            opacity: 0,
            duration: 0.72,
            stagger: 0.08
        }, '-=0.15')
        .from('#dashboardPreview', {
            y: 36,
            opacity: 0,
            scale: 0.96,
            rotateX: 5,
            duration: 0.82
        }, '-=0.48')
        .from('.preview-stat, .preview-checkout, .preview-line, .preview-box', {
            y: 18,
            opacity: 0,
            duration: 0.52,
            stagger: 0.055
        }, '-=0.35')
        .from('.lp-floating-card', {
            y: 18,
            opacity: 0,
            scale: 0.96,
            duration: 0.52,
            stagger: 0.08
        }, '-=0.28');

    gsap.to('.lp-orb-one', {
        yPercent: 12,
        xPercent: 6,
        scrollTrigger: {
            trigger: '.lp-hero',
            start: 'top top',
            end: 'bottom top',
            scrub: 1
        }
    });

    gsap.to('.lp-orb-two', {
        yPercent: -10,
        xPercent: -8,
        scrollTrigger: {
            trigger: '.lp-hero',
            start: 'top top',
            end: 'bottom top',
            scrub: 1
        }
    });

    gsap.to('.lp-floating-card', {
        y: -12,
        duration: 2.4,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
        stagger: {
            each: 0.35,
            repeat: -1,
            yoyo: true
        }
    });
    gsap.utils.toArray('.section-heading').forEach((el) => {
        gsap.from(el.children, {
            scrollTrigger: {
                trigger: el,
                start: 'top 78%'
            },
            y: 28,
            opacity: 0,
            duration: 0.65,
            stagger: 0.08,
            ease: 'power3.out'
        });
    });

    gsap.from('.lp-feature-card', {
        scrollTrigger: {
            trigger: '#fitur',
            start: 'top 72%'
        },
        y: 30,
        opacity: 0,
        duration: 0.62,
        stagger: 0.08,
        ease: 'power3.out'
    });

    gsap.from('.lp-workflow-card', {
        scrollTrigger: {
            trigger: '#workflow',
            start: 'top 72%'
        },
        x: 30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.09,
        ease: 'power3.out'
    });

    gsap.from('.report-row', {
        scrollTrigger: {
            trigger: '#laporan',
            start: 'top 72%'
        },
        y: 22,
        opacity: 0,
        duration: 0.56,
        stagger: 0.08,
        ease: 'power3.out'
    });

    gsap.utils.toArray('.lp-feature-card, .lp-workflow-card').forEach((card) => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                y: -6,
                scale: 1.012,
                duration: 0.22,
                ease: 'power2.out'
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                y: 0,
                scale: 1,
                duration: 0.24,
                ease: 'power2.out'
            });
        });
    });

    ScrollTrigger.matchMedia({
        '(min-width: 1024px)': function () {
            gsap.to('#dashboardPreview', {
                yPercent: -5,
                scrollTrigger: {
                    trigger: '.lp-hero',
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 1.1
                }
            });
        }
    });
});
</script>

</body>
</html>
