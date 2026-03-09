CREATE DATABASE question_7;

CREATE TABLE accounts (
    account_id INT PRIMARY KEY AUTO_INCREMENT,
    account_name VARCHAR(100),
    account_type VARCHAR(50),
	    
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

INSERT INTO accounts (account_name, account_type) VALUES
('Main Cash Account','Asset'),
('Savings Account','Asset'),
('Business Expenses','Expense');

CREATE TABLE account_hierarchy (
    parent_account_id INT,
    child_account_id INT,
    
        
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    PRIMARY KEY(parent_account_id, child_account_id),
    FOREIGN KEY(parent_account_id) REFERENCES accounts(account_id),
    FOREIGN KEY(child_account_id) REFERENCES accounts(account_id)
);

INSERT INTO account_hierarchy (parent_account_id , child_account_id ) VALUES (1,2);

CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT,
    transaction_date DATETIME,
    transaction_type ENUM('DEBIT','CREDIT'),
    amount DECIMAL(10,2),
    description VARCHAR(200),
    
        
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT,
    
    FOREIGN KEY(account_id) REFERENCES accounts(account_id)
);

INSERT INTO transactions 
(account_id, transaction_date, transaction_type, amount, description)
VALUES
(1,'2026-03-01 10:00:00','CREDIT',5000,'Initial deposit'),
(1,'2026-03-02 09:00:00','DEBIT',1000,'Office supplies'),
(1,'2026-03-03 11:00:00','DEBIT',2500,'Equipment purchase'),
(1,'2026-03-04 12:00:00','DEBIT',800,'Utility payment'),
(1,'2026-03-05 15:00:00','CREDIT',2000,'Customer payment');

SELECT
    a.account_name,
    t.transaction_date,
    t.transaction_type,
    t.amount,

    CASE 
        WHEN t.transaction_type = 'CREDIT' THEN t.amount
        ELSE -t.amount
    END AS signed_amount,

    SUM(
        CASE 
            WHEN t.transaction_type = 'CREDIT' THEN t.amount
            ELSE -t.amount
        END
    ) OVER (
        PARTITION BY t.account_id
        ORDER BY t.transaction_date
    ) AS running_balance

FROM transactions t
JOIN accounts a 
ON t.account_id = a.account_id
ORDER BY t.account_id, t.transaction_date;


WITH ledger AS (
SELECT
    account_id,
    transaction_date,
    transaction_type,
    amount,

    SUM(
        CASE 
            WHEN transaction_type='CREDIT' THEN amount
            ELSE -amount
        END
    ) OVER (PARTITION BY account_id ORDER BY transaction_date)
    AS running_balance

FROM transactions
)

SELECT *,
CASE
    WHEN running_balance < 0 THEN 'OVERDRAFT ALERT'
    WHEN amount > 2000 AND transaction_type='DEBIT'
         THEN 'RAPID DEPLETION'
    ELSE 'NORMAL'
END AS anomaly_flag

FROM ledger;