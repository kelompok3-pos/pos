CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    category ENUM('operational', 'purchase', 'salary', 'other') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT NULL,
    expense_date DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_expenses_store_id (store_id),
    INDEX idx_expenses_category (category),
    INDEX idx_expenses_created_at (created_at),
    INDEX idx_expenses_store_created (store_id, created_at),
    INDEX idx_expenses_store_date (store_id, expense_date),
    CONSTRAINT fk_expenses_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_expenses_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_expenses_amount CHECK (amount > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
