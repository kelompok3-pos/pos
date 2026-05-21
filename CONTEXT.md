# 📋 Project Context — POS App (Kelompok 3)

> **Dokumen ini adalah referensi utama bagi setiap anggota tim (dan AI assistant) agar seluruh kode yang dihasilkan konsisten, bersih, dan seragam.**

---

## 1. Gambaran Umum

| Item | Detail |
|---|---|
| **Nama Aplikasi** | POS App (Point of Sale) |
| **Bahasa** | PHP 8.2 (Native, tanpa framework) |
| **Arsitektur** | MVC Modular (Model–View–Controller) |
| **Database** | MariaDB 11.3 / MySQL |
| **Frontend** | Bootstrap 5.3 + Vanilla CSS + Vanilla JS |
| **Icon** | Bootstrap Icons |
| **Deployment** | Docker (Apache + MariaDB + phpMyAdmin) |
| **Entry Point** | `public/index.php` |

---

## 2. Struktur Direktori

```
pos/
├── app/
│   ├── Controllers/
│   │   ├── Controller.php              ← Base controller (semua controller HARUS extend ini)
│   │   ├── HomeController.php          ← Controller di root, tanpa subfolder
│   │   ├── Admin/
│   │   │   └── AdminProductController.php
│   │   └── Kasir/
│   │       ├── KasirProductController.php
│   │       └── KasirTransactionController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Transaction.php
│   │   └── User.php
│   └── Views/
│       ├── layouts/
│       │   └── main.php                ← Layout utama (navbar, flash, footer)
│       ├── home/
│       │   └── index.php
│       ├── admin/
│       │   └── product/
│       │       ├── index.php
│       │       ├── create.php
│       │       └── edit.php
│       ├── kasir/
│       │   ├── product/
│       │   │   └── index.php
│       │   └── transaction/
│       │       └── index.php
│       └── errors/
│           └── 404.php
├── config/
│   ├── config.php                      ← Load .env, env(), constants
│   └── database.php                    ← Koneksi PDO (singleton)
├── database/
│   └── pos_db.sql                      ← Schema + seed data
├── helpers/
│   └── functions.php                   ← Helper global (redirect, flash, csrf, format)
├── public/                             ← Document root (APACHE_DOCUMENT_ROOT)
│   ├── .htaccess                       ← Rewrite semua ke index.php
│   ├── index.php                       ← Front controller
│   └── assets/
│       ├── css/
│       │   └── style.css               ← Custom CSS (override Bootstrap)
│       └── js/
│           └── script.js               ← Custom JS
├── storage/
│   └── logs/
├── docker/
├── routes.php                          ← Daftar semua route
├── .env                                ← Environment variables (TIDAK di-commit)
├── .env.example
├── Dockerfile
├── docker-compose.yml
└── .gitignore
```

### Aturan Penamaan File & Folder

| Komponen | Konvensi Nama | Contoh |
|---|---|---|
| **Controller** | `PascalCase`, diakhiri `Controller` | `AdminProductController.php` |
| **Model** | `PascalCase`, singular | `Product.php`, `User.php` |
| **View** | `snake_case`, dikelompokkan per role/fitur | `admin/product/index.php` |
| **Helper** | `snake_case` | `functions.php` |
| **CSS/JS** | `snake_case` | `style.css`, `script.js` |
| **Folder Controller** | `PascalCase` per role | `Admin/`, `Kasir/` |
| **Folder View** | `snake_case` per role | `admin/`, `kasir/` |

### Aturan Penambahan File Baru

- **Controller baru** → taruh di subfolder sesuai role: `app/Controllers/{Role}/{Role}{Feature}Controller.php`
- **Model baru** → taruh langsung di `app/Models/{NamaModel}.php`
- **View baru** → taruh di `app/Views/{role}/{feature}/{action}.php`
- **Helper baru** → tambahkan fungsi di `helpers/functions.php` (satu file saja, jangan buat file baru)
- **Route baru** → tambahkan di `routes.php`

---

## 3. Konvensi Penulisan Kode

### 3.1 Prinsip Umum

- Kode harus **bersih dan mudah dibaca** — bayangkan orang lain yang membaca kode kamu 6 bulan dari sekarang.
- **Jangan tulis komentar yang menjelaskan "apa"** — kode yang baik sudah menjelaskan dirinya sendiri. Tulis komentar hanya untuk menjelaskan **"mengapa"**.
- Gunakan **nama variabel dan fungsi yang deskriptif** (hindari singkatan yang ambigu).
- Satu fungsi = satu tanggung jawab.

### 3.2 PHP

#### Docblock Wajib

Setiap file PHP harus memiliki block header yang menjelaskan tujuannya:

