-- قم بتنفيذ هذا الكود في phpMyAdmin أو أي أداة إدارة قواعد البيانات
CREATE DATABASE IF NOT EXISTS auto_bbs;
USE auto_bbs;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image TEXT NOT NULL,
    description TEXT,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إضافة بعض المنتجات الافتراضية
INSERT INTO products (name, price, image, description, category) VALUES
('BARRE DE TOIE DOBLO', '12000', 'images/porte-babage-doblo.jpg', 'قضبان السقف لدوبلو - مثالية لنقل الأمتعة والأغراض الكبيرة', 'interior'),
('ELARGISSUR DOBLO', '14000', 'images/elargisseur-doblo.jpeg', 'موسعات دوبلو - تزيد من مساحة التحميل بشكل ملحوظ', 'interior');