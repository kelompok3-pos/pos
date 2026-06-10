USE pos_db;

ALTER TABLE users
    ADD UNIQUE INDEX IF NOT EXISTS users_store_id_id_unique (store_id, id);
ALTER TABLE products
    ADD UNIQUE INDEX IF NOT EXISTS products_store_id_id_unique (store_id, id);
ALTER TABLE transactions
    ADD UNIQUE INDEX IF NOT EXISTS transactions_store_id_id_unique (store_id, id);

ALTER TABLE transactions DROP INDEX transaction_code;
ALTER TABLE transactions
    ADD UNIQUE INDEX IF NOT EXISTS transactions_store_code_unique (store_id, transaction_code),
    ADD CONSTRAINT transactions_store_cashier_fk
        FOREIGN KEY (store_id, cashier_id) REFERENCES users(store_id, id);

ALTER TABLE transaction_items
    ADD CONSTRAINT transaction_items_store_transaction_fk
        FOREIGN KEY (store_id, transaction_id) REFERENCES transactions(store_id, id);

ALTER TABLE stock_movements
    ADD CONSTRAINT stock_movements_store_product_fk
        FOREIGN KEY (store_id, product_id) REFERENCES products(store_id, id),
    ADD CONSTRAINT stock_movements_store_user_fk
        FOREIGN KEY (store_id, user_id) REFERENCES users(store_id, id);
