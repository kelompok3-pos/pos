-- Existing canonical indexes already cover products, transactions,
-- stock_movements, and cashier_shifts by left-most prefix.
ALTER TABLE expenses
    ADD INDEX IF NOT EXISTS expenses_store_created_idx (store_id, created_at);
