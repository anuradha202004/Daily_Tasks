create database time_series_aggregation;

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE,
    amount DECIMAL(10,2)
);

INSERT INTO transactions (transaction_date, amount) VALUES
('2025-04-10',1000),
('2025-04-20',2000),
('2025-05-05',1500),
('2025-05-18',2500),
('2025-06-10',3000),
('2025-07-15',3500),
('2025-08-20',4000),
('2025-09-12',4500),
('2025-10-08',5000),
('2025-11-18',5500),
('2025-12-22',6000),
('2026-01-05',6500),
('2026-02-10',7000),
('2026-03-12',7500);

SELECT 
YEAR(transaction_date) AS year,
MONTH(transaction_date) AS month,
SUM(amount) AS monthly_revenue
FROM transactions
GROUP BY YEAR(transaction_date), MONTH(transaction_date)
ORDER BY year, month;

SELECT 
YEAR(transaction_date) AS year,
MONTH(transaction_date) AS month,
SUM(amount) AS monthly_revenue,

SUM(SUM(amount)) OVER (
ORDER BY YEAR(transaction_date), MONTH(transaction_date)
) AS running_total,

SUM(SUM(amount)) OVER (
PARTITION BY YEAR(transaction_date)
ORDER BY MONTH(transaction_date)
) AS ytd_revenue,

LAG(SUM(amount)) OVER (
ORDER BY YEAR(transaction_date), MONTH(transaction_date)
) AS previous_month_revenue

FROM transactions
WHERE transaction_date >= CURDATE() - INTERVAL 24 MONTH
GROUP BY YEAR(transaction_date), MONTH(transaction_date)
ORDER BY year, month;

