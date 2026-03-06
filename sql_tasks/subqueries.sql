create database subqueries;

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100)
);

INSERT INTO categories (category_name) VALUES
('Electronics'),
('Clothing'),
('Books');

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
('Jeans',2),
('Book',3);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100)
);

INSERT INTO customers (customer_name) VALUES
('Rahul'),
('Anita'),
('Amit');

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

INSERT INTO orders (customer_id, order_date) VALUES
(1,'2026-03-01'),
(1,'2026-03-02'),
(2,'2026-03-03'),
(3,'2026-03-04');

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO order_items (order_id, product_id) VALUES
(1,1),  -- Laptop
(1,3),  -- Shirt
(1,5),  -- Book

(2,2),  -- Phone
(2,4),  -- Jeans

(3,1),  -- Laptop

(4,1),  -- Laptop
(4,3);  -- Shirt

SELECT c.customer_id, c.customer_name
FROM customers c
WHERE NOT EXISTS (

    SELECT *
    FROM categories cat
    WHERE NOT EXISTS (

        SELECT *
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.customer_id = c.customer_id
        AND p.category_id = cat.category_id

    )

);