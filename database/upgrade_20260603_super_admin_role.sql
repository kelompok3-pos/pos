USE pos_db;

ALTER TABLE users
    MODIFY role ENUM('super_admin', 'admin', 'kasir') NOT NULL DEFAULT 'kasir';

UPDATE users
SET role = 'super_admin'
WHERE email = 'admin@pos.com'
  AND deleted_at IS NULL;
