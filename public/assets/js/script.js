/* ============================================================
   CUSTOM JAVASCRIPT — POS App
   ============================================================
   Tambahkan custom JS di sini.
   Bootstrap 5 JS sudah di-load via CDN di layout.
   ============================================================ */

// Auto-dismiss alert setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarStorageKey = 'posSidebarCollapsed';
    const desktopSidebarQuery = window.matchMedia('(min-width: 992px)');

    function syncSidebarToggle() {
        if (!sidebarToggle) {
            return;
        }

        const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
        sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
        sidebarToggle.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Minimize sidebar');
        sidebarToggle.setAttribute('title', isCollapsed ? 'Expand sidebar' : 'Minimize sidebar');
    }

    function applySidebarPreference() {
        const shouldCollapse = localStorage.getItem(sidebarStorageKey) === 'true';
        document.documentElement.classList.toggle('sidebar-collapsed', shouldCollapse && desktopSidebarQuery.matches);
        syncSidebarToggle();
    }

    if (sidebarToggle) {
        applySidebarPreference();

        sidebarToggle.addEventListener('click', function() {
            const nextCollapsed = !document.documentElement.classList.contains('sidebar-collapsed');
            localStorage.setItem(sidebarStorageKey, String(nextCollapsed));
            applySidebarPreference();
        });

        desktopSidebarQuery.addEventListener('change', applySidebarPreference);
    }

    const tourRoot = document.getElementById('appGuideTour');
    const tourStart = document.getElementById('startGuideTour');
    const tourHighlight = document.getElementById('tourHighlight');
    const tourCard = document.getElementById('tourCard');
    const tourTitle = document.getElementById('tourTitle');
    const tourText = document.getElementById('tourText');
    const tourStepCount = document.getElementById('tourStepCount');
    const tourBack = document.getElementById('tourBack');
    const tourNext = document.getElementById('tourNext');
    const tourSkip = document.getElementById('tourSkip');
    const userId = document.body.dataset.userId || 'guest';
    const userRole = document.body.dataset.userRole || 'guest';
    const tourStorageKey = `posGuideTourSeen:${userId}:${userRole}`;
    let currentTourStep = 0;
    let activeTourSteps = [];
    let activeTourTarget = null;
    let restoreCollapsedSidebar = false;

    const baseTourSteps = [
        {
            target: 'dashboard-overview',
            title: 'Mulai dari Dashboard',
            text: 'Di sini kamu melihat ringkasan toko hari ini. Mulai dari angka utama, stok, sampai aktivitas penjualan.'
        },
        {
            target: 'sidebar-toggle',
            title: 'Minimize Sidebar',
            text: 'Klik tombol ini untuk mengecilkan sidebar. Saat kecil, menu tetap bisa dipakai lewat ikon.'
        },
        {
            target: 'nav-dashboard',
            title: 'Kembali ke Dashboard',
            text: 'Gunakan menu Dashboard untuk kembali ke halaman ringkasan dari mana pun.'
        }
    ];

    const adminTourSteps = [
        {
            target: 'quick-add-product',
            title: 'Tambah Produk',
            text: 'Klik kartu ini untuk memasukkan produk baru, harga, dan stok awal.'
        },
        {
            target: 'quick-add-user',
            title: 'Kelola Tim',
            text: 'Klik ini untuk membuat akun kasir. Jika kamu Super Admin, kamu juga bisa membuat akun admin.'
        },
        {
            target: 'nav-products',
            title: 'Kelola Produk',
            text: 'Menu Produk dipakai untuk melihat, mengedit, dan menonaktifkan produk.'
        }
    ];

    const cashierTourSteps = [
        {
            target: 'quick-transaction',
            title: 'Transaksi Baru',
            text: 'Klik kartu ini untuk membuka kasir dan mulai checkout pelanggan.'
        },
        {
            target: 'quick-product-search',
            title: 'Cari Produk',
            text: 'Gunakan ini untuk mengecek harga dan stok produk tanpa masuk ke transaksi.'
        },
        {
            target: 'nav-transaction',
            title: 'Menu Transaksi',
            text: 'Menu ini adalah jalur utama kasir untuk melayani transaksi harian.'
        }
    ];

    const endingTourSteps = [
        {
            target: 'dashboard-kpis',
            title: 'Pantau Angka Utama',
            text: 'Bagian ini menunjukkan pendapatan, jumlah transaksi, produk terjual, dan total stok.'
        },
        {
            target: 'quick-export',
            title: 'Export Laporan',
            text: 'Klik Export CSV untuk mengunduh laporan penjualan hari ini.'
        },
        {
            target: 'tour-help',
            title: 'Ulangi Guide Tour',
            text: 'Kapan pun butuh bantuan, klik Guide Tour untuk menjalankan panduan ini lagi.'
        }
    ];

    function getTourSteps() {
        const roleSteps = ['admin', 'super_admin'].includes(userRole) ? adminTourSteps : cashierTourSteps;
        return [...baseTourSteps, ...roleSteps, ...endingTourSteps].filter(function(step) {
            return document.querySelector(`[data-tour="${step.target}"]`);
        });
    }

    function clearTourTarget() {
        if (activeTourTarget) {
            activeTourTarget.classList.remove('app-tour-target');
            activeTourTarget = null;
        }
    }

    function positionTourCard(targetRect) {
        const spacing = 14;
        const cardWidth = tourCard.offsetWidth || 380;
        const cardHeight = tourCard.offsetHeight || 220;
        let left = targetRect.right + spacing;
        let top = targetRect.top;

        if (left + cardWidth > window.innerWidth - 16) {
            left = targetRect.left - cardWidth - spacing;
        }

        if (left < 16) {
            left = Math.min(Math.max(targetRect.left, 16), window.innerWidth - cardWidth - 16);
            top = targetRect.bottom + spacing;
        }

        if (top + cardHeight > window.innerHeight - 16) {
            top = window.innerHeight - cardHeight - 16;
        }

        tourCard.style.left = `${Math.max(16, left)}px`;
        tourCard.style.top = `${Math.max(16, top)}px`;
    }

    function renderTourStep() {
        const step = activeTourSteps[currentTourStep];
        const target = document.querySelector(`[data-tour="${step.target}"]`);

        if (!target) {
            currentTourStep += 1;
            if (currentTourStep >= activeTourSteps.length) {
                finishTour();
            } else {
                renderTourStep();
            }
            return;
        }

        clearTourTarget();
        activeTourTarget = target;
        activeTourTarget.classList.add('app-tour-target');
        target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

        window.setTimeout(function() {
            const rect = target.getBoundingClientRect();
            const padding = 8;

            tourHighlight.style.left = `${Math.max(8, rect.left - padding)}px`;
            tourHighlight.style.top = `${Math.max(8, rect.top - padding)}px`;
            tourHighlight.style.width = `${rect.width + padding * 2}px`;
            tourHighlight.style.height = `${rect.height + padding * 2}px`;

            tourStepCount.textContent = `Step ${currentTourStep + 1}/${activeTourSteps.length}`;
            tourTitle.textContent = step.title;
            tourText.textContent = step.text;
            tourBack.disabled = currentTourStep === 0;
            tourNext.textContent = currentTourStep === activeTourSteps.length - 1 ? 'Finish' : 'Next';
            positionTourCard(rect);
        }, 220);
    }

    function startTour(forceStart) {
        if (!tourRoot) {
            return;
        }

        activeTourSteps = getTourSteps();
        if (activeTourSteps.length === 0) {
            return;
        }

        if (!forceStart && localStorage.getItem(tourStorageKey) === 'true') {
            return;
        }

        restoreCollapsedSidebar = localStorage.getItem(sidebarStorageKey) === 'true';
        document.documentElement.classList.remove('sidebar-collapsed');
        syncSidebarToggle();
        tourRoot.classList.add('is-active');
        tourRoot.setAttribute('aria-hidden', 'false');
        currentTourStep = 0;
        renderTourStep();
    }

    function finishTour() {
        localStorage.setItem(tourStorageKey, 'true');
        clearTourTarget();

        if (tourRoot) {
            tourRoot.classList.remove('is-active');
            tourRoot.setAttribute('aria-hidden', 'true');
        }

        if (restoreCollapsedSidebar) {
            applySidebarPreference();
        }
    }

    if (tourRoot) {
        window.setTimeout(function() {
            startTour(false);
        }, 700);

        if (tourStart) {
            tourStart.addEventListener('click', function() {
                startTour(true);
            });
        }

        tourNext.addEventListener('click', function() {
            if (currentTourStep >= activeTourSteps.length - 1) {
                finishTour();
                return;
            }

            currentTourStep += 1;
            renderTourStep();
        });

        tourBack.addEventListener('click', function() {
            if (currentTourStep === 0) {
                return;
            }

            currentTourStep -= 1;
            renderTourStep();
        });

        tourSkip.addEventListener('click', finishTour);

        window.addEventListener('resize', function() {
            if (tourRoot.classList.contains('is-active')) {
                renderTourStep();
            }
        });
    }

    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    if (window.gsap) {
        gsap.from('.app-sidebar', {
            x: -22,
            opacity: 0,
            duration: 0.45,
            ease: 'power2.out'
        });

        gsap.from('.app-topbar', {
            y: -14,
            opacity: 0,
            duration: 0.45,
            delay: 0.08,
            ease: 'power2.out'
        });

        gsap.from('.app-content > *', {
            y: 18,
            opacity: 0,
            duration: 0.52,
            stagger: 0.04,
            ease: 'power2.out'
        });
    }
});
