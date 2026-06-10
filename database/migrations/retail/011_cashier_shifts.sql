CREATE TABLE cashier_shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    kasir_id BIGINT UNSIGNED NOT NULL,
    opening_cash DECIMAL(15,2) NOT NULL DEFAULT 0,
    closing_cash DECIMAL(15,2) NULL,
    expected_cash DECIMAL(15,2) NULL,
    cash_difference DECIMAL(15,2) NULL,
    total_transactions INT UNSIGNED NOT NULL DEFAULT 0,
    total_revenue DECIMAL(15,2) NOT NULL DEFAULT 0,
    opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    note TEXT NULL,
    UNIQUE KEY uq_cashier_shifts_store_id_id (store_id, id),
    INDEX idx_cashier_shifts_store_id (store_id),
    INDEX idx_cashier_shifts_kasir_id (kasir_id),
    INDEX idx_cashier_shifts_status (status),
    INDEX idx_cashier_shifts_created_at (opened_at),
    INDEX idx_cashier_shifts_store_created (store_id, opened_at),
    CONSTRAINT fk_cashier_shifts_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cashier_shifts_kasir_scope FOREIGN KEY (store_id, kasir_id)
        REFERENCES users(store_id, id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_shift_cash CHECK (
        opening_cash >= 0 AND (closing_cash IS NULL OR closing_cash >= 0)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
