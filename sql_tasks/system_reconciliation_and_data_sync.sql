CREATE TABLE system1_inventory (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(100),
    stock INT
);

CREATE TABLE system2_inventory (
	product_id INT PRIMARY KEY,
	product_name VARCHAR(100),
	stock INT
);

INSERT INTO system1_inventory VALUES
(1,'Laptop',50),
(2,'Phone',100),
(3,'Tablet',40),
(4,'Camera',30);

INSERT INT system2_inventory VALUES
(1,'Laptop',50),
(2,'Phone',100),
(3,'Tablet',40),
(4,'Camera',30);

SELECT
COALESCE(s1.product_id, s2.product_id) AS product_id,
COALESCE(s1.product_name, s2.product_name) AS product_name,
s1.stock AS system1_stock,
s2.stock AS system2_stock,

CASE
    WHEN s1.product_id IS NULL THEN 'Missing in System1'
    WHEN s2.product_id IS NULL THEN 'Missing in System2'
    WHEN s1.stock <> s2.stock THEN 'Stock Mismatch'
    ELSE 'Match'
END AS status

FROM system1_inventory s1
FULL JOIN system2_inventory s2
ON s1.product_id = s2.product_id;