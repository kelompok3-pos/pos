ALTER TABLE settings ADD COLUMN IF NOT EXISTS store_id INT NULL AFTER id;
UPDATE settings SET store_id = 1 WHERE store_id IS NULL;
ALTER TABLE settings DROP INDEX IF EXISTS setting_key;
ALTER TABLE settings MODIFY store_id INT NOT NULL;
ALTER TABLE settings ADD UNIQUE INDEX IF NOT EXISTS settings_store_key_unique (store_id, setting_key);
ALTER TABLE settings ADD CONSTRAINT settings_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
