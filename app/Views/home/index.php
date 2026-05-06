<!-- ============================================================ -->
<!-- DASHBOARD -->
<!-- ============================================================ -->

<div class="row g-4 mb-4">
    <!-- Card: Total Produk -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-box-seam text-primary fs-2"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Total Produk</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalProducts ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Stok -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-archive text-success fs-2"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Total Stok</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalStock ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Quick Links -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-lightning text-info fs-2"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Aksi Cepat</h6>
                    <a href="/admin/product/create" class="btn btn-sm btn-primary me-1">
                        <i class="bi bi-plus"></i> Tambah Produk
                    </a>
                    <a href="/kasir/transaction" class="btn btn-sm btn-success">
                        <i class="bi bi-cart"></i> Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-info-circle"></i> Selamat Datang di <?= APP_NAME ?></h5>
        <p class="card-text text-muted">
            Aplikasi Point of Sale (POS) sederhana ini dibuat menggunakan PHP Native dengan arsitektur MVC Modular.
        </p>
        <hr>
        <h6>Navigasi:</h6>
        <ul class="list-unstyled ms-3">
            <li><i class="bi bi-arrow-right text-primary"></i> <strong>Admin → Kelola Produk</strong> — Tambah, edit, dan hapus produk</li>
            <li><i class="bi bi-arrow-right text-success"></i> <strong>Kasir → Lihat Produk</strong> — Lihat daftar produk (read-only)</li>
            <li><i class="bi bi-arrow-right text-info"></i> <strong>Kasir → Transaksi</strong> — Buat transaksi penjualan</li>
        </ul>
    </div>
</div>
