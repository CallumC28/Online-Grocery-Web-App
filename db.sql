-- 1. Create the Users table to store customer and manager details
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'customer', 
  UNIQUE KEY (email)
);

-- 2. Create the Orders table for order headers
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_users
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);

-- 3. Create the Products table to store product details
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(50) NOT NULL,
  name VARCHAR(100) NOT NULL,
  image VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL
);

-- 4. Create the Order Items table to store detailed order data
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_orders
    FOREIGN KEY (order_id)
    REFERENCES orders(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_order_items_products
    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE CASCADE
);

-- Insert products
INSERT INTO products (category, name, image, price) VALUES 
('Vegetables', 'Potato', 'images/potato.jpg', 0.50),
('Vegetables', 'Carrots', 'images/carrots.jpg', 0.70),
('Vegetables', 'Broccoli', 'images/broccoli.jpg', 1.00),
('Meat', 'Chicken', 'images/chicken.jpg', 5.00),
('Meat', 'Fish', 'images/fish.jpg', 6.00),
('Meat', 'Beef', 'images/beef.jpg', 7.50),
('Meat', 'Pork', 'images/pork.jpg', 6.50);
