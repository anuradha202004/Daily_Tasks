Create database organization;

CREATE TABLE employees (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_name VARCHAR(100),
    manager_id INT,
    FOREIGN KEY (manager_id) REFERENCES employees(emp_id)
);

INSERT INTO employees (emp_name, manager_id)
VALUES ('CEO', NULL);

SELECT * FROM employees;

INSERT INTO employees (emp_name, manager_id)
VALUES
('Manager1', 1),
('Manager2', 1);

SELECT * FROM employees;

INSERT INTO employees (emp_name, manager_id)
VALUES
('Developer1', 2),
('Developer2', 2),
('Tester1', 3);

SELECT * FROM employees;

SELECT 
e.emp_name AS Employee,
m.emp_name AS Manager
FROM employees e
LEFT JOIN employees m
ON e.manager_id = m.emp_id;

WITH RECURSIVE hierarchy AS (

    -- Step 1: Start from the CEO
    SELECT 
        emp_id,
        emp_name,
        manager_id,
        1 AS level,
        emp_name AS path
    FROM employees
    WHERE manager_id IS NULL

    UNION ALL

    -- Step 2: Find employees under each manager
    SELECT 
        e.emp_id,
        e.emp_name,
        e.manager_id,
        h.level + 1,
        CONCAT(h.path, ' -> ', e.emp_name)
    FROM employees e
    JOIN hierarchy h
        ON e.manager_id = h.emp_id

)

SELECT * FROM hierarchy;