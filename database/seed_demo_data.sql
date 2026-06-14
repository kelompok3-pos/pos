START TRANSACTION;

-- All demo accounts use password: Demo123!
SET @demo_password = '$2y$10$uHlWJqcx0f9w54IXtrRHAuVrIip8N1OrYyUyrrlKnCSMwCzxvzOeS';

INSERT INTO users (name, email, password, role, store_id)
SELECT 'Super Admin Demo', 'superadmin@demo.pos', @demo_password, 'super_admin', NULL
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'superadmin@demo.pos');

SET @superadmin_id = (SELECT id FROM users WHERE email = 'superadmin@demo.pos' LIMIT 1);

INSERT INTO tenants (name, status, created_by)
SELECT 'Demo Mart Jakarta', 'active', @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM tenants WHERE name = 'Demo Mart Jakarta');
INSERT INTO tenants (name, status, created_by)
SELECT 'Demo Mart Bandung', 'active', @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM tenants WHERE name = 'Demo Mart Bandung');
INSERT INTO tenants (name, status, created_by)
SELECT 'Demo Mart Surabaya', 'active', @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM tenants WHERE name = 'Demo Mart Surabaya');

SET @store_jkt = (SELECT id FROM tenants WHERE name = 'Demo Mart Jakarta' LIMIT 1);
SET @store_bdg = (SELECT id FROM tenants WHERE name = 'Demo Mart Bandung' LIMIT 1);
SET @store_sby = (SELECT id FROM tenants WHERE name = 'Demo Mart Surabaya' LIMIT 1);

INSERT INTO users (name, email, password, role, store_id, created_by)
SELECT 'Admin Demo Jakarta', 'admin.jakarta@demo.pos', @demo_password, 'admin', @store_jkt, @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin.jakarta@demo.pos');
INSERT INTO users (name, email, password, role, store_id, created_by)
SELECT 'Admin Demo Bandung', 'admin.bandung@demo.pos', @demo_password, 'admin', @store_bdg, @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin.bandung@demo.pos');
INSERT INTO users (name, email, password, role, store_id, created_by)
SELECT 'Admin Demo Surabaya', 'admin.surabaya@demo.pos', @demo_password, 'admin', @store_sby, @superadmin_id
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin.surabaya@demo.pos');

SET @admin_jkt = (SELECT id FROM users WHERE email = 'admin.jakarta@demo.pos');
SET @admin_bdg = (SELECT id FROM users WHERE email = 'admin.bandung@demo.pos');
SET @admin_sby = (SELECT id FROM users WHERE email = 'admin.surabaya@demo.pos');

INSERT INTO users (name, email, password, role, store_id, created_by, assigned_admin_id)
SELECT 'Kasir Pagi Jakarta', 'kasir1.jakarta@demo.pos', @demo_password, 'kasir', @store_jkt, @admin_jkt, @admin_jkt
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kasir1.jakarta@demo.pos');
INSERT INTO users (name, email, password, role, store_id, created_by, assigned_admin_id)
SELECT 'Kasir Sore Jakarta', 'kasir2.jakarta@demo.pos', @demo_password, 'kasir', @store_jkt, @admin_jkt, @admin_jkt
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kasir2.jakarta@demo.pos');
INSERT INTO users (name, email, password, role, store_id, created_by, assigned_admin_id)
SELECT 'Kasir Bandung', 'kasir.bandung@demo.pos', @demo_password, 'kasir', @store_bdg, @admin_bdg, @admin_bdg
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kasir.bandung@demo.pos');
INSERT INTO users (name, email, password, role, store_id, created_by, assigned_admin_id)
SELECT 'Kasir Surabaya', 'kasir.surabaya@demo.pos', @demo_password, 'kasir', @store_sby, @admin_sby, @admin_sby
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kasir.surabaya@demo.pos');

SET @kasir_jkt1 = (SELECT id FROM users WHERE email = 'kasir1.jakarta@demo.pos');
SET @kasir_jkt2 = (SELECT id FROM users WHERE email = 'kasir2.jakarta@demo.pos');
SET @kasir_bdg = (SELECT id FROM users WHERE email = 'kasir.bandung@demo.pos');
SET @kasir_sby = (SELECT id FROM users WHERE email = 'kasir.surabaya@demo.pos');

INSERT INTO products (store_id, name, price, stock, minimum_stock, status, description)
SELECT s.id, p.name, p.price, p.stock, p.minimum_stock, 'active', CONCAT('DEMO - ', p.description)
FROM (
    SELECT @store_jkt id UNION ALL SELECT @store_bdg UNION ALL SELECT @store_sby
) s
CROSS JOIN (
    SELECT 'Beras Premium 5kg' name, 75000 price, 28 stock, 8 minimum_stock, 'Beras pulen pilihan' description
    UNION ALL SELECT 'Minyak Goreng 1L', 21000, 42, 10, 'Minyak goreng kemasan'
    UNION ALL SELECT 'Mi Instan Goreng', 3500, 95, 20, 'Mi instan favorit'
    UNION ALL SELECT 'Air Mineral 600ml', 4000, 120, 24, 'Air mineral botol'
    UNION ALL SELECT 'Kopi Susu Kaleng', 9000, 34, 10, 'Minuman kopi siap minum'
    UNION ALL SELECT 'Roti Tawar', 18000, 7, 8, 'Roti tawar harian'
) p
WHERE NOT EXISTS (
    SELECT 1 FROM products existing
    WHERE existing.store_id = s.id AND existing.name = p.name AND existing.deleted_at IS NULL
);

