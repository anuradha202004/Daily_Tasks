CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100)
);

INSERT INTO customers (customer_name) VALUES
('Rahul'),
('Anita'),
('Amit');

CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100),
    price NUMERIC(10,2)
);

INSERT INTO products (product_name, price) VALUES
('Laptop',50000),
('Phone',30000),
('Tablet',20000);

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT REFERENCES customers(customer_id),
    order_date DATE
);

INSERT INTO orders (customer_id, order_date) VALUES
(1,'2026-03-01'),
(1,'2026-03-02'),
(2,'2026-03-03');

CREATE TABLE order_items (
    item_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id),
    product_id INT REFERENCES products(product_id),
    quantity INT
);

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1,1,1),
(1,2,1),
(2,3,2),
(3,1,1);

SELECT
c.customer_id,
c.customer_name,
json_agg(
    json_build_object(
        'order_id', o.order_id,
        'order_date', o.order_date,
        'items',
        (
            SELECT json_agg(
                json_build_object(
                    'product_name', p.product_name,
                    'quantity', oi.quantity,
                    'price', p.price
                )
            )
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = o.order_id
        )
    )
) AS orders
FROM customers c
JOIN orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.customer_name;
