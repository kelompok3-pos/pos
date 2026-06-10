CREATE TABLE restock_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    quantity INT UNSIGNED NOT NULL,
    purchase_price DECIMAL(15,2) NOT NULL,
    total_cost DECIMAL(15,2) NOT NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_restock_logs_store_id (store_id),
    INDEX idx_restock_logs_product_id (product_id),
    INDEX idx_restock_logs_created_at (created_at),
    INDEX idx_restock_logs_store_created (store_id, created_at),
    CONSTRAINT fk_restock_logs_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_restock_logs_supplier_scope FOREIGN KEY (store_id, supplier_id)
        REFERENCES suppliers(store_id, id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_restock_logs_product_scope FOREIGN KEY (store_id, product_id)
        REFERENCES products(store_id, id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_restock_logs_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_restock_cost CHECK (purchase_price >= 0 AND total_cost >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
