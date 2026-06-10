CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_scope_name (store_id, name),
    UNIQUE KEY uq_categories_store_id_id (store_id, id),
    INDEX idx_categories_store_id (store_id),
    INDEX idx_categories_created_at (created_at),
    INDEX idx_categories_store_created (store_id, created_at),
    CONSTRAINT fk_categories_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
