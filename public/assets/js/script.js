/* ============================================================
   CUSTOM JAVASCRIPT — POS App
   Fixed sidebar toggle, active menu, tour, alerts, and animation
   ============================================================ */

document.addEventListener("DOMContentLoaded", function () {
  const root = document.documentElement;
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebarStorageKey = "posSidebarCollapsed";
  const desktopSidebarQuery = window.matchMedia("(min-width: 992px)");

  /* ============================================================
       SIDEBAR ACTIVE MENU
       ============================================================ */

  document.querySelectorAll(".app-sidebar-link").forEach(function (link) {
    const linkPath = new URL(
      link.href,
      window.location.origin,
    ).pathname.replace(/\/$/, "");
    const currentPath = window.location.pathname.replace(/\/$/, "");

    const isActive =
      currentPath === linkPath || currentPath.startsWith(`${linkPath}/`);

    link.classList.toggle("is-active", isActive);
    link.classList.toggle("active", isActive);

    if (isActive) {
      link.setAttribute("aria-current", "page");
    } else {
      link.removeAttribute("aria-current");
    }
  });

  /* ============================================================
       SIDEBAR COLLAPSE
       ============================================================ */

  function syncSidebarToggle() {
    if (!sidebarToggle) return;

    const isCollapsed = root.classList.contains("sidebar-collapsed");

    sidebarToggle.setAttribute("aria-expanded", String(!isCollapsed));
    sidebarToggle.setAttribute(
      "aria-label",
      isCollapsed ? "Expand sidebar" : "Minimize sidebar",
    );
    sidebarToggle.setAttribute(
      "title",
      isCollapsed ? "Expand sidebar" : "Minimize sidebar",
    );

    const icon = sidebarToggle.querySelector("i");

    if (icon) {
      icon.className = isCollapsed
        ? "ti ti-layout-sidebar-right-collapse"
        : "ti ti-layout-sidebar-left-collapse";
    }
  }

  function setSidebarCollapsed(collapsed, persist = true) {
    const shouldCollapse = collapsed && desktopSidebarQuery.matches;

    root.classList.toggle("sidebar-collapsed", shouldCollapse);

    if (persist) {
      try {
        localStorage.setItem(sidebarStorageKey, collapsed ? "true" : "false");
      } catch (error) {}
    }

    syncSidebarToggle();
  }

  function applySidebarPreference() {
    let savedCollapsed = false;

    try {
      savedCollapsed = localStorage.getItem(sidebarStorageKey) === "true";
    } catch (error) {}

    setSidebarCollapsed(savedCollapsed, false);
  }

  applySidebarPreference();

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const nextCollapsed = !root.classList.contains("sidebar-collapsed");
      setSidebarCollapsed(nextCollapsed, true);
    });
  }

  if (desktopSidebarQuery.addEventListener) {
    desktopSidebarQuery.addEventListener("change", applySidebarPreference);
  } else if (desktopSidebarQuery.addListener) {
    desktopSidebarQuery.addListener(applySidebarPreference);
  }

  /* ============================================================
       GUIDE TOUR
       ============================================================ */

  const tourRoot = document.getElementById("appGuideTour");
  const tourStart = document.getElementById("startGuideTour");
  const tourHighlight = document.getElementById("tourHighlight");
  const tourCard = document.getElementById("tourCard");
  const tourTitle = document.getElementById("tourTitle");
  const tourText = document.getElementById("tourText");
  const tourStepCount = document.getElementById("tourStepCount");
  const tourBack = document.getElementById("tourBack");
  const tourNext = document.getElementById("tourNext");
  const tourSkip = document.getElementById("tourSkip");

  const userId = document.body.dataset.userId || "guest";
  const userRole = document.body.dataset.userRole || "guest";
  const tourStorageKey = `posGuideTourSeen:${userId}:${userRole}`;

  let currentTourStep = 0;
  let activeTourSteps = [];
  let activeTourTarget = null;
  let restoreCollapsedSidebar = false;

  const baseTourSteps = [
    {
      target: "dashboard-overview",
      title: "Mulai dari Dashboard",
      text: "Di sini kamu melihat ringkasan toko hari ini. Mulai dari angka utama, stok, sampai aktivitas penjualan.",
    },
    {
      target: "sidebar-toggle",
      title: "Minimize Sidebar",
      text: "Klik tombol ini untuk mengecilkan sidebar. Saat kecil, menu tetap bisa dipakai lewat ikon.",
    },
    {
      target: "nav-dashboard",
      title: "Kembali ke Dashboard",
      text: "Gunakan menu Dashboard untuk kembali ke halaman ringkasan dari mana pun.",
    },
  ];

  const adminTourSteps = [
    {
      target: "quick-add-product",
      title: "Tambah Produk",
      text: "Klik kartu ini untuk memasukkan produk baru, harga, dan stok awal.",
    },
    {
      target: "quick-add-user",
      title: "Kelola Tim",
      text: "Klik ini untuk membuat akun kasir. Jika kamu Super Admin, kamu juga bisa membuat akun admin.",
    },
    {
      target: "nav-products",
      title: "Kelola Produk",
      text: "Menu Produk dipakai untuk melihat, mengedit, dan menonaktifkan produk.",
    },
  ];

  const cashierTourSteps = [
    {
      target: "quick-transaction",
      title: "Transaksi Baru",
      text: "Klik kartu ini untuk membuka kasir dan mulai checkout pelanggan.",
    },
    {
      target: "quick-product-search",
      title: "Cari Produk",
      text: "Gunakan ini untuk mengecek harga dan stok produk tanpa masuk ke transaksi.",
    },
    {
      target: "nav-transaction",
      title: "Menu Transaksi",
      text: "Menu ini adalah jalur utama kasir untuk melayani transaksi harian.",
    },
  ];

  const endingTourSteps = [
    {
      target: "dashboard-kpis",
      title: "Pantau Angka Utama",
      text: "Bagian ini menunjukkan pendapatan, jumlah transaksi, produk terjual, dan total stok.",
    },
    {
      target: "quick-export",
      title: "Export Laporan",
      text: "Klik Export CSV untuk mengunduh laporan penjualan hari ini.",
    },
    {
      target: "tour-help",
      title: "Ulangi Guide Tour",
      text: "Kapan pun butuh bantuan, klik Guide Tour untuk menjalankan panduan ini lagi.",
    },
  ];

  function getTourSteps() {
    const roleSteps = ["admin", "super_admin"].includes(userRole)
      ? adminTourSteps
      : cashierTourSteps;

    return [...baseTourSteps, ...roleSteps, ...endingTourSteps].filter(
      function (step) {
        return document.querySelector(`[data-tour="${step.target}"]`);
      },
    );
  }

  function clearTourTarget() {
    if (activeTourTarget) {
      activeTourTarget.classList.remove("app-tour-target");
      activeTourTarget = null;
    }
  }

  function positionTourCard(targetRect) {
    if (!tourCard) return;

    const spacing = 14;
    const cardWidth = tourCard.offsetWidth || 380;
    const cardHeight = tourCard.offsetHeight || 220;

    let left = targetRect.right + spacing;
    let top = targetRect.top;

    if (left + cardWidth > window.innerWidth - 16) {
      left = targetRect.left - cardWidth - spacing;
    }

    if (left < 16) {
      left = Math.min(
        Math.max(targetRect.left, 16),
        window.innerWidth - cardWidth - 16,
      );
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

    if (!step) {
      finishTour();
      return;
    }

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
    activeTourTarget.classList.add("app-tour-target");

    target.scrollIntoView({
      behavior: "smooth",
      block: "center",
      inline: "center",
    });

    window.setTimeout(function () {
      const rect = target.getBoundingClientRect();
      const padding = 8;

      if (tourHighlight) {
        tourHighlight.style.left = `${Math.max(8, rect.left - padding)}px`;
        tourHighlight.style.top = `${Math.max(8, rect.top - padding)}px`;
        tourHighlight.style.width = `${rect.width + padding * 2}px`;
        tourHighlight.style.height = `${rect.height + padding * 2}px`;
      }

      if (tourStepCount) {
        tourStepCount.textContent = `Step ${currentTourStep + 1}/${activeTourSteps.length}`;
      }

      if (tourTitle) {
        tourTitle.textContent = step.title;
      }

      if (tourText) {
        tourText.textContent = step.text;
      }

      if (tourBack) {
        tourBack.disabled = currentTourStep === 0;
      }

      if (tourNext) {
        tourNext.textContent =
          currentTourStep === activeTourSteps.length - 1 ? "Finish" : "Next";
      }

      positionTourCard(rect);
    }, 220);
  }

  function startTour(forceStart) {
    if (!tourRoot) return;

    activeTourSteps = getTourSteps();

    if (activeTourSteps.length === 0) return;

    if (!forceStart) {
      try {
        if (localStorage.getItem(tourStorageKey) === "true") return;
      } catch (error) {}
    }

    try {
      restoreCollapsedSidebar =
        localStorage.getItem(sidebarStorageKey) === "true";
    } catch (error) {
      restoreCollapsedSidebar = false;
    }

    setSidebarCollapsed(false, false);

    tourRoot.classList.add("is-active");
    tourRoot.setAttribute("aria-hidden", "false");

    currentTourStep = 0;
    renderTourStep();
  }

  function finishTour() {
    try {
      localStorage.setItem(tourStorageKey, "true");
    } catch (error) {}

    clearTourTarget();

    if (tourRoot) {
      tourRoot.classList.remove("is-active");
      tourRoot.setAttribute("aria-hidden", "true");
    }

    if (restoreCollapsedSidebar) {
      setSidebarCollapsed(true, false);
    }
  }

  if (tourRoot) {
    window.setTimeout(function () {
      startTour(false);
    }, 700);

    if (tourStart) {
      tourStart.addEventListener("click", function () {
        startTour(true);
      });
    }

    if (tourNext) {
      tourNext.addEventListener("click", function () {
        if (currentTourStep >= activeTourSteps.length - 1) {
          finishTour();
          return;
        }

        currentTourStep += 1;
        renderTourStep();
      });
    }

    if (tourBack) {
      tourBack.addEventListener("click", function () {
        if (currentTourStep === 0) return;

        currentTourStep -= 1;
        renderTourStep();
      });
    }

    if (tourSkip) {
      tourSkip.addEventListener("click", finishTour);
    }

    window.addEventListener("resize", function () {
      if (tourRoot.classList.contains("is-active")) {
        renderTourStep();
      }
    });
  }

  /* ============================================================
       ALERT AUTO DISMISS
       ============================================================ */

  const alerts = document.querySelectorAll(".alert-dismissible, .app-alert");

  alerts.forEach(function (alert) {
    window.setTimeout(function () {
      if (window.bootstrap && bootstrap.Alert) {
        try {
          const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
          bsAlert.close();
          return;
        } catch (error) {}
      }

      alert.remove();
    }, 5000);
  });

  /* ============================================================
       CONTENT ANIMATION ONLY
       ============================================================ */

  if (window.gsap) {
    gsap.from(".app-content > *", {
      y: 12,
      opacity: 0,
      duration: 0.32,
      stagger: 0.03,
      ease: "power2.out",
    });
  }
});
