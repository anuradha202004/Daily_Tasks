CREATE DATABASE Question_2;

CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

CREATE TABLE inventory_levels (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    current_stock INT,
    daily_consumption_rate INT,
    last_updated DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE reorder_points (
    reorder_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    reorder_point INT,
    supply_lead_time INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO products (product_name, category) VALUES
('Laptop', 'Electronics'),
('Keyboard', 'Electronics'),
('Mouse', 'Electronics'),
('Printer Paper', 'Stationary');

INSERT INTO inventory_levels (product_id, current_stock, daily_consumption_rate, last_updated) VALUES
(1, 15, 5, CURDATE() - INTERVAL 2 DAY),
(2, 50, 4, CURDATE() - INTERVAL 3 DAY),
(3, 10, 3, CURDATE() - INTERVAL 1 DAY),
(4, 200, 20, CURDATE() - INTERVAL 6 DAY);

INSERT INTO reorder_points (product_id, reorder_point, supply_lead_time) VALUES
(1, 20, 7),
(2, 30, 5),
(3, 15, 4),
(4, 150, 6);

SELECT 
    p.product_name,
    il.current_stock,
    rp.reorder_point,
    rp.supply_lead_time,
    
    ROUND(il.current_stock / il.daily_consumption_rate,2) AS estimated_days_until_stockout,
    
    il.last_updated
    
FROM products p

JOIN inventory_levels il 
ON p.product_id = il.product_id

JOIN reorder_points rp 
ON p.product_id = rp.product_id

WHERE il.current_stock < rp.reorder_point
AND il.last_updated >= CURDATE() - INTERVAL 7 DAY;

