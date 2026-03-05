Create database multi_dimensional_sales_reporting;

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100)
);

INSERT INTO categories (category_name) VALUES
('Electronics'),
('Clothing');

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

INSERT INTO products (product_name, category_id) VALUES
('Laptop',1),
('Phone',1),
('Shirt',2),
('Jacket',2);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_date DATE,
    status VARCHAR(20)
);

INSERT INTO orders (order_date, status) VALUES
('2026-01-10','completed'),
('2026-01-20','pending'),
('2026-02-05','completed'),
('2026-02-10','cancelled'),
('2026-03-15','completed');

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1,1,1,50000),
(1,2,1,30000),
(2,3,2,2000),
(3,1,1,52000),
(4,4,1,4000),
(5,2,1,31000);

SELECT
c.category_name,
CONCAT('Q', QUARTER(o.order_date)) AS quarter,

COUNT(CASE WHEN o.status='completed' THEN 1 END) AS completed_orders,
SUM(CASE WHEN o.status='completed' THEN oi.quantity*oi.price END) AS completed_amount,

COUNT(CASE WHEN o.status='pending' THEN 1 END) AS pending_orders,
SUM(CASE WHEN o.status='pending' THEN oi.quantity*oi.price END) AS pending_amount,

COUNT(CASE WHEN o.status='cancelled' THEN 1 END) AS cancelled_orders,
SUM(CASE WHEN o.status='cancelled' THEN oi.quantity*oi.price END) AS cancelled_amount

FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN products p ON oi.product_id = p.product_id
JOIN categories c ON p.category_id = c.category_id

GROUP BY c.category_name, QUARTER(o.order_date)
ORDER BY c.category_name, quarter;