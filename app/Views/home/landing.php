<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?> - POS Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#111827',
                        muted: '#64748b',
                        surface: '#f8fafc',
                        brand: '#2563eb',
                        mint: '#10b981',
                        amber: '#f59e0b'
                    },
                    boxShadow: {
                        soft: '0 24px 70px rgba(15, 23, 42, 0.12)',
                        panel: '0 16px 50px rgba(15, 23, 42, 0.10)'
                    }
                }
            }
        }
    </script>
    <style>
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #f8fafc;
            color: #111827;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            overflow-x: hidden;
        }

        .page-bg {
            background:
                radial-gradient(circle at 12% 8%, rgba(37, 99, 235, 0.12), transparent 30rem),
                radial-gradient(circle at 90% 12%, rgba(16, 185, 129, 0.12), transparent 28rem),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 55%, #eef4ff 100%);
        }

        .loader {
            background:
                radial-gradient(circle at 50% 35%, rgba(37, 99, 235, 0.12), transparent 24rem),
                #ffffff;
        }

        .loader-orbit {
            animation: spin 1.45s linear infinite;
            border: 1px solid rgba(37, 99, 235, 0.12);
            border-top-color: #2563eb;
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
            letter-spacing: -0.06em;
            line-height: 0.96;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(226, 232, 240, 0.95);
            backdrop-filter: blur(24px);
        }

        .dashboard-preview {
            background:
                linear-gradient(135deg, rgba(37, 99, 235, 0.10), transparent 28%),
                #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            overflow: hidden;
        }

        .preview-sidebar {
            background: #0f172a;
            border-radius: 22px;
        }

        .preview-line {
            background: #e2e8f0;
            border-radius: 999px;
            height: 8px;
        }

        .metric-pill {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #fff;
        }

        .floating-card {
            animation: floatCard 4.8s ease-in-out infinite;
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
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .feature-card:hover {
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.10);
            transform: translateY(-4px);
        }

        .soft-grid {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        @media (max-width: 768px) {
            .hero-shell {
                min-height: auto;
            }

            .dashboard-preview {
                border-radius: 22px;
            }
        }
    </style>
</head>

<body>
    <div id="loader" class="loader fixed inset-0 z-50 grid place-items-center">
        <div class="w-[min(420px,calc(100vw-40px))] text-center">
            <div class="mx-auto mb-6 grid h-20 w-20 place-items-center rounded-3xl bg-brand text-3xl font-black text-white shadow-soft">
                <div class="loader-orbit absolute h-28 w-28"></div>
                P
            </div>
            <div class="loader-copy">
                <div class="text-sm font-black uppercase tracking-[0.2em] text-brand">Preparing workspace</div>
                <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] text-ink">Loading modern POS experience</h1>
                <p class="mt-3 text-sm font-semibold leading-6 text-muted">Sinkronisasi produk, stok, transaksi, dan laporan harian.</p>
            </div>
            <div class="mt-7 h-2 overflow-hidden rounded-full bg-slate-200">
                <div id="loaderBar" class="h-full w-0 rounded-full bg-gradient-to-r from-brand via-mint to-amber"></div>
            </div>
        </div>
    </div>

    <div class="page-bg min-h-screen">
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
                <a href="<?= url('/') ?>" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand text-xl font-black text-white shadow-sm">P</span>
                    <span class="text-xl font-black tracking-[-0.04em]"><?= e(APP_NAME) ?></span>
                </a>
                <div class="flex items-center gap-2">
                    <a href="#fitur" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100 sm:inline-flex">Fitur</a>
                    <a href="#workflow" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100 sm:inline-flex">Workflow</a>
                    <a href="#laporan" class="hidden rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100 sm:inline-flex">Laporan</a>
                    <a href="<?= url('/login') ?>" class="rounded-2xl bg-ink px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-slate-700">Login</a>
                </div>
            </nav>
        </header>

        <main>
            <section class="hero-shell soft-grid relative overflow-hidden">
                <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[0.94fr_1.06fr] lg:items-center">
                    <div class="hero-copy">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-black text-blue-700">
                            <span class="h-2 w-2 rounded-full bg-mint"></span>
                            POS modern untuk operasional toko harian
                        </div>
                        <h1 class="hero-title max-w-4xl font-black text-ink">
                            Kasir cepat, stok terkendali, laporan siap.
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-muted">
                            Satu dashboard formal dan mudah dipakai untuk admin dan kasir: kelola produk, checkout, cetak struk, dan export laporan harian tanpa alur yang rumit.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="<?= url('/login') ?>" class="rounded-2xl bg-brand px-7 py-4 text-center font-black text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-blue-700">
                                Masuk Dashboard
                            </a>
                            <a href="#fitur" class="rounded-2xl border border-slate-300 bg-white px-7 py-4 text-center font-black text-slate-800 transition hover:-translate-y-0.5 hover:bg-slate-50">
                                Lihat Fitur
                            </a>
                        </div>
                        <div class="mt-10 grid max-w-xl grid-cols-3 gap-3">
                            <div class="metric-pill p-4">
                                <strong class="block text-2xl font-black">3x</strong>
                                <span class="text-xs font-bold text-muted">Alur lebih ringkas</span>
                            </div>
                            <div class="metric-pill p-4">
                                <strong class="block text-2xl font-black">80mm</strong>
                                <span class="text-xs font-bold text-muted">Struk siap print</span>
                            </div>
                            <div class="metric-pill p-4">
                                <strong class="block text-2xl font-black">CSV</strong>
                                <span class="text-xs font-bold text-muted">Laporan harian</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-visual relative">
                        <div class="dashboard-preview shadow-soft">
                            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-red-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Live Store</span>
                            </div>

                            <div class="grid gap-4 p-4 md:grid-cols-[180px_1fr]">
                                <aside class="preview-sidebar hidden p-4 md:block">
                                    <div class="mb-6 flex items-center gap-2">
                                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand text-sm font-black text-white">P</span>
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
                                        <div class="rounded-2xl bg-blue-50 p-4">
                                            <span class="text-xs font-black uppercase text-blue-700">Revenue</span>
                                            <strong class="mt-2 block text-2xl font-black">Rp 420K</strong>
                                        </div>
                                        <div class="rounded-2xl bg-emerald-50 p-4">
                                            <span class="text-xs font-black uppercase text-emerald-700">Transaksi</span>
                                            <strong class="mt-2 block text-2xl font-black">3 trx</strong>
                                        </div>
                                        <div class="rounded-2xl bg-amber-50 p-4">
                                            <span class="text-xs font-black uppercase text-amber-700">Low Stock</span>
                                            <strong class="mt-2 block text-2xl font-black">2 item</strong>
                                        </div>
                                    </div>

                                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-panel">
                                        <div class="mb-4 flex items-center justify-between">
                                            <div>
                                                <h3 class="font-black">Transaksi Berjalan</h3>
                                                <p class="text-sm font-semibold text-muted">Total dan kembalian dihitung otomatis.</p>
                                            </div>
                                            <span class="rounded-xl bg-brand px-3 py-2 text-xs font-black text-white">Checkout</span>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold"><span>Kopi Arabica x2</span><span>Rp 50.000</span></div>
                                            <div class="flex justify-between rounded-2xl bg-slate-50 p-4 text-sm font-bold"><span>Susu Segar x1</span><span>Rp 12.000</span></div>
                                            <div class="grid grid-cols-2 gap-3 pt-2">
                                                <div class="rounded-2xl border border-slate-200 p-4">
                                                    <span class="text-xs font-black uppercase text-muted">Paid</span>
                                                    <strong class="block text-xl font-black">Rp 100K</strong>
                                                </div>
                                                <div class="rounded-2xl border border-slate-200 p-4">
                                                    <span class="text-xs font-black uppercase text-muted">Change</span>
                                                    <strong class="block text-xl font-black text-emerald-600">Rp 38K</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="floating-card absolute -right-2 top-12 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-panel lg:block">
                            <span class="text-xs font-black uppercase text-muted">Receipt</span>
                            <strong class="block text-lg font-black">Ready to print</strong>
                        </div>
                        <div class="floating-card absolute -left-4 bottom-16 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-panel lg:block">
                            <span class="text-xs font-black uppercase text-muted">Stock</span>
                            <strong class="block text-lg font-black text-emerald-600">Auto synced</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="border-y border-slate-200 bg-white py-20">
                <div class="mx-auto max-w-7xl px-5">
                    <div class="mb-10 max-w-3xl">
                        <span class="text-sm font-black uppercase text-brand">Fitur utama</span>
                        <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] md:text-6xl">Lengkap, tapi tetap ringan dipakai setiap hari.</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <article class="feature-card p-6">
                            <div class="mb-6 grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-lg font-black text-blue-700">01</div>
                            <h3 class="text-xl font-black">Produk & Stok</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-muted">CRUD produk, stok rendah, dan update stok otomatis setelah checkout.</p>
                        </article>
                        <article class="feature-card p-6">
                            <div class="mb-6 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-lg font-black text-emerald-700">02</div>
                            <h3 class="text-xl font-black">Keranjang Cepat</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-muted">Cari produk, edit quantity, quick cash, total, dan kembalian otomatis.</p>
                        </article>
                        <article class="feature-card p-6">
                            <div class="mb-6 grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-lg font-black text-slate-800">03</div>
                            <h3 class="text-xl font-black">Struk Print</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-muted">Struk thermal 80mm yang mudah dicetak setelah transaksi selesai.</p>
                        </article>
                        <article class="feature-card p-6">
                            <div class="mb-6 grid h-12 w-12 place-items-center rounded-2xl bg-amber-50 text-lg font-black text-amber-700">04</div>
                            <h3 class="text-xl font-black">Laporan Harian</h3>
                            <p class="mt-3 text-sm font-semibold leading-6 text-muted">Dashboard, omzet, produk terlaris, uang diterima, dan export CSV.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="workflow" class="py-20">
                <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                    <div>
                        <span class="text-sm font-black uppercase text-brand">Workflow</span>
                        <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] md:text-6xl">Dari stok sampai struk dalam satu alur.</h2>
                        <p class="mt-5 text-lg font-semibold leading-8 text-muted">Admin menyiapkan produk dan user. Kasir menjalankan transaksi. Sistem merapikan stok, pembayaran, kembalian, struk, dan laporan.</p>
                    </div>
                    <div class="grid gap-4">
                        <div class="glass-panel rounded-3xl p-5 shadow-panel">
                            <div class="flex items-center gap-4">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 font-black text-blue-700">1</span>
                                <div><strong class="block font-black">Kelola produk dan stok</strong><span class="text-sm font-semibold text-muted">Admin update produk sebelum toko mulai ramai.</span></div>
                            </div>
                        </div>
                        <div class="glass-panel rounded-3xl p-5 shadow-panel">
                            <div class="flex items-center gap-4">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 font-black text-emerald-700">2</span>
                                <div><strong class="block font-black">Checkout pelanggan</strong><span class="text-sm font-semibold text-muted">Kasir pilih produk, input pembayaran, lalu sistem hitung kembalian.</span></div>
                            </div>
                        </div>
                        <div class="glass-panel rounded-3xl p-5 shadow-panel">
                            <div class="flex items-center gap-4">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-amber-50 font-black text-amber-700">3</span>
                                <div><strong class="block font-black">Cetak dan export</strong><span class="text-sm font-semibold text-muted">Struk siap print dan laporan harian siap diunduh.</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="laporan" class="bg-ink py-20 text-white">
                <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="text-sm font-black uppercase text-blue-300">Report-ready</span>
                        <h2 class="mt-3 text-4xl font-black tracking-[-0.04em] md:text-6xl">Data harian lebih mudah dibaca.</h2>
                        <p class="mt-5 text-lg font-semibold leading-8 text-slate-300">Lihat pendapatan, transaksi, produk terjual, uang diterima, kembalian, dan export CSV untuk audit harian.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <div class="grid gap-3">
                            <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span class="font-bold text-slate-300">Omzet</span><strong>Rp 420.000</strong></div>
                            <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span class="font-bold text-slate-300">Produk Terjual</span><strong>18 pcs</strong></div>
                            <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span class="font-bold text-slate-300">Uang Diterima</span><strong>Rp 500.000</strong></div>
                            <div class="flex justify-between rounded-2xl bg-blue-500 p-4"><span class="font-bold">Export CSV</span><strong>Ready</strong></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white py-8">
            <div class="mx-auto flex max-w-7xl flex-col justify-between gap-3 px-5 text-sm font-semibold text-muted sm:flex-row">
                <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Kelompok 3.</span>
                <a href="<?= url('/login') ?>" class="font-black text-ink">Masuk ke aplikasi</a>
            </div>
        </footer>
    </div>

    <script>
        window.addEventListener('load', function () {
            const loaderTl = gsap.timeline({
                defaults: { ease: 'power3.out' },
                onComplete: function () {
                    document.getElementById('loader').style.display = 'none';
                }
            });

            loaderTl
                .from('.loader-copy > *', { y: 18, opacity: 0, duration: 0.45, stagger: 0.08 })
                .to('#loaderBar', { width: '100%', duration: 0.8, ease: 'power2.inOut' }, '-=0.1')
                .to('#loader', { opacity: 0, y: -18, duration: 0.55, ease: 'power2.inOut' });

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
