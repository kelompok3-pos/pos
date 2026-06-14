ALTER TABLE users
    ADD COLUMN IF NOT EXISTS created_by INT DEFAULT NULL AFTER store_id;

ALTER TABLE products
    ADD COLUMN IF NOT EXISTS minimum_stock INT NOT NULL DEFAULT 5 AFTER stock,
    ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') NOT NULL DEFAULT 'active' AFTER minimum_stock;

ALTER TABLE transactions
    ADD COLUMN IF NOT EXISTS subtotal DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER cashier_id,
    ADD COLUMN IF NOT EXISTS tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER subtotal,
    ADD COLUMN IF NOT EXISTS payment_method ENUM('cash', 'qris', 'card') NOT NULL DEFAULT 'cash' AFTER change_amount;

UPDATE transactions SET subtotal = total_price WHERE subtotal = 0;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('store_name', 'POS App'),
('store_address', 'Jl. Toko No. 3, Indonesia'),
('store_logo', ''),
('currency_symbol', 'Rp'),
('tax_percentage', '0'),
('receipt_footer', 'Terima kasih sudah berbelanja.'),
('system_timezone', 'Asia/Jakarta');

CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'sale', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    stock_before INT NOT NULL,
    stock_after INT NOT NULL,
    note VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
