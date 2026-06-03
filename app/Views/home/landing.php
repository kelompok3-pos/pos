<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?> - Kasir Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#111827',
                        muted: '#6b7280',
                        paper: '#f8fafc',
                        brand: '#2563eb',
                        mint: '#10b981'
                    },
                    boxShadow: {
                        soft: '0 18px 50px rgba(15, 23, 42, 0.12)'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-paper text-ink antialiased">
    <div id="preloader" class="fixed inset-0 z-50 grid place-items-center bg-white">
        <div class="w-72">
            <div class="mb-5 flex items-center justify-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand text-white shadow-soft">
                    <span class="text-xl font-black">P</span>
                </div>
                <div>
                    <div class="text-lg font-black"><?= e(APP_NAME) ?></div>
                    <div class="text-sm text-muted">Menyiapkan kasir...</div>
                </div>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                <div id="preloaderBar" class="h-full w-0 rounded-full bg-brand"></div>
            </div>
        </div>
    </div>

    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <a href="<?= url('/') ?>" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand text-lg font-black text-white">P</span>
                <span class="text-lg font-black tracking-tight"><?= e(APP_NAME) ?></span>
            </a>
            <div class="flex items-center gap-2">
                <a href="#fitur" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:inline-block">Fitur</a>
                <a href="#laporan" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:inline-block">Laporan</a>
                <a href="<?= url('/login') ?>" class="rounded-xl bg-ink px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-slate-700">Login</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="mx-auto grid min-h-[calc(100vh-73px)] max-w-6xl items-center gap-10 px-5 py-12 lg:grid-cols-[1fr_0.9fr]">
                <div class="hero-copy">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-mint"></span>
                        POS ringan untuk toko harian
                    </div>
                    <h1 class="max-w-3xl text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Kasir cepat, stok rapi, laporan harian langsung siap.
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                        Kelola produk, transaksi, struk, stok, dan laporan penjualan dalam satu alur yang sederhana untuk admin dan kasir.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="<?= url('/login') ?>" class="rounded-2xl bg-brand px-6 py-3 text-center font-black text-white shadow-soft hover:bg-blue-700">
                            Mulai Login
                        </a>
                        <a href="#fitur" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-center font-black text-slate-800 hover:bg-slate-50">
                            Lihat Fitur
                        </a>
                    </div>
                </div>

                <div class="hero-panel">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-soft">
                        <div class="rounded-3xl bg-slate-950 p-4 text-white">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-slate-400">Transaksi Hari Ini</div>
                                    <div class="text-2xl font-black">Rp 420.000</div>
                                </div>
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-sm font-bold text-emerald-300">Live</span>
                            </div>
                            <div class="space-y-3">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <div class="flex justify-between text-sm text-slate-300"><span>Kopi Arabica x2</span><span>Rp 50.000</span></div>
                                    <div class="mt-3 h-2 rounded-full bg-white/10"><div class="h-full w-3/4 rounded-full bg-emerald-400"></div></div>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <div class="flex justify-between text-sm text-slate-300"><span>Susu Segar x1</span><span>Rp 12.000</span></div>
                                    <div class="mt-3 h-2 rounded-full bg-white/10"><div class="h-full w-1/2 rounded-full bg-blue-400"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 pt-4">
                            <div class="rounded-2xl bg-blue-50 p-4 text-center">
                                <div class="text-xl font-black text-blue-700">5</div>
                                <div class="text-xs font-bold text-slate-500">Produk</div>
                            </div>
                            <div class="rounded-2xl bg-emerald-50 p-4 text-center">
                                <div class="text-xl font-black text-emerald-700">3</div>
                                <div class="text-xs font-bold text-slate-500">Trx</div>
                            </div>
                            <div class="rounded-2xl bg-amber-50 p-4 text-center">
                                <div class="text-xl font-black text-amber-700">2</div>
                                <div class="text-xs font-bold text-slate-500">Stok Low</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="border-y border-slate-200 bg-white py-16">
            <div class="mx-auto max-w-6xl px-5">
                <div class="mb-10 max-w-2xl">
                    <h2 class="text-3xl font-black tracking-tight">Dibuat untuk alur toko yang nyata</h2>
                    <p class="mt-3 text-slate-600">Fitur utama fokus ke kecepatan kasir, kontrol admin, dan laporan yang mudah dipakai.</p>
                </div>
                <div class="feature-grid grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="mb-4 grid h-11 w-11 place-items-center rounded-xl bg-blue-600 text-white">01</div>
                        <h3 class="font-black">Produk & Stok</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">CRUD produk, stok rendah, dan update stok otomatis setelah checkout.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="mb-4 grid h-11 w-11 place-items-center rounded-xl bg-emerald-600 text-white">02</div>
                        <h3 class="font-black">Keranjang Cepat</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Cari produk, edit quantity, quick cash, total, dan kembalian otomatis.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="mb-4 grid h-11 w-11 place-items-center rounded-xl bg-slate-900 text-white">03</div>
                        <h3 class="font-black">Struk Print</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Struk gaya thermal 80mm yang mudah dicetak setelah transaksi.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="mb-4 grid h-11 w-11 place-items-center rounded-xl bg-amber-500 text-white">04</div>
                        <h3 class="font-black">Laporan Harian</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Dashboard, produk terlaris, omzet, uang diterima, dan export CSV.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="laporan" class="py-16">
            <div class="mx-auto grid max-w-6xl gap-8 px-5 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-black tracking-tight">Lebih mudah dipakai, lebih mudah diaudit.</h2>
                    <p class="mt-4 leading-8 text-slate-600">
                        Admin bisa melihat performa harian tanpa membuka database. Kasir tetap fokus pada transaksi, sementara sistem menjaga stok dan struk.
                    </p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-soft">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-black">Ringkasan Hari Ini</h3>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700">CSV Ready</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between rounded-2xl bg-slate-50 p-4"><span class="font-bold text-slate-600">Omzet</span><strong>Rp 420.000</strong></div>
                        <div class="flex justify-between rounded-2xl bg-slate-50 p-4"><span class="font-bold text-slate-600">Produk Terjual</span><strong>18 pcs</strong></div>
                        <div class="flex justify-between rounded-2xl bg-slate-50 p-4"><span class="font-bold text-slate-600">Kembalian</span><strong>Rp 80.000</strong></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white py-8">
        <div class="mx-auto flex max-w-6xl flex-col justify-between gap-3 px-5 text-sm text-slate-500 sm:flex-row">
            <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Kelompok 3.</span>
            <a href="<?= url('/login') ?>" class="font-bold text-slate-800">Masuk ke aplikasi</a>
        </div>
    </footer>

    <script>
        window.addEventListener('load', function () {
            gsap.to('#preloaderBar', { width: '100%', duration: 0.75, ease: 'power2.out' });
            gsap.to('#preloader', {
                opacity: 0,
                delay: 0.85,
                duration: 0.45,
                ease: 'power2.out',
                onComplete: function () {
                    document.getElementById('preloader').style.display = 'none';
                }
            });

            gsap.from('.hero-copy > *', {
                y: 26,
                opacity: 0,
                duration: 0.75,
                stagger: 0.09,
                delay: 1,
                ease: 'power3.out'
            });

            gsap.from('.hero-panel', {
                y: 34,
                opacity: 0,
                scale: 0.96,
                duration: 0.85,
                delay: 1.1,
                ease: 'power3.out'
            });

            gsap.from('.feature-grid > div', {
                scrollTrigger: {
                    trigger: '.feature-grid',
                    start: 'top 80%'
                },
                y: 24,
                opacity: 0,
                duration: 0.55,
                stagger: 0.08,
                ease: 'power2.out'
            });
        });
    </script>
</body>

</html>
