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
    store_id INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    assigned_admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    UNIQUE KEY users_store_id_id_unique (store_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    UNIQUE KEY products_store_id_id_unique (store_id, id),
    INDEX products_store_status_idx (store_id, status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- TRANSACTION TABLES
-- ==========================================

-- Tabel Transactions (Header)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    transaction_code VARCHAR(50) NOT NULL,
    cashier_id INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    change_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_method ENUM('cash', 'qris', 'card') NOT NULL DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY transactions_store_id_id_unique (store_id, id),
    UNIQUE KEY transactions_store_code_unique (store_id, transaction_code),
    INDEX transactions_store_created_idx (store_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE transactions
    ADD COLUMN IF NOT EXISTS paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER total_price,
    ADD COLUMN IF NOT EXISTS change_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER paid_amount;

-- Tabel Transaction Items (Detail)
CREATE TABLE IF NOT EXISTS transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    transaction_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX transaction_items_store_transaction_idx (store_id, transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY settings_store_key_unique (store_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'sale', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    stock_before INT NOT NULL,
    stock_after INT NOT NULL,
    note VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX stock_movements_store_product_idx (store_id, product_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    expense_date DATE NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX expenses_store_date_idx (store_id, expense_date, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cashier_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    kasir_id INT NOT NULL,
    opened_at DATETIME NOT NULL,
    closed_at DATETIME DEFAULT NULL,
    opening_cash DECIMAL(12,2) NOT NULL DEFAULT 0,
    closing_cash DECIMAL(12,2) DEFAULT NULL,
    total_transactions INT NOT NULL DEFAULT 0,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX cashier_shifts_store_kasir_status_idx (store_id, kasir_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    store_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    target_table VARCHAR(100) NOT NULL,
    target_id VARCHAR(100) DEFAULT NULL,
    old_value JSON DEFAULT NULL,
    new_value JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX audit_logs_store_created_idx (store_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Canonical tenant and cross-store constraints.
ALTER TABLE users
    ADD CONSTRAINT users_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE products
    ADD CONSTRAINT products_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE transactions
    ADD CONSTRAINT transactions_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT transactions_store_cashier_fk
        FOREIGN KEY (store_id, cashier_id) REFERENCES users(store_id, id) ON DELETE RESTRICT;
ALTER TABLE transaction_items
    ADD CONSTRAINT transaction_items_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT transaction_items_store_transaction_fk
        FOREIGN KEY (store_id, transaction_id) REFERENCES transactions(store_id, id) ON DELETE RESTRICT;
ALTER TABLE stock_movements
    ADD CONSTRAINT stock_movements_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT stock_movements_store_product_fk
        FOREIGN KEY (store_id, product_id) REFERENCES products(store_id, id) ON DELETE RESTRICT,
    ADD CONSTRAINT stock_movements_store_user_fk
        FOREIGN KEY (store_id, user_id) REFERENCES users(store_id, id) ON DELETE RESTRICT;
ALTER TABLE expenses
    ADD CONSTRAINT expenses_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT expenses_created_by_fk FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT;
ALTER TABLE cashier_shifts
    ADD CONSTRAINT cashier_shifts_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT cashier_shifts_store_kasir_fk
        FOREIGN KEY (store_id, kasir_id) REFERENCES users(store_id, id) ON DELETE RESTRICT;
ALTER TABLE settings
    ADD CONSTRAINT settings_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE audit_logs
    ADD CONSTRAINT audit_logs_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT,
    ADD CONSTRAINT audit_logs_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT;

ALTER TABLE expenses
    ADD INDEX IF NOT EXISTS expenses_store_created_idx (store_id, created_at);

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL UNIQUE,
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO migrations (filename) VALUES
('001_add_store_scope.sql'),
('002_create_operational_security_tables.sql'),
('003_scope_settings.sql'),
('004_add_cross_store_constraints.sql'),
('005_enforce_tenant_foreign_keys.sql'),
('006_add_canonical_indexes.sql'),
('007_remove_redundant_foreign_keys.sql');

INSERT IGNORE INTO tenants (id, name, status) VALUES (1, 'Default Store', 'active');

INSERT IGNORE INTO settings (store_id, setting_key, setting_value) VALUES
(1, 'store_name', 'POS App'),
(1, 'store_address', 'Jl. Toko No. 3, Indonesia'),
(1, 'store_logo', ''),
(1, 'currency_symbol', 'Rp'),
(1, 'tax_percentage', '0'),
(1, 'receipt_footer', 'Terima kasih sudah berbelanja.'),
(1, 'system_timezone', 'Asia/Jakarta');