-- Migrations 001-004 create all other tenant and cross-store relationships.
-- Users are the only store-owned table whose tenant FK is not created earlier.
ALTER TABLE users
    ADD CONSTRAINT users_store_fk
        FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
