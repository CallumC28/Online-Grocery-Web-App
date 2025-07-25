# 🥦 Online Grocery Store  
Web Development Coursework. 

A fully responsive and secure online grocery store built using PHP, MySQL, Tailwind CSS, JavaScript (AJAX), and ReactJS. This project was developed for the Advanced Web Technologies module I studied, showcasing user registration, live product browsing, order management, and a RESTful API for manager access.

## Project Features

### 🛍 Public Users (Not Logged In)
- Browse products by category (Vegetables, Meat)
- View product names and images
- No prices shown until login (privacy and user incentive)
- Mobile-first design using Tailwind CSS

### 👤 Registered Customers
- Register using a form with **live validation (ReactJS)**
- CAPTCHA-secured login
- View product prices
- Place orders with multiple items and quantities
- View order history (`my_orders.php`)

### 🧑‍💼 Manager Role
- Access a **Manager API** page to:
  - View detailed order information using a RESTful API
- Manager-only navigation tab (visible only when logged in as manager)

## 🧑‍💻 Technologies Used

| Area            | Technology                         |
|-----------------|-------------------------------------|
| Frontend        | Tailwind CSS, HTML, JavaScript, jQuery |
| Interactivity   | AJAX, ReactJS (live validation)     |
| Backend         | PHP                                 |
| Database        | MySQL (with `x5z36` schema)         |
| Security        | Prepared Statements, CAPTCHA, Sessions |
| SEO             | Structured data (JSON-LD), Meta Tags |

---

## ⚙️ Database Schema (Updated)

**Database Name:** `x5z36`

```sql
CREATE DATABASE IF NOT EXISTS x5z36;
USE x5z36;

CREATE TABLE grocery_db_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) DEFAULT 'customer'
);

CREATE TABLE grocery_db_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(50),
  name VARCHAR(100),
  image VARCHAR(255),
  price DECIMAL(10,2)
);

CREATE TABLE grocery_db_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES grocery_db_users(id) ON DELETE CASCADE
);

CREATE TABLE grocery_db_order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES grocery_db_orders(id),
  FOREIGN KEY (product_id) REFERENCES grocery_db_products(id)
);
```

## 🛠️ Setup Instructions (XAMPP)
Clone or Download the project to your htdocs/ directory.

Start Apache & MySQL from XAMPP Control Panel.

Create database:

Open phpMyAdmin

Run the SQL schema provided above

Access the app at:
http://localhost/online-grocery-store/index.php (Will most likely be different depending on your own file path)

Create a manager manually:
```sql
INSERT INTO grocery_db_users (name, phone, email, password, role)
VALUES ('Manager', '07123456789', 'manager@example.com', '<hashed_pw>', 'manager');
```
Generate <hashed_password> in PHP with:
```
<?php echo password_hash('manager', PASSWORD_DEFAULT); ?>
```
