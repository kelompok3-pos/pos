-- Composite foreign keys enforce both record identity and tenant ownership.
-- Remove older single-column relationships that duplicate or conflict with them.
ALTER TABLE transactions
    DROP FOREIGN KEY IF EXISTS transactions_ibfk_1;
ALTER TABLE transaction_items
    DROP FOREIGN KEY IF EXISTS transaction_items_ibfk_1;
ALTER TABLE stock_movements
    DROP FOREIGN KEY IF EXISTS stock_movements_ibfk_1;
ALTER TABLE stock_movements
    DROP FOREIGN KEY IF EXISTS stock_movements_ibfk_2;
