USE phone_store;

-- Use the correct database name (from your schema)
USE mydatabase;

-- Clear existing data (if needed)
DELETE FROM wishlist;
DELETE FROM reviews;
DELETE FROM cart;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM users;

-- Reset auto-increment counters
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;

-- Insert admin user (password: admin123)
-- Using the hash you provided: $2y$10$N9qo8uLOickgx2ZMRZoMye/.G6t7c6m8nX6tX7c1J7z6QbQ5qY5Wq
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMye/.G6t7c6m8nX6tX7c1J7z6QbQ5qY5Wq', 'admin@phonestore.com', 'Admin User', 'admin'),
  ('admin', 'admin', 'admin@phonestore.com', 'Admin User', 'admin'),
('john_doe', '$2y$10$N9qo8uLOickgx2ZMRZoMye/.G6t7c6m8nX6tX7c1J7z6QbQ5qY5Wq', 'john@example.com', 'John Doe', 'customer'),
('jane_smith', '$2y$10$N9qo8uLOickgx2ZMRZoMye/.G6t7c6m8nX6tX7c1J7z6QbQ5qY5Wq', 'jane@example.com', 'Jane Smith', 'customer');

-- Insert categories
INSERT INTO categories (name, slug, description) VALUES
('Smartphones', 'smartphones', 'Latest smartphones from top brands'),
('Feature Phones', 'feature-phones', 'Basic phones with essential features'),
('Accessories', 'accessories', 'Phone cases, chargers, headphones'),
('Tablets', 'tablets', 'Tablets and iPads'),
('Wearables', 'wearables', 'Smart watches and fitness trackers');

-- Insert sample products
INSERT INTO products (name, slug, description, brand, model, price, discount_price, category_id, stock_quantity, image_url, is_featured) VALUES
('iPhone 15 Pro Max', 'iphone-15-pro-max', 'Latest iPhone with A17 Pro chip', 'Apple', 'iPhone 15 Pro Max', 1199.99, 1099.99, 1, 25, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=600', TRUE),
('Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Flagship Samsung phone with S Pen', 'Samsung', 'Galaxy S24 Ultra', 1299.99, 1199.99, 1, 30, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=600', TRUE),
('Google Pixel 8 Pro', 'google-pixel-8-pro', 'Best camera phone with Google AI', 'Google', 'Pixel 8 Pro', 999.00, 899.00, 1, 40, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=600', TRUE),
('OnePlus 12', 'oneplus-12', 'Flagship killer with fast charging', 'OnePlus', '12', 799.99, 749.99, 1, 50, 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=600', FALSE),
('AirPods Pro', 'airpods-pro', 'Wireless earbuds with noise cancellation', 'Apple', 'AirPods Pro', 249.99, 229.99, 3, 100, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=600', FALSE),
('Samsung Galaxy Tab S9', 'samsung-galaxy-tab-s9', 'Premium Android tablet', 'Samsung', 'Galaxy Tab S9', 1199.99, 1099.99, 4, 20, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=600', FALSE);

-- Insert sample cart items (user_id 2 = john_doe, user_id 3 = jane_smith)
INSERT INTO cart (user_id, product_id, quantity) VALUES
(2, 1, 1),  -- john adds iPhone 15 Pro Max
(2, 3, 2),  -- john adds 2 Google Pixel 8 Pro
(3, 2, 1);  -- jane adds Samsung Galaxy S24 Ultra

-- Insert sample reviews
INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(2, 1, 5, 'Amazing phone!'),
(3, 1, 4, 'Great phone'),
(2, 3, 5, 'Best Android phone'),
(3, 2, 5, 'Perfect noise cancellation');

-- Insert sample wishlist items (using valid product IDs 1-6)
INSERT INTO wishlist (user_id, product_id) VALUES
(2, 4),  -- john wishes OnePlus 12
(2, 6),  -- john wishes Samsung Galaxy Tab S9
(3, 2),  -- jane wishes Samsung Galaxy S24 Ultra
(3, 5);  -- jane wishes AirPods Pro
