CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_suppliers_store_id_id (store_id, id),
    INDEX idx_suppliers_store_id (store_id),
    INDEX idx_suppliers_created_at (created_at),
    INDEX idx_suppliers_store_created (store_id, created_at),
    CONSTRAINT fk_suppliers_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
