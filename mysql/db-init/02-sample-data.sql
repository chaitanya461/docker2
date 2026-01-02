USE phone_store;

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'admin@phonestore.com', 'Admin User', 'admin'),
('john_doe', '$2y$10$AnotherHashedPassword', 'john@example.com', 'John Doe', 'customer'),
('jane_smith', '$2y$10$AnotherHashedPassword2', 'jane@example.com', 'Jane Smith', 'customer');

-- Insert categories
INSERT INTO categories (name, slug, description) VALUES
('Smartphones', 'smartphones', 'Latest smartphones from top brands'),
('Feature Phones', 'feature-phones', 'Basic phones with essential features'),
('Accessories', 'accessories', 'Phone cases, chargers, headphones and more'),
('Tablets', 'tablets', 'Tablets and iPads'),
('Wearables', 'wearables', 'Smart watches and fitness trackers');

-- Insert sample products
INSERT INTO products (name, slug, description, brand, model, price, discount_price, category_id, stock_quantity, image_url, is_featured, specifications) VALUES
('iPhone 15 Pro', 'iphone-15-pro', 'Latest iPhone with A17 Pro chip, Titanium design, and advanced camera system', 'Apple', 'iPhone 15 Pro', 999.99, 949.99, 1, 50, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=600', TRUE, '{"display": "6.1-inch Super Retina XDR", "processor": "A17 Pro", "ram": "8GB", "storage": "128GB/256GB/512GB/1TB", "camera": "48MP Main + 12MP Ultra Wide + 12MP Telephoto", "battery": "3650mAh", "os": "iOS 17"}'),
('Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Flagship Samsung phone with S Pen and advanced AI features', 'Samsung', 'Galaxy S24 Ultra', 1299.99, 1199.99, 1, 35, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=600', TRUE, '{"display": "6.8-inch Dynamic AMOLED 2X", "processor": "Snapdragon 8 Gen 3", "ram": "12GB", "storage": "256GB/512GB/1TB", "camera": "200MP Main + 12MP Ultra Wide + 10MP Telephoto x2", "battery": "5000mAh", "os": "Android 14"}'),
('Google Pixel 8 Pro', 'google-pixel-8-pro', 'Best camera phone with Google AI features', 'Google', 'Pixel 8 Pro', 999.00, 899.00, 1, 40, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=600', TRUE, '{"display": "6.7-inch LTPO OLED", "processor": "Google Tensor G3", "ram": "12GB", "storage": "128GB/256GB/512GB", "camera": "50MP Main + 48MP Ultra Wide + 48MP Telephoto", "battery": "5050mAh", "os": "Android 14"}'),
('OnePlus 12', 'oneplus-12', 'Flagship killer with fast charging', 'OnePlus', '12', 799.99, 749.99, 1, 60, 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=600', FALSE, '{"display": "6.82-inch LTPO AMOLED", "processor": "Snapdragon 8 Gen 3", "ram": "12GB/16GB", "storage": "256GB/512GB", "camera": "50MP Main + 48MP Ultra Wide + 64MP Periscope Telephoto", "battery": "5400mAh", "os": "OxygenOS 14"}'),
('Xiaomi 14 Pro', 'xiaomi-14-pro', 'High-performance phone with Leica cameras', 'Xiaomi', '14 Pro', 899.99, NULL, 1, 45, 'https://images.unsplash.com/photo-1598327105854-c8674faddf74?auto=format&fit=crop&w=600', FALSE, '{"display": "6.73-inch LTPO AMOLED", "processor": "Snapdragon 8 Gen 3", "ram": "12GB/16GB", "storage": "256GB/512GB/1TB", "camera": "50MP Main + 50MP Ultra Wide + 50MP Telephoto", "battery": "4880mAh", "os": "HyperOS"}'),
('AirPods Pro (2nd Gen)', 'airpods-pro-2nd-gen', 'Wireless earbuds with active noise cancellation', 'Apple', 'AirPods Pro 2', 249.99, 229.99, 3, 100, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=600', FALSE, '{"type": "True Wireless", "connectivity": "Bluetooth 5.3", "battery_life": "6 hours (30 with case)", "features": "Active Noise Cancellation, Transparency Mode, Spatial Audio"}'),
('Samsung Galaxy Tab S9', 'samsung-galaxy-tab-s9', 'Premium Android tablet with S Pen', 'Samsung', 'Galaxy Tab S9', 849.99, 799.99, 4, 25, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=600', FALSE, '{"display": "11-inch Dynamic AMOLED 2X", "processor": "Snapdragon 8 Gen 2", "ram": "8GB/12GB", "storage": "128GB/256GB", "battery": "8400mAh", "os": "Android 13"}'),
('Apple Watch Series 9', 'apple-watch-series-9', 'Advanced smartwatch with health features', 'Apple', 'Watch Series 9', 399.99, 379.99, 5, 75, 'https://images.unsplash.com/photo-1579586337278-3f9a7d3ea5a7?auto=format&fit=crop&w=600', FALSE, '{"display": "Always-On Retina", "size": "41mm/45mm", "features": "ECG, Blood Oxygen, Fall Detection", "battery": "18 hours", "water_resistance": "50m"}'),
('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Premium noise cancelling headphones', 'Sony', 'WH-1000XM5', 399.99, 349.99, 3, 50, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600', FALSE, '{"type": "Over-ear", "connectivity": "Bluetooth 5.2", "noise_cancelling": "Yes", "battery_life": "30 hours", "weight": "250g"}'),
('Anker PowerCore 26800', 'anker-powercore-26800', 'High-capacity power bank', 'Anker', 'PowerCore 26800', 79.99, 69.99, 3, 200, 'https://images.unsplash.com/photo-1527613426441-4da17471b66d?auto=format&fit=crop&w=600', FALSE, '{"capacity": "26800mAh", "output": "60W", "ports": "2x USB-C, 1x USB-A", "input": "USB-C", "weight": "570g"}');

-- Insert sample cart items
INSERT INTO cart (user_id, product_id, quantity) VALUES
(2, 1, 1),
(2, 3, 2),
(3, 2, 1);

-- Insert sample reviews
INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(2, 1, 5, 'Amazing phone! The camera quality is outstanding.'),
(3, 1, 4, 'Great phone but battery could be better.'),
(2, 3, 5, 'Best Android phone I have ever used!'),
(3, 5, 4, 'Excellent value for money.');

-- Insert sample wishlist items
INSERT INTO wishlist (user_id, product_id) VALUES
(2, 4),
(2, 6),
(3, 7),
(3, 8);
