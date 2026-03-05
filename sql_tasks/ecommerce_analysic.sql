CREATE DATABASE ecommerce_analysis;

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    email VARCHAR(100)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

INSERT INTO customers (customer_name, email) VALUES
('Anuradha', 'anu@email.com'),
('Pooja', 'pooja@email.com'),
('Parv', 'parv@email.com');

INSERT INTO orders (customer_id, order_date) VALUES
(1, CURDATE() - INTERVAL 10 DAY),
(1, CURDATE() - INTERVAL 5 DAY),
(2, CURDATE() - INTERVAL 15 DAY),
(3, CURDATE() - INTERVAL 40 DAY);

INSERT INTO order_items (order_id, product_name, quantity, price) VALUES
(1, 'Laptop', 1, 50000),
(1, 'Mouse', 2, 500),
(2, 'Phone', 1, 30000),
(3, 'Tablet', 1, 20000),
(4, 'Camera', 1, 15000);


SELECT * 
FROM orders
WHERE order_date >= CURDATE() - INTERVAL 30 DAY;

SELECT 
c.customer_id,
c.customer_name,
COUNT(o.order_id) AS purchase_count,
SUM(oi.quantity * oi.price) AS total_spent
FROM customers c
JOIN orders o ON c.customer_id = o.customer_id
JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
GROUP BY c.customer_id, c.customer_name;

SELECT AVG(total_spent)
FROM (
    SELECT 
    c.customer_id,
    SUM(oi.quantity * oi.price) AS total_spent
    FROM customers c
    JOIN orders o ON c.customer_id = o.customer_id
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
    GROUP BY c.customer_id
) avg_table;

SELECT 
c.customer_name,
COUNT(o.order_id) AS purchase_count,
SUM(oi.quantity * oi.price) AS total_spent,
SUM(oi.quantity * oi.price) - (
    SELECT AVG(total_spent)
    FROM (
        SELECT 
        c.customer_id,
        SUM(oi.quantity * oi.price) AS total_spent
        FROM customers c
        JOIN orders o ON c.customer_id = o.customer_id
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
        GROUP BY c.customer_id
    ) avg_spending
) AS above_average
FROM customers c
JOIN orders o ON c.customer_id = o.customer_id
JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
GROUP BY c.customer_id, c.customer_name
HAVING total_spent > (
    SELECT AVG(total_spent)
    FROM (
        SELECT 
        c.customer_id,
        SUM(oi.quantity * oi.price) AS total_spent
        FROM customers c
        JOIN orders o ON c.customer_id = o.customer_id
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_date >= CURDATE() - INTERVAL 30 DAY
        GROUP BY c.customer_id
    ) avg_spending
);