# Kelompok 3 - Point of Sale (POS)

Aplikasi Point of Sale (POS) sederhana menggunakan PHP Native

## Yang Dibutuhkan

Pilih **salah satu** dari dua cara berikut:

|               | Docker                                                            | XAMPP                                                          |
| ------------- | ----------------------------------------------------------------- | -------------------------------------------------------------- |
| **Install**   | [Docker Desktop](https://www.docker.com/products/docker-desktop/) | [XAMPP](https://www.apachefriends.org/download.html) (PHP 8.x) |
| **Kelebihan** | Setup otomatis, konsisten di semua OS                             | Familiar untuk pemula, ringan                                  |
| **Database**  | Otomatis di-setup via container                                   | Perlu import manual via phpMyAdmin                             |

---

## Cara Menjalankan (Docker)

```bash
# 1. Clone repository
git clone https://github.com/kelompok3-pos/pos.git
cd pos

# 2. Copy file environment
cp .env.example .env

# 3. Jalankan dengan Docker
docker compose up -d

# 4. Buka di browser
# Aplikasi  : http://localhost:3000
# phpMyAdmin: http://localhost:8080 (user: user, password: user)
```

### Perintah Docker Lainnya

```bash
# Hentikan semua container
docker compose down

# Lihat log aplikasi
docker compose logs -f php-app

# Lihat log database
docker compose logs -f db

# Rebuild setelah mengubah Dockerfile
docker compose up -d --build

# Reset database (hapus volume, data akan hilang!)
docker compose down -v
docker compose up -d
```

---

## Cara Menjalankan (XAMPP)

### Langkah 1: Install & Jalankan XAMPP

1. Download dan install [XAMPP](https://www.apachefriends.org/download.html) (pilih versi PHP 8.x)
2. Buka **XAMPP Control Panel**
3. Start **Apache** dan **MySQL**

### Langkah 2: Letakkan Project

Clone atau copy folder project ke dalam folder `htdocs` XAMPP:

```
# Windows
C:\xampp\htdocs\POS\

# macOS
/Applications/XAMPP/htdocs/POS/

# Linux
/opt/lampp/htdocs/POS/
```

### Langkah 3: Setup Database

1. Buka **phpMyAdmin** di browser: `http://localhost/phpmyadmin`
2. Klik tab **"Import"**
3. Pilih file `database/pos_db.sql` dari folder project
4. Klik **"Go"** / **"Import"** untuk menjalankan SQL

> Atau buat database manual:
>
> 1. Klik **"New"** di sidebar phpMyAdmin
> 2. Buat database dengan nama `pos_db`
> 3. Pilih tab **"SQL"**, copy-paste isi file `database/pos_db.sql`, lalu klik **"Go"**

### Langkah 4: Setup Environment

1. Copy file `.env.example` menjadi `.env`
2. Ubah konfigurasi database sesuai XAMPP:

```env
APP_NAME="POS App"
APP_ENV=local
APP_URL=http://localhost/POS/public

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan konfigurasi MySQL/MariaDB di XAMPP kamu.

> **Catatan:** XAMPP secara default menggunakan user `root` tanpa password untuk MySQL.

### Langkah 5: Setup Virtual Host (Opsional tapi Direkomendasikan)

Agar URL lebih bersih (tanpa `/POS/public`), setup Virtual Host:

**a. Edit file `httpd-vhosts.conf`:**

```
# Windows: C:\xampp\apache\conf\extra\httpd-vhosts.conf
# macOS:   /Applications/XAMPP/etc/extra/httpd-vhosts.conf
# Linux:   /opt/lampp/etc/extra/httpd-vhosts.conf
```

Tambahkan di akhir file:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/POS/public"
    ServerName pos.local

    <Directory "C:/xampp/htdocs/POS/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

> Sesuaikan path `DocumentRoot` dan `Directory` dengan lokasi project kamu.

**b. Edit file `hosts`:**

```
# Windows: C:\Windows\System32\drivers\etc\hosts
# macOS/Linux: /etc/hosts
```

Tambahkan baris:

```
127.0.0.1    pos.local
```

**c. Restart Apache** di XAMPP Control Panel.

**d. Akses** `http://pos.local` di browser.

### Langkah 6: Buka di Browser

| Metode                  | URL                           |
| ----------------------- | ----------------------------- |
| **Tanpa Virtual Host**  | `http://localhost/POS/public` |
| **Dengan Virtual Host** | `http://pos.local`            |
| **phpMyAdmin**          | `http://localhost/phpmyadmin` |

### Troubleshooting XAMPP

**Halaman blank atau error 500:**

- Pastikan `mod_rewrite` aktif. Buka `httpd.conf` dan pastikan baris ini **tidak** diawali `#`:
  ```apache
  LoadModule rewrite_module modules/mod_rewrite.so
  ```
- Restart Apache setelah mengubah konfigurasi.

**URL routing tidak berfungsi (404):**

- Pastikan file `public/.htaccess` ada dan isinya benar.
- Pastikan `AllowOverride All` sudah diset di konfigurasi Apache.

**Database connection refused:**

- Pastikan MySQL sudah running di XAMPP Control Panel.
- Pastikan `.env` menggunakan `DB_HOST=localhost` (bukan `db`).
- Pastikan `DB_PASSWORD=` kosong (default XAMPP tanpa password).

---

## Struktur Folder

```
POS/
├── public/                         # Document root (web server)
│   ├── index.php                   # Entry point — semua request masuk ke sini
│   ├── .htaccess                   # URL rewriting
│   └── assets/                     # File statis (CSS, JS)
│
├── routes.php                      # Daftar semua route (URL → Controller)
│
├── app/                            # Kode aplikasi MVC
│   ├── Controllers/                # Controller (menerima request)
│   │   ├── Controller.php          # Base controller (parent class)
│   │   ├── HomeController.php      # Controller untuk dashboard
│   │   ├── Admin/                  # Group: Admin
│   │   │   └── AdminProductController.php
│   │   └── Kasir/                  # Group: Kasir
│   │       ├── KasirProductController.php
│   │       └── KasirTransactionController.php
│   │
│   ├── Models/                     # Model (akses database)
│   │   ├── Product.php
│   │   └── Transaction.php
│   │
│   └── Views/                      # View (tampilan HTML)
│       ├── layouts/main.php        # Layout utama (header + footer)
│       ├── home/index.php          # Halaman dashboard
│       ├── admin/product/          # View untuk admin produk
│       └── kasir/                  # View untuk kasir
│
├── config/                         # Konfigurasi
│   ├── config.php                  # Load .env & konstanta
│   └── database.php                # Koneksi database PDO
│
├── helpers/                        # Fungsi bantuan
│   └── functions.php               # redirect(), flash(), csrf, dll
│
├── database/                       # SQL schema
│   └── pos_db.sql                  # Full database schema
│
├── Dockerfile                      # Image PHP + Apache
├── docker-compose.yml              # Docker untuk development
├── .env.example                    # Template environment variables
└── .gitignore
```

## 📖 Cara Menambah Module Baru

Contoh: Membuat module **Category** untuk role **Admin**.

### Langkah 1: Buat Model

Buat file `app/Models/Category.php`:

```php
<?php

class Category
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        return $stmt->execute([$data['name']]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
```

### Langkah 2: Buat Controller

Buat file `app/Controllers/Admin/AdminCategoryController.php`:

```php
<?php

require_once __DIR__ . '/../Controller.php';
require_once BASE_PATH . '/app/Models/Category.php';

class AdminCategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAll();
        $this->view('admin/category/index', [
            'title'      => 'Daftar Kategori',
            'categories' => $categories,
        ]);
    }

    public function create(): void
    {
        $this->view('admin/category/create', [
            'title' => 'Tambah Kategori',
        ]);
    }

    public function store(): void
    {
        verifyCsrf();
        $this->categoryModel->create(['name' => $_POST['name']]);
        flash('success', 'Kategori berhasil ditambahkan!');
        $this->redirect('/admin/category');
    }

    // ... tambahkan edit, update, delete seperti ProductController
}
```

### Langkah 3: Buat Views

Buat folder `app/Views/admin/category/` lalu buat file:

- `index.php` — Tabel list kategori
- `create.php` — Form tambah kategori
- `edit.php` — Form edit kategori

### Langkah 4: Daftarkan Route

Tambahkan di `routes.php`:

```php
// === ADMIN - KATEGORI ===
'/admin/category'           => ['Admin/AdminCategoryController', 'index'],
'/admin/category/create'    => ['Admin/AdminCategoryController', 'create'],
'/admin/category/store'     => ['Admin/AdminCategoryController', 'store'],
'/admin/category/edit'      => ['Admin/AdminCategoryController', 'edit'],
'/admin/category/update'    => ['Admin/AdminCategoryController', 'update'],
'/admin/category/delete'    => ['Admin/AdminCategoryController', 'delete'],
```

### Langkah 5: Buat Tabel di Database

```sql
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Selesai!** Buka `http://localhost:3000/admin/category` untuk melihat hasilnya.

---

## Arsitektur MVC

```
Browser → public/index.php → routes.php → Controller → Model → Database
                                                     ↘ View → HTML Response → Browser
```

### Penjelasan:

1. **Browser** mengirim request ke URL (misal: `/admin/product`)
2. **index.php** menerima request, load konfigurasi & routes
3. **routes.php** mencocokkan URL dengan Controller yang sesuai
4. **Controller** memproses request:
   - Memanggil **Model** untuk ambil/simpan data dari database
   - Memanggil **View** untuk menampilkan HTML
5. **View** menampilkan data dalam format HTML
6. **Browser** menampilkan hasilnya ke user

### Aturan Penamaan Controller (dalam subfolder):

| File                                           | Class Name               | Di routes.php                    |
| ---------------------------------------------- | ------------------------ | -------------------------------- |
| `Controllers/HomeController.php`               | `HomeController`         | `'HomeController'`               |
| `Controllers/Admin/AdminProductController.php` | `AdminProductController` | `'Admin/AdminProductController'` |
| `Controllers/Kasir/KasirProductController.php` | `KasirProductController` | `'Kasir/KasirProductController'` |

**Pola:** Nama class = `{Role}{Module}Controller`, Path di routes = `{Folder}/{NamaClass}`

---

## Environment Variables

| Variable      | Default (Docker)      | Default (XAMPP)   | Keterangan                             |
| ------------- | --------------------- | ----------------- | -------------------------------------- |
| `APP_NAME`    | POS App               | POS App           | Nama aplikasi (tampil di navbar)       |
| `APP_ENV`     | local                 | local             | Environment: `local` atau `production` |
| `APP_URL`     | http://localhost:3000  | http://localhost/POS/public | Base URL aplikasi          |
| `DB_HOST`     | db                    | localhost         | Hostname database                      |
| `DB_PORT`     | 3306                  | 3306              | Port database                          |
| `DB_DATABASE` | root_db               | pos_db            | Nama database                          |
| `DB_USERNAME` | user                  | root              | Username database                      |
| `DB_PASSWORD` | user                  | *(kosong)*        | Password database                      |

---

## Helper Functions

| Fungsi              | Contoh                              | Keterangan                                |
| ------------------- | ----------------------------------- | ----------------------------------------- |
| `redirect($url)`    | `redirect('/admin/product')`        | Redirect ke URL lain                      |
| `flash($key, $msg)` | `flash('success', 'Berhasil!')`     | Simpan flash message                      |
| `getFlash($key)`    | `getFlash('success')`               | Ambil flash message                       |
| `old($key)`         | `old('name')`                       | Ambil input lama (setelah validasi gagal) |
| `csrf_field()`      | `<?= csrf_field() ?>`               | Generate hidden CSRF token di form        |
| `verifyCsrf()`      | `verifyCsrf()`                      | Validasi CSRF token                       |
| `formatRupiah($n)`  | `formatRupiah(25000)` → `Rp 25.000` | Format angka ke Rupiah                    |
| `e($str)`           | `e($userInput)`                     | Escape HTML (cegah XSS)                   |
| `asset($path)`      | `asset('css/style.css')`            | Generate path ke file asset               |

---

## Troubleshooting

### Container tidak mau start

```bash
# Cek log error
docker compose logs -f

# Rebuild image
docker compose down
docker compose up -d --build
```

### Port sudah dipakai

Ubah port di `docker-compose.yml`:

```yaml
ports:
  - "8888:80" # Ganti 3000 ke 8888
```

### Database tidak bisa terkoneksi

- Pastikan container `db` sudah running: `docker compose ps`
- Pastikan `.env` menggunakan `DB_HOST=db` (bukan `localhost`)
- Cek log MySQL: `docker compose logs -f db`

### Data hilang setelah docker compose down

Data MySQL disimpan di Docker volume `db_data`. Jika menjalankan `docker compose down -v`, volume akan terhapus beserta datanya.

---
