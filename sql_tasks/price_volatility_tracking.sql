Create database price_volatility_tracking;

CREATE TABLE product_prices (
    price_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    product_name VARCHAR(100),
    price DECIMAL(10,2),
    price_date DATE
);

INSERT INTO product_prices (product_id, product_name, price, price_date) VALUES
(1,'Laptop',50000,'2026-01-01'),
(1,'Laptop',52000,'2026-02-01'),
(1,'Laptop',48000,'2026-03-01'),
(2,'Phone',30000,'2026-01-10'),
(2,'Phone',32000,'2026-02-10'),
(2,'Phone',31000,'2026-03-10');

SELECT * FROM product_prices;

SELECT 
product_id,
product_name,
price_date,
price AS current_price,
LAG(price) OVER (PARTITION BY product_id ORDER BY price_date) AS previous_price,
LEAD(price) OVER (PARTITION BY product_id ORDER BY price_date) AS next_price,
ROUND(
    ((price - LAG(price) OVER (PARTITION BY product_id ORDER BY price_date)) 
    / LAG(price) OVER (PARTITION BY product_id ORDER BY price_date)) * 100
,2) AS percentage_change
FROM product_prices
WHERE price_date >= CURDATE() - INTERVAL 90 DAY;


