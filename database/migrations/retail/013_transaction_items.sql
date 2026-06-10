CREATE TABLE transaction_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(180) NOT NULL,
    product_sku VARCHAR(80) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL,
    INDEX idx_transaction_items_store_id (store_id),
    INDEX idx_transaction_items_transaction_id (transaction_id),
    INDEX idx_transaction_items_product_id (product_id),
    INDEX idx_transaction_items_created_at (transaction_id, id),
    INDEX idx_transaction_items_store_created (store_id, transaction_id),
    CONSTRAINT fk_transaction_items_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_transaction_items_transaction_scope FOREIGN KEY (store_id, transaction_id)
        REFERENCES transactions(store_id, id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_transaction_items_product_scope FOREIGN KEY (store_id, product_id)
        REFERENCES products(store_id, id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_transaction_items_amounts CHECK (
        unit_price >= 0 AND quantity > 0 AND discount_amount >= 0 AND subtotal >= 0
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
