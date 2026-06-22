-- =====================================================
-- SEED DATA untuk Database SIA (Sales Intelligence Agent)
-- TEMA: KOMPONEN KOMPUTER
-- =====================================================

-- =====================================================
-- HAPUS DATA LAMA (jika ingin reset)
-- =====================================================
TRUNCATE discount_recommendations, deadstock_analyses, transaction_items, transactions, stock_requests, products, users RESTART IDENTITY CASCADE;

-- =====================================================
-- USERS (3 data)
-- =====================================================
INSERT INTO users (name, email, password, role) VALUES
('Admin Utama',    'admin@gmail.com',   'hashedpassword', 'admin'),
('Manager Retail', 'manager@gmail.com', 'hashedpassword', 'manager'),
('Kasir Toko',     'cashier@gmail.com', 'hashedpassword', 'cashier');

-- =====================================================
-- PRODUCTS (10 data)
-- =====================================================
INSERT INTO products (name, category, price, stock, status) VALUES
('Intel Core i9-14900K',             'Processor',   10500000,  20, 'active'),
('AMD Ryzen 7 7800X3D',              'Processor',   7500000,   15, 'active'),
('NVIDIA GeForce RTX 4090 24GB',     'VGA',         35000000,  5,  'active'),
('ASUS ROG Strix B650-A Gaming',     'Motherboard', 5200000,   12, 'active'),
('Corsair Vengeance RGB 32GB DDR5',  'RAM',         2800000,   30, 'active'),
('Samsung 990 PRO 2TB NVMe',         'SSD',         3500000,   25, 'active'),
('Seasonic Vertex GX-1000 1000W',    'PSU',         3200000,   18, 'active'),
('Lian Li O11 Dynamic EVO',          'Casing',      2500000,   8,  'active'),
('NZXT Kraken Elite 360 RGB',        'Cooler',      4500000,   10, 'active'),
('Thermal Grizzly Kryonaut 1g',      'Aksesoris',   150000,    0,  'active');

-- =====================================================
-- TRANSACTIONS (10 data, tersebar beberapa bulan)
-- =====================================================
INSERT INTO transactions (cashier_id, transaction_date, total) VALUES
-- Bulan Juni 2026 (bulan ini)
(3, '2026-06-20 09:15:00', 45500000), -- trx 1
(3, '2026-06-19 14:30:00', 15500000), -- trx 2
(3, '2026-06-18 10:45:00', 70000000), -- trx 3
(3, '2026-06-15 16:20:00', 10200000), -- trx 4
(3, '2026-06-10 11:00:00', 7000000),  -- trx 5
-- Bulan Mei 2026 (bulan lalu)
(3, '2026-05-28 09:30:00', 10800000), -- trx 6
(3, '2026-05-20 13:15:00', 9100000),  -- trx 7
(3, '2026-05-10 15:45:00', 12700000), -- trx 8
-- Bulan April 2026
(3, '2026-04-15 10:00:00', 6400000),  -- trx 9
-- Bulan Maret 2026
(3, '2026-03-22 14:00:00', 5000000);  -- trx 10

-- =====================================================
-- TRANSACTION ITEMS (detail item per transaksi)
-- =====================================================