```php
<?php

/**
 * =================================================================
 * JUDUL FILE / KOMPONEN
 * =================================================================
 * Deskripsi singkat tentang file ini.
 *
 * Cara pakai:
 *   Contoh penggunaan (opsional, tapi sangat membantu)
 * =================================================================
 */
```

Setiap method publik harus memiliki PHPDoc:

```php
/**
 * Deskripsi singkat method
 *
 * @param int   $id
 * @param array $data ['name', 'price', 'stock']
 * @return bool
 */
public function update(int $id, array $data): bool
```

#### Gaya Penulisan PHP

```php
// ✅ BENAR — Type hints, return type, spacing konsisten
public function getById(int $id): array|false
{
    $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ❌ SALAH — Tanpa type hint, nama tidak jelas
public function get($x)
{
    $s = $this->pdo->prepare("SELECT * FROM products WHERE id = $x"); // SQL Injection!
    return $s->fetch();
}
```

**Aturan PHP:**

- Selalu gunakan **type hints** pada parameter dan return type.
- Selalu gunakan **prepared statements** (parameterized query) — jangan pernah interpolasi variabel langsung ke SQL.
- Gunakan `private` untuk property class, bukan `public`.
- Gunakan alignment (`=>`) yang rapi pada array asosiatif:

```php
$data = [
    'name'        => $_POST['name'],
    'price'       => $_POST['price'],
    'description' => $_POST['description'] ?? '',
];
```

- Gunakan section separator di file yang panjang:

```php
// ============================================================
// NAMA SECTION
// ============================================================
```

### 3.3 Controller

Setiap controller **harus** meng-extend `Controller.php`:

```php
<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Product.php';

class AdminProductController extends Controller
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index(): void
    {
        $products = $this->productModel->getAll();

        $this->view('admin/product/index', [
            'title'    => 'Daftar Produk',
            'products' => $products,
        ]);
    }
}
```

**Aturan Controller:**

- Deklarasi `require_once` Model di **atas class**, bukan di dalam method.
- Inisialisasi Model di `__construct()` sebagai `private` property.
- Gunakan `$this->view()` untuk render view, `$this->redirect()` untuk redirect.
- Panggil `verifyCsrf()` di **awal** setiap method POST (`store`, `update`).
- Gunakan `flash('success', '...')` atau `flash('error', '...')` sebelum redirect.

### 3.4 Model

```php
class Product
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    // Method-method CRUD di sini...
}
```

**Aturan Model:**

- Satu model = satu tabel database.
- Koneksi via `getConnection()` (singleton dari `config/database.php`).
- Semua query menggunakan **prepared statements**.
- Method yang me-return data list → return type `array`.
- Method yang me-return satu row → return type `array|false`.
- Method yang melakukan write → return type `bool`.

### 3.5 View

View adalah file PHP murni yang hanya berisi HTML + PHP template tag:

```php
<!-- ============================================================ -->
<!-- JUDUL HALAMAN -->
<!-- ============================================================ -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Daftar Produk</h2>
    <a href="/admin/product/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
    </a>
</div>
```

**Aturan View:**

- Gunakan **alternative syntax** PHP di view (`if/endif`, `foreach/endforeach`):
  ```php
  <?php if ($condition): ?>
      <p>Konten</p>
  <?php endif; ?>
  ```
- Selalu escape output dengan `e()` → `<?= e($product['name']) ?>`
- Gunakan `formatRupiah()` untuk angka mata uang → `<?= formatRupiah($product['price']) ?>`
- Gunakan Bootstrap class untuk layout, **jangan tulis inline style**.
- Setiap view dimulai dengan komentar HTML section header.

### 3.6 Routes

Format pendaftaran route di `routes.php`:

```php
$routes = [
    // ============================================================
    // NAMA SECTION (contoh: ADMIN, KASIR)
    // ============================================================
    '/url/path'         => ['SubFolder/ControllerName', 'methodName'],
    '/url/path/action'  => ['SubFolder/ControllerName', 'action'],
];
```

- Gunakan alignment `=>` yang rapi.
- Kelompokkan route per role/fitur dengan section separator.

### 3.7 SQL / Database

