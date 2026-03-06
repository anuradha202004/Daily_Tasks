CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100)
);

INSERT INTO customers (customer_name) VALUES
('Rahul'),
('Anita'),
('Amit');

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT REFERENCES customers(customer_id),
    order_date DATE,
    total_amount NUMERIC(10,2)
);

INSERT INTO orders (customer_id, order_date, total_amount) VALUES
(1,'2026-03-01',5000),
(1,'2026-03-05',2000),
(1,'2026-03-07',3000),
(1,'2026-03-10',1500),
(1,'2026-03-12',8000),
(1,'2026-03-15',2500),

(2,'2026-03-01',6000),
(2,'2026-03-03',1000),
(2,'2026-03-07',7000),
(2,'2026-03-09',2000),
(2,'2026-03-11',3500),

(3,'2026-03-02',4000),
(3,'2026-03-04',2500),
(3,'2026-03-06',5000);

SELECT 
c.customer_id,
c.customer_name,
o.order_id,
o.order_date,
o.total_amount,
ROW_NUMBER() OVER (
PARTITION BY c.customer_id 
ORDER BY o.order_date DESC
) AS row_num

FROM customers c

JOIN LATERAL (
    SELECT *
    FROM orders
    WHERE orders.customer_id = c.customer_id
    ORDER BY order_date DESC
    LIMIT 5
) o ON TRUE;