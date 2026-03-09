CREATE DATABASE Question_5;

CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(15),
    address VARCHAR(200),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

INSERT INTO customers (first_name, last_name, email, phone, address) VALUES
('Rahul','Sharma','rahul@gmail.com','9876543210','Delhi'),
('Rahul','Sharma','rahul@gmail.com','9876543210','New Delhi'),
('Rohit','Patel','rohit@gmail.com','9123456780','Ahmedabad'),
('Rohit','P','rohit@gmail.com','9123456780','Ahmedabad'),
('Anita','Desai','anita@gmail.com','9988776655','Mumbai'),
('Anita','D','anita.d@gmail.com','9988776655','Mumbai');

SELECT 
    c1.customer_id AS customer1,
    c2.customer_id AS customer2,
    
    c1.email = c2.email AS email_match,
    c1.phone = c2.phone AS phone_match,
    SOUNDEX(c1.first_name) = SOUNDEX(c2.first_name) AS name_similarity,

    (
        (c1.email = c2.email) * 0.5 +
        (c1.phone = c2.phone) * 0.3 +
        (SOUNDEX(c1.first_name) = SOUNDEX(c2.first_name)) * 0.2
    ) AS confidence_score

FROM customers c1
JOIN customers c2 
ON c1.customer_id < c2.customer_id;


SELECT *,
CASE 
    WHEN confidence_score >= 0.7 THEN 'High Duplicate'
    WHEN confidence_score >= 0.4 THEN 'Possible Duplicate'
    ELSE 'Low Probability'
END AS duplicate_flag
FROM (
    SELECT 
        c1.customer_id AS customer1,
        c2.customer_id AS customer2,
        (
            (c1.email = c2.email) * 0.5 +
            (c1.phone = c2.phone) * 0.3 +
            (SOUNDEX(c1.first_name) = SOUNDEX(c2.first_name)) * 0.2
        ) AS confidence_score
    FROM customers c1
    JOIN customers c2
    ON c1.customer_id < c2.customer_id
) AS duplicates;