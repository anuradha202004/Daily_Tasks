CREATE TABLE regions (
    region_id SERIAL PRIMARY KEY,
    region_name VARCHAR(100)
);

INSERT INTO regions (region_name) VALUES
('North America'),
('Europe'),
('Asia');

CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100),
    region_id INT REFERENCES regions(region_id)
);

INSERT INTO customers (customer_name, region_id) VALUES
('John',1),
('Maria',2),
('Raj',3);

CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100)
);

INSERT INTO products (product_name) VALUES
('Laptop'),
('Phone'),
('Tablet'),
('Camera');

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT REFERENCES customers(customer_id),
    order_date DATE
);

INSERT INTO orders (customer_id, order_date) VALUES
(1,'2026-03-01'),
(2,'2026-03-02'),
(3,'2026-03-03');

CREATE TABLE order_items (
    item_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id),
    product_id INT REFERENCES products(product_id)
);

INSERT INTO order_items (order_id, product_id) VALUES
(1,1),  -- Laptop (North America)
(2,2),  -- Phone (Europe)
(3,3);  -- Tablet (Asia)

SELECT p.product_name
FROM products p
WHERE NOT EXISTS (
    SELECT *
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE oi.product_id = p.product_id
    AND c.region_id = (
        SELECT region_id 
        FROM regions 
        WHERE region_name = 'North America'
    )
);

SELECT p.product_name
FROM products p
LEFT JOIN order_items oi ON p.product_id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.order_id
LEFT JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN regions r ON c.region_id = r.region_id
AND r.region_name = 'North America'
WHERE r.region_id IS NULL;