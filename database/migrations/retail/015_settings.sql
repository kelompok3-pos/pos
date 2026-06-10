CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_store_key (store_id, setting_key),
    INDEX idx_settings_store_id (store_id),
    INDEX idx_settings_created_at (created_at),
    INDEX idx_settings_store_created (store_id, created_at),
    CONSTRAINT fk_settings_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (store_id, setting_key, setting_value) VALUES
(1, 'store_name', 'Toko Utama'),
(1, 'tax_enabled', '0'),
(1, 'tax_percentage', '0'),
(1, 'receipt_footer', 'Terima kasih telah berbelanja.');
