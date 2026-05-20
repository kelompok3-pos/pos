CREATE DATABASE IF NOT EXISTS pos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_db;

-- Tabel Users (Authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Produk
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Transaksi Utama
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) NOT NULL UNIQUE,
    id_kasir INT NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kasir) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Detail Transaksi (Diperlukan oleh dashboard.php)
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    nama_produk VARCHAR(255) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ==========================================
-- DATA SAMPEL (DUMMY DATA UNTUK DASHBOARD)
-- ==========================================

-- Data Sampel Users (Password: admin123)
INSERT INTO users (nama, email, password, role) VALUES
('Rizky Admin', 'admin@pos.com', '$2y$10$uXyT468XOMe2NhglAnjCP.uJpJtbYUNeFDw8lmx.MXKU15gS94/Uu', 'admin'),
('Moses', 'kasir@pos.com', '$2y$10$uXyT468XOMe2NhglAnjCP.uJpJtbYUNeFDw8lmx.MXKU15gS94/Uu', 'kasir');

-- Data Sampel Produk
INSERT INTO products (name, price, stock, description) VALUES
('Kopi Arabica', 25000, 100, 'Kopi arabica premium dari Toraja'),
('Teh Hijau', 15000, 50, 'Teh hijau organik'),
('Roti Gandum', 18000, 75, 'Roti gandum segar'),
('Susu Segar', 12000, 200, 'Susu segar 1 liter'),
('Air Mineral', 5000, 500, 'Air mineral 600ml');

-- Data Sampel Transaksi (Menimulasi Penjualan Beberapa Bulan Lalu untuk Grafik)
INSERT INTO transaksi (id, kode_transaksi, id_kasir, total_harga, created_at) VALUES
(1, 'TRX-202603-001', 2, 65000, '2026-03-15 10:00:00'),
(2, 'TRX-202604-001', 2, 35000, '2026-04-10 11:30:00'),
(3, 'TRX-202605-001', 2, 120000, NOW());

-- Data Sampel Detail Transaksi
INSERT INTO detail_transaksi (id_transaksi, nama_produk, jumlah, subtotal) VALUES
(1, 'Kopi Arabica', 2, 50000),
(1, 'Teh Hijau', 1, 15000),
(2, 'Roti Gandum', 1, 18000),
(2, 'Susu Segar', 1, 12000),
(2, 'Air Mineral', 1, 5000),
(3, 'Kopi Arabica', 4, 100000),
(3, 'Susu Segar', 1, 12000),
(3, 'Air Mineral', 2, 10000);