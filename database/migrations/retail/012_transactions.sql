CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    kasir_id BIGINT UNSIGNED NOT NULL,
    payment_method_id BIGINT UNSIGNED NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    total DECIMAL(15,2) NOT NULL,
    cash_received DECIMAL(15,2) NULL,
    change_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('completed', 'voided') NOT NULL DEFAULT 'completed',
    void_reason VARCHAR(255) NULL,
    voided_by BIGINT UNSIGNED NULL,
    voided_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_transactions_store_id_id (store_id, id),
    INDEX idx_transactions_store_id (store_id),
    INDEX idx_transactions_shift_id (shift_id),
    INDEX idx_transactions_kasir_id (kasir_id),
    INDEX idx_transactions_status (status),
    INDEX idx_transactions_created_at (created_at),
    INDEX idx_transactions_store_created (store_id, created_at),
    CONSTRAINT fk_transactions_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_transactions_shift_scope FOREIGN KEY (store_id, shift_id)
        REFERENCES cashier_shifts(store_id, id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_kasir_scope FOREIGN KEY (store_id, kasir_id)
        REFERENCES users(store_id, id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_payment_method FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_voided_by FOREIGN KEY (voided_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_transactions_amounts CHECK (
        subtotal >= 0 AND discount_amount >= 0 AND tax_amount >= 0 AND total >= 0 AND change_amount >= 0
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
