USE pos_db;

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS store_id INT DEFAULT NULL AFTER role;

UPDATE users
SET store_id = 1
WHERE role IN ('admin', 'kasir')
  AND store_id IS NULL;
