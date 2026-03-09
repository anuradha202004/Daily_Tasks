CREATE DATABASE Question_3;

CREATE TABLE departments (
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(100),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

CREATE TABLE employees (
    employee_id INT PRIMARY KEY AUTO_INCREMENT,
    employee_name VARCHAR(100),
    department_id INT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

CREATE TABLE performance (
    performance_id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT,
    sales_amount DECIMAL(10,2),
    target_amount DECIMAL(10,2),
    rating INT,
    
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

INSERT INTO departments (department_name) VALUES
('Sales'),
('Marketing'),
('IT');

INSERT INTO employees (employee_name, department_id) VALUES
('Rahul',1),
('Priya',1),
('Amit',1),
('Sneha',2),
('Karan',2),
('Neha',3);

SELECT * FROM employees;
SELECT * FROM departments;
DROP TABLE departments;
INSERT INTO performance (employee_id, sales_amount, target_amount, rating) VALUES
(1,50000,45000,4),
(2,60000,55000,5),
(3,40000,45000,3),
(4,30000,35000,4),
(5,45000,40000,5),
(6,20000,25000,3);

SELECT 
    e.employee_name,
    d.department_name,
    p.sales_amount,
    p.target_amount,

    -- Achievement percentage
    ROUND((p.sales_amount / p.target_amount) * 100,2) AS achievement_percentage,

    -- Department average sales
    AVG(p.sales_amount) OVER (PARTITION BY d.department_id) AS department_avg_sales,

    -- Percentile rank
    PERCENT_RANK() OVER (
        PARTITION BY d.department_id
        ORDER BY p.sales_amount
    ) AS percentile_rank,

    -- Quartile assignment
    NTILE(4) OVER (
        PARTITION BY d.department_id
        ORDER BY p.sales_amount DESC
    ) AS quartile

FROM employees e
JOIN departments d 
    ON e.department_id = d.department_id
JOIN performance p 
    ON e.employee_id = p.employee_id;