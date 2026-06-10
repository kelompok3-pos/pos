CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    type ENUM('restock', 'adjustment', 'sale', 'void') NOT NULL,
    quantity_before INT NOT NULL,
    quantity_change INT NOT NULL,
    quantity_after INT NOT NULL,
    reason VARCHAR(255) NULL,
    reference_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_stock_movements_store_id (store_id),
    INDEX idx_stock_movements_product_id (product_id),
    INDEX idx_stock_movements_type (type),
    INDEX idx_stock_movements_created_at (created_at),
    INDEX idx_stock_movements_store_created (store_id, created_at),
    CONSTRAINT fk_stock_movements_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_stock_movements_product_scope FOREIGN KEY (store_id, product_id)
        REFERENCES products(store_id, id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_stock_movements_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_stock_movement_quantities CHECK (quantity_before >= 0 AND quantity_after >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