INSERT INTO settings (store_id, setting_key, setting_value)
SELECT s.id, cfg.setting_key, cfg.setting_value
FROM (SELECT @store_jkt id UNION ALL SELECT @store_bdg UNION ALL SELECT @store_sby) s
CROSS JOIN (
    SELECT 'tax_percentage' setting_key, '11' setting_value
    UNION ALL SELECT 'currency_symbol', 'Rp'
    UNION ALL SELECT 'receipt_footer', 'Terima kasih telah berbelanja di Demo Mart.'
) cfg
WHERE 1 = 1
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO expenses (store_id, category, amount, description, expense_date, created_by)
SELECT s.store_id, e.category, e.amount, e.description, DATE_SUB(CURDATE(), INTERVAL e.days_ago DAY), s.admin_id
FROM (
    SELECT @store_jkt store_id, @admin_jkt admin_id
    UNION ALL SELECT @store_bdg, @admin_bdg
    UNION ALL SELECT @store_sby, @admin_sby
) s
CROSS JOIN (
    SELECT 'operational' category, 350000 amount, 'DEMO - listrik dan internet' description, 2 days_ago
    UNION ALL SELECT 'purchase', 1250000, 'DEMO - pembelian stok mingguan', 5
    UNION ALL SELECT 'salary', 850000, 'DEMO - insentif pegawai', 8
    UNION ALL SELECT 'other', 175000, 'DEMO - perawatan toko', 12
) e
WHERE NOT EXISTS (
    SELECT 1 FROM expenses x WHERE x.store_id = s.store_id AND x.description = e.description
);

INSERT INTO cashier_shifts (store_id, kasir_id, opened_at, closed_at, opening_cash, closing_cash, total_transactions, status)
SELECT @store_jkt, @kasir_jkt1, DATE_SUB(NOW(), INTERVAL 9 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR), 500000, 1845000, 8, 'closed'
WHERE NOT EXISTS (SELECT 1 FROM cashier_shifts WHERE store_id = @store_jkt AND kasir_id = @kasir_jkt1 AND opening_cash = 500000);
INSERT INTO cashier_shifts (store_id, kasir_id, opened_at, opening_cash, total_transactions, status)
SELECT @store_jkt, @kasir_jkt2, DATE_SUB(NOW(), INTERVAL 2 HOUR), 400000, 3, 'open'
WHERE NOT EXISTS (SELECT 1 FROM cashier_shifts WHERE store_id = @store_jkt AND kasir_id = @kasir_jkt2 AND status = 'open');
INSERT INTO cashier_shifts (store_id, kasir_id, opened_at, closed_at, opening_cash, closing_cash, total_transactions, status)
SELECT @store_bdg, @kasir_bdg, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 18 HOUR), 400000, 1520000, 7, 'closed'
WHERE NOT EXISTS (SELECT 1 FROM cashier_shifts WHERE store_id = @store_bdg AND kasir_id = @kasir_bdg AND opening_cash = 400000);
INSERT INTO cashier_shifts (store_id, kasir_id, opened_at, closed_at, opening_cash, closing_cash, total_transactions, status)
SELECT @store_sby, @kasir_sby, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 42 HOUR), 450000, 1760000, 9, 'closed'
WHERE NOT EXISTS (SELECT 1 FROM cashier_shifts WHERE store_id = @store_sby AND kasir_id = @kasir_sby AND opening_cash = 450000);

INSERT INTO transactions
    (store_id, transaction_code, cashier_id, subtotal, tax_amount, total_price, paid_amount, change_amount, payment_method, created_at)
SELECT stores.store_id, CONCAT('DEMO-', stores.code, '-', LPAD(days.n, 3, '0')),
       stores.kasir_id, 50000 + (days.n * 12500), 0, 50000 + (days.n * 12500),
       CASE WHEN days.n % 3 = 0 THEN 100000 + (days.n * 12500) ELSE 50000 + (days.n * 12500) END,
       CASE WHEN days.n % 3 = 0 THEN 50000 ELSE 0 END,
       CASE WHEN days.n % 3 = 0 THEN 'cash' WHEN days.n % 3 = 1 THEN 'qris' ELSE 'card' END,
       DATE_SUB(NOW(), INTERVAL days.n DAY)