-- Transaksi 1 (Juni 20 - total 45,500,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(1, 3, 1, 35000000, 35000000),  -- RTX 4090
(1, 1, 1, 10500000, 10500000);  -- i9-14900K

-- Transaksi 2 (Juni 19 - total 15,500,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(2, 2, 1, 7500000, 7500000),    -- Ryzen 7
(2, 4, 1, 5200000, 5200000),    -- ASUS Motherboard
(2, 5, 1, 2800000, 2800000);    -- Corsair RAM

-- Transaksi 3 (Juni 18 - total 70,000,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(3, 3, 2, 35000000, 70000000);  -- RTX 4090 x2

-- Transaksi 4 (Juni 15 - total 10,200,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(4, 6, 2, 3500000, 7000000),    -- Samsung SSD x2
(4, 7, 1, 3200000, 3200000);    -- Seasonic PSU

-- Transaksi 5 (Juni 10 - total 7,000,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(5, 8, 1, 2500000, 2500000),    -- Lian Li Casing
(5, 9, 1, 4500000, 4500000);    -- NZXT Cooler

-- Transaksi 6 (Mei 28 - total 10,800,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(6, 1, 1, 10500000, 10500000),  -- i9-14900K
(6, 10, 2, 150000, 300000);     -- Thermal Grizzly x2

-- Transaksi 7 (Mei 20 - total 9,100,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(7, 5, 2, 2800000, 5600000),    -- Corsair RAM x2
(7, 6, 1, 3500000, 3500000);    -- Samsung SSD

-- Transaksi 8 (Mei 10 - total 12,700,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(8, 4, 1, 5200000, 5200000),    -- ASUS Motherboard
(8, 2, 1, 7500000, 7500000);    -- Ryzen 7

-- Transaksi 9 (April 15 - total 6,400,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(9, 7, 2, 3200000, 6400000);    -- Seasonic PSU x2

-- Transaksi 10 (Maret 22 - total 5,000,000)
INSERT INTO transaction_items (transaction_id, product_id, qty, price, subtotal) VALUES
(10, 8, 2, 2500000, 5000000);   -- Lian Li Casing x2

-- =====================================================
-- STOCK REQUESTS (5 data)
-- =====================================================
INSERT INTO stock_requests (product_id, requested_by, approved_by, qty, status, request_date, approved_date) VALUES
(10, 3, 2, 50,  'approved', '2026-06-18 08:00:00', '2026-06-18 10:30:00'),
(8,  3, 2, 10,  'approved', '2026-06-17 09:00:00', '2026-06-17 14:00:00'),
(3,  3, NULL, 5, 'pending',  '2026-06-20 08:30:00', NULL),
(5,  3, 2, 20, 'approved', '2026-06-15 07:00:00', '2026-06-15 09:00:00'),
(1,  3, 1, 30,  'rejected', '2026-06-10 10:00:00', NULL);

-- =====================================================
-- DEADSTOCK ANALYSES (5 data)
-- =====================================================
INSERT INTO deadstock_analyses (product_id, last_sale_date, total_sales, stock_remaining, status, analysis_date) VALUES
(10, '2026-05-28 09:30:00', 2,   0,   'deadstock',  '2026-06-20 00:00:00'),
(4,  '2026-06-19 14:30:00', 2,   12,  'normal',     '2026-06-20 00:00:00'),
(9,  '2026-06-10 11:00:00', 1,   10,  'slow_moving','2026-06-20 00:00:00'),
(8,  '2026-06-10 11:00:00', 3,   8,   'slow_moving',     '2026-06-20 00:00:00'),
(2,  '2026-06-19 14:30:00', 2,   15,   'normal',    '2026-06-20 00:00:00');

-- =====================================================
-- DISCOUNT RECOMMENDATIONS (3 data)
-- =====================================================
INSERT INTO discount_recommendations (deadstock_analysis_id, discount_percent, recommendation_note) VALUES
(1, 10, 'Thermal paste habis terjual tetapi sebelumnya lambat. Tidak perlu diskon, cukup restock.'),
(3, 15, 'NZXT Cooler pergerakan penjualannya lambat. Diskon 15% atau jadikan paket bundling dengan Processor.'),
(4, 5, 'Casing Lian Li menumpuk. Diskon tipis 5% untuk menarik perakit PC.');

-- =====================================================
-- VERIFIKASI DATA
-- =====================================================
SELECT 'users' as tabel, COUNT(*) as jumlah FROM users
UNION ALL
SELECT 'products', COUNT(*) FROM products
UNION ALL
SELECT 'transactions', COUNT(*) FROM transactions
UNION ALL
SELECT 'transaction_items', COUNT(*) FROM transaction_items
UNION ALL
SELECT 'stock_requests', COUNT(*) FROM stock_requests
UNION ALL
SELECT 'deadstock_analyses', COUNT(*) FROM deadstock_analyses
UNION ALL
SELECT 'discount_recommendations', COUNT(*) FROM discount_recommendations;