```sql
CREATE TABLE IF NOT EXISTS nama_tabel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kolom_varchar VARCHAR(255) NOT NULL,
    kolom_decimal DECIMAL(12,2) NOT NULL DEFAULT 0,
    kolom_int INT NOT NULL DEFAULT 0,
    kolom_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Aturan Database:**

- Nama tabel: `snake_case`, **plural** (`products`, `users`, `transactions`).
- Nama kolom: `snake_case` (`product_id`, `total_price`, `created_at`).
- Setiap tabel wajib memiliki `id INT AUTO_INCREMENT PRIMARY KEY`.
- Gunakan `TIMESTAMP` untuk kolom waktu, bukan `DATETIME`.
- Untuk soft delete, tambahkan kolom `deleted_at TIMESTAMP DEFAULT NULL`.
- Engine: `InnoDB`. Charset: `utf8mb4`.
- Data sampel ditulis dengan komentar `-- Data Sampel {NamaTabel}`.

---

## 4. Panduan Desain UI / UX

### 4.1 Prinsip Dasar

1. **Desain harus terasa natural dan dibuat oleh manusia** — bukan seperti template AI. Hindari desain yang terlalu "sempurna" atau generik.
2. **Responsif** — semua halaman harus tampil baik di desktop, tablet, dan mobile. Gunakan grid system Bootstrap (`col-md-*`, `col-lg-*`).
3. **Aksesibel (WCAG 2.1 AA compliant)** — pastikan semua user, termasuk yang memiliki keterbatasan visual, bisa menggunakan aplikasi dengan nyaman.

### 4.2 Palet Warna

Gunakan **warna solid dan flat** — **JANGAN gunakan gradient** di manapun.

| Fungsi | Warna | Kode | Catatan |
|---|---|---|---|
| **Background body** | Abu-abu sangat terang | `#f8f9fa` | Netral, tidak melelahkan mata |
| **Navbar & Footer** | Gelap (Bootstrap `bg-dark`) | `#212529` | Kontras tinggi dengan konten |
| **Teks utama** | Hitam default | `#212529` | Contrast ratio ≥ 7:1 (AAA) |
| **Teks sekunder** | Abu-abu (Bootstrap `text-muted`) | `#6c757d` | Contrast ratio ≥ 4.5:1 (AA) |
| **Aksi utama** | Biru (Bootstrap `btn-primary`) | `#0d6efd` | Untuk tombol CTA utama |
| **Sukses** | Hijau (Bootstrap `bg-success`) | `#198754` | Stok cukup, pesan sukses |
| **Bahaya** | Merah (Bootstrap `bg-danger`) | `#dc3545` | Stok rendah, hapus, error |
| **Peringatan** | Kuning (Bootstrap `btn-warning`) | `#ffc107` | Tombol edit |
| **Info** | Biru muda (Bootstrap `bg-info`) | `#0dcaf0` | Informasi umum |

**Aturan Warna:**

- Semua kombinasi teks-background harus memenuhi **WCAG AA minimum** (contrast ratio ≥ 4.5:1 untuk teks normal, ≥ 3:1 untuk teks besar).
- **Jangan pernah** menggunakan warna sebagai satu-satunya cara menyampaikan informasi — selalu sertakan teks atau ikon.
- **Jangan gunakan gradient** pada background, tombol, atau elemen apapun. Gunakan warna solid.

### 4.3 Tipografi

- Gunakan **font default Bootstrap** (system font stack) — jangan tambahkan Google Fonts custom.
- Hierarki heading yang jelas:
  - `<h2>` untuk judul halaman utama
  - `<h5>`, `<h6>` untuk judul card / section
  - `<p>` dengan `text-muted` untuk deskripsi
- Gunakan `fw-bold`, `fw-semibold` untuk penekanan, bukan ALL CAPS.
- Ukuran font biarkan default Bootstrap — jangan override kecuali benar-benar perlu.

### 4.4 Komponen UI

#### Card

```html
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Konten -->
    </div>
</div>
```

- Selalu gunakan `border-0 shadow-sm` — bersih dan ringan.
- Jangan gunakan warna border yang mencolok.

#### Tabel

```html
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                ...
            </thead>
        </table>
    </div>
</div>
```

- Bungkus tabel dalam card + `table-responsive`.
- Header tabel: `table-dark`.
- Gunakan `table-hover` untuk interaktivitas.

#### Form

```html
<form method="POST" action="/admin/product/store">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" class="form-control" id="name" name="name"
               value="<?= old('name') ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Simpan
    </button>
</form>
```

- Selalu sertakan `<?= csrf_field() ?>` di dalam form.
- Gunakan `old('field')` untuk mempertahankan input setelah validasi gagal.
- Setiap input harus memiliki `<label>` yang terhubung via `for`/`id`.

#### Alert / Flash Message

Flash message sudah di-handle otomatis di `layouts/main.php`. Cukup panggil:

```php
flash('success', 'Pesan sukses');
flash('error', 'Pesan error');
```

#### Tombol

| Aksi | Class | Ikon |
|---|---|---|
| Tambah / Simpan | `btn btn-primary` | `bi-plus-lg` / `bi-check-lg` |
| Edit | `btn btn-sm btn-warning` | `bi-pencil` |
| Hapus | `btn btn-sm btn-danger` | `bi-trash` |
| Kembali | `btn btn-secondary` | `bi-arrow-left` |

- Tombol hapus harus ada konfirmasi: `onclick="return confirm('Yakin?')"`
- Selalu sertakan ikon Bootstrap Icons di tombol.