FROM (
    SELECT @store_jkt store_id, @kasir_jkt1 kasir_id, 'JKT' code
    UNION ALL SELECT @store_bdg, @kasir_bdg, 'BDG'
    UNION ALL SELECT @store_sby, @kasir_sby, 'SBY'
) stores
CROSS JOIN (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
    UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) days
WHERE NOT EXISTS (
    SELECT 1 FROM transactions t
    WHERE t.store_id = stores.store_id AND t.transaction_code = CONCAT('DEMO-', stores.code, '-', LPAD(days.n, 3, '0'))
);

UPDATE cashier_shifts shift_row
SET shift_row.total_transactions = (
    SELECT COUNT(*)
    FROM transactions transaction_row
    WHERE transaction_row.store_id = shift_row.store_id
      AND transaction_row.cashier_id = shift_row.kasir_id
      AND transaction_row.transaction_code LIKE 'DEMO-%'
)
WHERE shift_row.kasir_id IN (@kasir_jkt1, @kasir_jkt2, @kasir_bdg, @kasir_sby);

INSERT INTO transaction_items (store_id, transaction_id, product_name, quantity, subtotal, created_at)
SELECT t.store_id, t.id, 'Mi Instan Goreng', 2, 7000, t.created_at
FROM transactions t
WHERE t.transaction_code LIKE 'DEMO-%'
AND NOT EXISTS (SELECT 1 FROM transaction_items i WHERE i.transaction_id = t.id AND i.product_name = 'Mi Instan Goreng');
INSERT INTO transaction_items (store_id, transaction_id, product_name, quantity, subtotal, created_at)
SELECT t.store_id, t.id, 'Air Mineral 600ml', 3, 12000, t.created_at
FROM transactions t
WHERE t.transaction_code LIKE 'DEMO-%'
AND NOT EXISTS (SELECT 1 FROM transaction_items i WHERE i.transaction_id = t.id AND i.product_name = 'Air Mineral 600ml');
INSERT INTO transaction_items (store_id, transaction_id, product_name, quantity, subtotal, created_at)
SELECT t.store_id, t.id, 'Beras Premium 5kg', 1, t.total_price - 19000, t.created_at
FROM transactions t
WHERE t.transaction_code LIKE 'DEMO-%'
AND NOT EXISTS (SELECT 1 FROM transaction_items i WHERE i.transaction_id = t.id AND i.product_name = 'Beras Premium 5kg');

INSERT INTO stock_movements (store_id, product_id, user_id, movement_type, quantity, stock_before, stock_after, note, created_at)
SELECT p.store_id, p.id, u.id, 'in', 40, p.stock, p.stock + 40, 'DEMO - restock awal', DATE_SUB(NOW(), INTERVAL 10 DAY)
FROM products p
JOIN users u ON u.store_id = p.store_id AND u.role = 'admin' AND u.email LIKE 'admin.%@demo.pos'
WHERE p.description LIKE 'DEMO - %'
AND NOT EXISTS (SELECT 1 FROM stock_movements sm WHERE sm.product_id = p.id AND sm.note = 'DEMO - restock awal');
INSERT INTO stock_movements (store_id, product_id, user_id, movement_type, quantity, stock_before, stock_after, note, created_at)
SELECT p.store_id, p.id, u.id, 'sale', 3, p.stock + 3, p.stock, 'DEMO - penjualan sample', DATE_SUB(NOW(), INTERVAL 1 DAY)
FROM products p
JOIN users u ON u.store_id = p.store_id AND u.role = 'kasir' AND u.email LIKE '%@demo.pos'
WHERE p.description LIKE 'DEMO - %'
AND NOT EXISTS (SELECT 1 FROM stock_movements sm WHERE sm.product_id = p.id AND sm.note = 'DEMO - penjualan sample')
GROUP BY p.id;

INSERT INTO audit_logs (store_id, user_id, action, target_table, target_id, new_value, ip_address, created_at)
SELECT s.store_id, s.admin_id, a.action, a.target_table, CONCAT('DEMO-', a.target_id),
       JSON_OBJECT('source', 'demo_seed', 'description', a.description), '127.0.0.1',
       DATE_SUB(NOW(), INTERVAL a.hours_ago HOUR)
FROM (
    SELECT @store_jkt store_id, @admin_jkt admin_id
    UNION ALL SELECT @store_bdg, @admin_bdg
    UNION ALL SELECT @store_sby, @admin_sby
) s
CROSS JOIN (
    SELECT 'CREATE' action, 'products' target_table, 'PRODUCT' target_id, 'Produk demo dibuat' description, 20 hours_ago
    UNION ALL SELECT 'UPDATE', 'stock_movements', 'STOCK', 'Stok demo diperbarui', 12
    UNION ALL SELECT 'CREATE', 'expenses', 'EXPENSE', 'Pengeluaran demo dicatat', 6
) a
WHERE NOT EXISTS (
    SELECT 1 FROM audit_logs l
    WHERE l.store_id = s.store_id AND l.target_id = CONCAT('DEMO-', a.target_id)
);

COMMIT;
