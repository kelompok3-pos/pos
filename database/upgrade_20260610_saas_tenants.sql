CREATE TABLE IF NOT EXISTS tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tenants (id, name, status, created_by)
SELECT DISTINCT u.store_id, CONCAT('Tenant ', u.store_id), 'active', NULL
FROM users u
WHERE u.store_id IS NOT NULL
  AND u.store_id > 0
  AND NOT EXISTS (SELECT 1 FROM tenants t WHERE t.id = u.store_id);

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS assigned_admin_id INT DEFAULT NULL AFTER created_by;

UPDATE users cashier
SET cashier.assigned_admin_id = (
    SELECT admin_user.id
    FROM users admin_user
    WHERE admin_user.role = 'admin'
      AND admin_user.store_id = cashier.store_id
      AND admin_user.deleted_at IS NULL
    ORDER BY admin_user.id ASC
    LIMIT 1
)
WHERE cashier.role = 'kasir'
  AND cashier.assigned_admin_id IS NULL;

ALTER TABLE users
    ADD INDEX IF NOT EXISTS users_store_role_index (store_id, role),
    ADD INDEX IF NOT EXISTS users_assigned_admin_index (assigned_admin_id);
