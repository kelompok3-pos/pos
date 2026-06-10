CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NULL,
    role ENUM('superadmin', 'admin', 'kasir') NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_store_id_id (store_id, id),
    INDEX idx_users_store_id (store_id),
    INDEX idx_users_status (status),
    INDEX idx_users_created_at (created_at),
    INDEX idx_users_store_created (store_id, created_at),
    CONSTRAINT fk_users_store FOREIGN KEY (store_id) REFERENCES stores(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_users_store_role CHECK (
        (role = 'superadmin' AND store_id IS NULL) OR
        (role IN ('admin', 'kasir') AND store_id IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
