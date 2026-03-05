create database market_basket_analysis;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_date DATE
);
select * from order_items;
select * from orders;

CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

INSERT INTO orders (order_date) VALUES
('2026-03-01'),
('2026-03-02'),
('2026-03-03'),
('2026-03-04'),
('2026-03-05');

INSERT INTO order_items (order_id, product_name) VALUES
(1,'Milk'),
(1,'Bread'),
(1,'Butter'),

(2,'Milk'),
(2,'Bread'),

(3,'Milk'),
(3,'Butter'),

(4,'Bread'),
(4,'Butter'),

(5,'Milk'),
(5,'Bread');

INSERT INTO orders (order_date) VALUES
('2026-03-06'),
('2026-03-07'),
('2026-03-08'),
('2026-03-09'),
('2026-03-10'),
('2026-03-11'),
('2026-03-12'),
('2026-03-13'),
('2026-03-14'),
('2026-03-15');

INSERT INTO order_items (order_id, product_name) VALUES
(6,'Milk'),(6,'Bread'),
(7,'Milk'),(7,'Bread'),
(8,'Milk'),(8,'Bread'),
(9,'Milk'),(9,'Bread'),
(10,'Milk'),(10,'Bread'),
(11,'Milk'),(11,'Bread'),
(12,'Milk'),(12,'Bread'),
(13,'Milk'),(13,'Bread'),
(14,'Milk'),(14,'Bread'),
(15,'Milk'),(15,'Bread');

drop table order_items;

SELECT 
oi1.product_name AS product1,
oi2.product_name AS product2,
COUNT(*) AS times_bought_together
FROM order_items oi1
JOIN order_items oi2
ON oi1.order_id = oi2.order_id
AND oi1.product_name < oi2.product_name
GROUP BY oi1.product_name, oi2.product_name;

SELECT 
oi1.product_name AS product1,
oi2.product_name AS product2,
COUNT(*) AS times_bought_together,
ROUND(
COUNT(*) * 100.0 / (SELECT COUNT(DISTINCT order_id) FROM orders),2
) AS percentage_orders
FROM order_items oi1
JOIN order_items oi2
ON oi1.order_id = oi2.order_id
AND oi1.product_name < oi2.product_name
GROUP BY oi1.product_name, oi2.product_name;

SELECT 
oi1.product_name AS product1,
oi2.product_name AS product2,
COUNT(*) AS times_bought_together,
ROUND(
COUNT(*) * 100.0 / (SELECT COUNT(DISTINCT order_id) FROM orders),2
) AS percentage_orders
FROM order_items oi1
JOIN order_items oi2
ON oi1.order_id = oi2.order_id
AND oi1.product_name < oi2.product_name
GROUP BY oi1.product_name, oi2.product_name
HAVING COUNT(*) > 10;

