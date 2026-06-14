ALTER TABLE products ADD COLUMN IF NOT EXISTS store_id INT NULL AFTER id;
UPDATE products SET store_id = 1 WHERE store_id IS NULL;
ALTER TABLE products MODIFY store_id INT NOT NULL;
ALTER TABLE products ADD INDEX IF NOT EXISTS products_store_status_idx (store_id, status, deleted_at);

ALTER TABLE transactions ADD COLUMN IF NOT EXISTS store_id INT NULL AFTER id;
UPDATE transactions t
INNER JOIN users u ON u.id = t.cashier_id
SET t.store_id = u.store_id
WHERE t.store_id IS NULL;
ALTER TABLE transactions MODIFY store_id INT NOT NULL;
ALTER TABLE transactions ADD INDEX IF NOT EXISTS transactions_store_created_idx (store_id, created_at);

ALTER TABLE transaction_items ADD COLUMN IF NOT EXISTS store_id INT NULL AFTER id;
UPDATE transaction_items ti
INNER JOIN transactions t ON t.id = ti.transaction_id
SET ti.store_id = t.store_id
WHERE ti.store_id IS NULL;
ALTER TABLE transaction_items MODIFY store_id INT NOT NULL;
ALTER TABLE transaction_items ADD INDEX IF NOT EXISTS transaction_items_store_transaction_idx (store_id, transaction_id);

ALTER TABLE stock_movements ADD COLUMN IF NOT EXISTS store_id INT NULL AFTER id;
UPDATE stock_movements sm
INNER JOIN users u ON u.id = sm.user_id
SET sm.store_id = u.store_id
WHERE sm.store_id IS NULL;
UPDATE stock_movements sm
INNER JOIN products p ON p.id = sm.product_id
SET sm.store_id = p.store_id
WHERE sm.store_id IS NULL;
ALTER TABLE stock_movements MODIFY store_id INT NOT NULL;
ALTER TABLE stock_movements ADD INDEX IF NOT EXISTS stock_movements_store_product_idx (store_id, product_id, created_at);

ALTER TABLE products
    ADD CONSTRAINT products_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE transactions
    ADD CONSTRAINT transactions_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE transaction_items
    ADD CONSTRAINT transaction_items_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
ALTER TABLE stock_movements
    ADD CONSTRAINT stock_movements_store_fk FOREIGN KEY (store_id) REFERENCES tenants(id) ON DELETE RESTRICT;
