CREATE DATABASE IF NOT EXISTS inventaris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

DROP TABLE IF EXISTS stock_logs;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE stock_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    action VARCHAR(20) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    note VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name) VALUES
('Aksesoris'),
('Display'),
('Komputer'),
('Jaringan'),
('Penyimpanan');

INSERT INTO suppliers (name, phone) VALUES
('PT Sumber Elektronik', '081234567801'),
('CV Mitra Komputer', '081234567802'),
('PT Jaya Teknologi', '081234567803'),
('UD Berkah Gadget', '081234567804'),
('PT Cahaya Digital', '081234567805');

INSERT INTO products (name, category_id, supplier_id, price, stock) VALUES
('Laptop ASUS Vivobook', 3, 1, 8500000, 12),
('Mouse Wireless Logitech', 1, 2, 150000, 45),
('Monitor LED 24 Inch', 2, 3, 1750000, 18),
('Keyboard Mechanical', 1, 2, 450000, 30),
('Router WiFi 6', 4, 4, 900000, 20),
('SSD NVME 512GB', 5, 5, 700000, 25),
('Webcam Full HD', 1, 1, 350000, 15);
