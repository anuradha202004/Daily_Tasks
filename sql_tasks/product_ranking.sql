CREATE DATABASE product_ranking;

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    product_name VARCHAR(100),
    revenue DECIMAL(10,2)
);

INSERT INTO products (category_id, product_name, revenue) VALUES
(1, 'Laptop', 50000),
(1, 'Phone', 40000),
(1, 'Tablet', 40000),
(1, 'Camera', 20000),
(2, 'Shoes', 15000),
(2, 'T-shirt', 12000),
(2, 'Jacket', 12000),
(2, 'Jeans', 8000);

SELECT * FROM products;

SELECT 
product_id,
category_id,
product_name,
revenue,
DENSE_RANK() OVER (
    PARTITION BY category_id 
    ORDER BY revenue DESC
) AS rank_in_category
FROM products;

SELECT * FROM (
    SELECT 
    product_id,
    category_id,
    product_name,
    revenue,
    DENSE_RANK() OVER (
        PARTITION BY category_id 
        ORDER BY revenue DESC
    ) AS rank_in_category
    FROM products
) ranked_products
WHERE rank_in_category <= 3;

