CREATE TABLE categories (
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(100)
);

INSERT INTO categories (category_name) VALUES
('Electronics'),
('Clothing'),
('Furniture');

CREATE TABLE regions (
    region_id SERIAL PRIMARY KEY,
    region_name VARCHAR(100)
);

INSERT INTO regions (region_name) VALUES
('North America'),
('Europe'),
('Asia');

CREATE TABLE sales (
    sale_id SERIAL PRIMARY KEY,
    category_id INT REFERENCES categories(category_id),
    region_id INT REFERENCES regions(region_id),
    amount NUMERIC(10,2)
);

INSERT INTO sales (category_id, region_id, amount) VALUES
(1,1,50000),
(1,2,40000),
(1,3,35000),

(2,1,20000),
(2,2,25000),
(2,3,15000),

(3,1,30000),
(3,2,28000),
(3,3,22000);

SELECT
c.category_name,
r.region_name,
SUM(s.amount) AS total_sales

FROM sales s
JOIN categories c ON s.category_id = c.category_id
JOIN regions r ON s.region_id = r.region_id

GROUP BY GROUPING SETS (

(c.category_name, r.region_name),
(c.category_name),
(r.region_name),
()

)

ORDER BY c.category_name, r.region_name;