### 4.5 Aksesibilitas (WCAG)

| Aspek | Aturan |
|---|---|
| **Kontras warna** | Minimum 4.5:1 untuk teks normal, 3:1 untuk teks besar (≥18pt) |
| **Label form** | Setiap `<input>` harus punya `<label>` yang terhubung |
| **Alt text** | Setiap `<img>` harus punya atribut `alt` yang deskriptif |
| **Bahasa** | Set `<html lang="id">` |
| **Keyboard navigation** | Semua elemen interaktif harus bisa diakses via keyboard |
| **ARIA** | Gunakan `role="alert"` pada flash message |
| **Focus visible** | Jangan hapus outline default browser pada fokus elemen |

### 4.6 Hal yang DILARANG

- ❌ **Gradient** — pada background, tombol, atau elemen apapun.
- ❌ **Inline style** — semua styling via class Bootstrap atau `style.css`.
- ❌ **Custom font** — gunakan system font stack Bootstrap.
- ❌ **Animasi berlebihan** — hanya `transition` ringan pada card hover (`transform 0.2s ease`).
- ❌ **Warna neon / terlalu terang** — gunakan palet Bootstrap standar.
- ❌ **Desain yang "terlalu sempurna"** — jaga agar tetap terasa natural dan fungsional, bukan showcase template.
- ❌ **Shadow yang berat** — gunakan `shadow-sm` saja, bukan `shadow-lg`.

---

## 5. Security

| Aspek | Implementasi |
|---|---|
| **XSS** | Selalu escape output dengan `e()` atau `htmlspecialchars()` |
| **SQL Injection** | Selalu gunakan prepared statements (`?` placeholder) |
| **CSRF** | Sertakan `csrf_field()` di form, panggil `verifyCsrf()` di controller |
| **Password** | Hash dengan `password_hash()`, verifikasi dengan `password_verify()` |
| **Environment** | Data sensitif di `.env`, file ini **tidak boleh di-commit** |

---

## 6. Cara Menjalankan Project

```bash
# 1. Clone repository
git clone <repo-url>

# 2. Copy file environment
cp .env.example .env

# 3. Jalankan Docker
docker compose up -d --build

# 4. Import database
#    Buka phpMyAdmin di http://localhost:8080
#    Import file database/pos_db.sql

# 5. Akses aplikasi
#    http://localhost:3000
```

### Port Default

| Service | Port | URL |
|---|---|---|
| Aplikasi PHP | 3000 | `http://localhost:3000` |
| phpMyAdmin | 8080 | `http://localhost:8080` |
| MariaDB | 3306 | — |

---

## 7. Helper Functions yang Tersedia

Semua fungsi ini tersedia secara global (di-load otomatis via `public/index.php`):

| Fungsi | Kegunaan | Contoh |
|---|---|---|
| `redirect($url)` | Redirect ke URL lain | `redirect('/admin/product')` |
| `url($path)` | Full URL utk href/action | `url('/login/authenticate')` → `http://localhost/pos/public/login/authenticate` |
| `asset($path)` | Generate URL asset | `asset('css/style.css')` → `/pos/public/assets/css/style.css` |
| `flash($key, $msg)` | Simpan flash message | `flash('success', 'Berhasil!')` |
| `getFlash($key)` | Ambil flash message | `getFlash('success')` |
| `old($key, $default)` | Ambil input sebelumnya | `old('name')` |
| `keepOldInput()` | Simpan semua POST input | `keepOldInput()` |
| `csrf_field()` | Generate hidden CSRF input | `<?= csrf_field() ?>` |
| `verifyCsrf()` | Verifikasi CSRF token | `verifyCsrf()` |
| `formatRupiah($angka)` | Format ke Rupiah | `formatRupiah(25000)` → `Rp 25.000` |
| `e($value)` | Escape HTML (anti-XSS) | `e($userInput)` |
| `env($key, $default)` | Ambil env variable | `env('DB_HOST', 'localhost')` |
| `getConnection()` | Ambil koneksi PDO | `$pdo = getConnection()` |

---

## 8. Checklist Sebelum Commit

- [ ] Kode bersih, tidak ada `var_dump()`, `die()`, atau debug statement.
- [ ] Semua output di-escape dengan `e()`.
- [ ] Semua form memiliki `csrf_field()`.
- [ ] Semua query menggunakan prepared statements.
- [ ] View menggunakan alternative syntax (`if/endif`).
- [ ] Tidak ada inline style di HTML.
- [ ] Tidak ada gradient di CSS.
- [ ] Contrast ratio warna memenuhi WCAG AA.
- [ ] Semua `<input>` memiliki `<label>`.
- [ ] Route sudah didaftarkan di `routes.php`.
- [ ] File `.env` tidak ter-commit.
