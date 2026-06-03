CREATE DATABASE IF NOT EXISTS pos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_db;

-- ==========================================
-- DATA SAMPLE USERS
-- ==========================================
-- Password: admin123

-- Tabel Users (Authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Products
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

-- ==========================================
-- TRANSACTION TABLES
-- ==========================================

-- Tabel Transactions (Header)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(50) NOT NULL UNIQUE,
    cashier_id INT NOT NULL,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    change_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE transactions
    ADD COLUMN IF NOT EXISTS paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER total_price,
    ADD COLUMN IF NOT EXISTS change_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER paid_amount;

-- Tabel Transaction Items (Detail)
CREATE TABLE IF NOT EXISTS transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- DATA SAMPLE
-- ==========================================

-- Sample Users (Password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@pos.com', '$2y$10$uXyT468XOMe2NhglAnjCP.uJpJtbYUNeFDw8lmx.MXKU15gS94/Uu', 'super_admin'),
('Kasir', 'kasir@pos.com', '$2y$10$uXyT468XOMe2NhglAnjCP.uJpJtbYUNeFDw8lmx.MXKU15gS94/Uu', 'kasir');

-- Sample Products
INSERT INTO products (name, price, stock, description) VALUES
('Kopi Arabica', 25000, 100, 'Kopi arabica premium dari Toraja'),
('Teh Hijau', 15000, 50, 'Teh hijau organik'),
('Roti Gandum', 18000, 75, 'Roti gandum segar'),
('Susu Segar', 12000, 200, 'Susu segar 1 liter'),
('Air Mineral', 5000, 500, 'Air mineral 600ml');

-- Sample Transactions (Simulate sales from past months)
INSERT INTO transactions (id, transaction_code, cashier_id, total_price, paid_amount, change_amount, created_at) VALUES
(1, 'TRX-202603-001', 2, 65000, 70000, 5000, '2026-03-15 10:00:00'),
(2, 'TRX-202604-001', 2, 35000, 50000, 15000, '2026-04-10 11:30:00'),
(3, 'TRX-202605-001', 2, 120000, 150000, 30000, NOW());

-- Sample Transaction Items
INSERT INTO transaction_items (transaction_id, product_name, quantity, subtotal) VALUES
(1, 'Kopi Arabica', 2, 50000),
(1, 'Teh Hijau', 1, 15000),
(2, 'Roti Gandum', 1, 18000),
(2, 'Susu Segar', 1, 12000),
(2, 'Air Mineral', 1, 5000),
(3, 'Kopi Arabica', 4, 100000),
(3, 'Susu Segar', 1, 12000),
(3, 'Air Mineral', 2, 10000);
