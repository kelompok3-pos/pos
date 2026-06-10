INSERT INTO stores (id, name, address, phone, owner_name, status)
VALUES (1, 'Toko Utama', 'Jl. Contoh No. 1, Jakarta', '021-5550001', 'Pemilik Toko', 'active');

INSERT INTO users (id, store_id, role, name, email, password, status) VALUES
(1, NULL, 'superadmin', 'Super Admin', 'superadmin@example.com', '$2y$10$4kJks6/MQVDxxAmRNweK6ed7UyARvJ.4dTOYe3fRcCJ2ANivIZwQi', 'active'),
(2, 1, 'admin', 'Admin Toko', 'admin@example.com', '$2y$10$X9uUcylrBsz2/taDm97MHOcOmVQlZvFC/4JYtO00n86hg/O0XHdke', 'active'),
(3, 1, 'kasir', 'Kasir Toko', 'kasir@example.com', '$2y$10$xEvc5smlDJ9nsgN7usDic.ZBsh7l6ODi6qsXYFOQQC9YCfof5iccG', 'active');

INSERT INTO categories (id, store_id, name, description) VALUES
(1, NULL, 'Makanan', 'Kategori global produk makanan'),
(2, NULL, 'Minuman', 'Kategori global produk minuman');

INSERT INTO payment_methods (id, name, type, is_active) VALUES
(1, 'Cash', 'cash', 1),
(2, 'QRIS', 'digital', 1),
(3, 'Debit', 'card', 1);

INSERT INTO products
    (store_id, category_id, name, sku, purchase_price, selling_price, stock, min_stock, unit, status)
VALUES
(1, 1, 'Beras Premium 5kg', 'MKN-0001', 65000, 72000, 20, 5, 'pack', 'active'),
(1, 1, 'Mi Instan Goreng', 'MKN-0002', 2600, 3500, 100, 20, 'pcs', 'active'),
(1, 1, 'Roti Tawar', 'MKN-0003', 13000, 16000, 25, 5, 'pcs', 'active'),
(1, 2, 'Air Mineral 600ml', 'MNM-0001', 2500, 4000, 120, 24, 'botol', 'active'),
(1, 2, 'Kopi Susu Kaleng', 'MNM-0002', 6500, 8500, 50, 10, 'kaleng', 'active');
