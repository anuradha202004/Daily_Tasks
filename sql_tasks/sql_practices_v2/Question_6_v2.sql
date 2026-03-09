CREATE DATABASE Question_6;

CREATE TABLE customers (
    customer_id INT PRIMARY KEY,
    customer_name VARCHAR(100),
    address VARCHAR(255),
    latitude DECIMAL(9,6),
    longitude DECIMAL(9,6),
    
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

CREATE TABLE stores (
    store_id INT PRIMARY KEY,
    store_name VARCHAR(100),
    latitude DECIMAL(9,6),
    longitude DECIMAL(9,6),
        
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
    
);

INSERT INTO customers 
(customer_id, customer_name, address, latitude, longitude)
VALUES
(1,'Rahul','Ahmedabad',23.0225,72.5714),
(2,'Priya','Surat',21.1702,72.8311),
(3,'Amit','Vadodara',22.3072,73.1812);

INSERT INTO stores 
(store_id, store_name, latitude, longitude, created_at, updated_at)
VALUES
(1,'Reliance Mart',23.0300,72.5800,NOW(),NOW()),
(2,'DMart',23.0500,72.6000,NOW(),NOW()),
(3,'Big Bazaar',23.0100,72.5600,NOW(),NOW()),
(4,'Spencer Store',23.1000,72.6500,NOW(),NOW()),
(5,'Smart Bazaar',23.2000,72.7000,NOW(),NOW()),
(6,'More Store',23.4000,72.9000,NOW(),NOW());

SELECT 
    c.customer_id,
    c.customer_name,
    s.store_name,

    (6371 * ACOS(
        COS(RADIANS(c.latitude)) *
        COS(RADIANS(s.latitude)) *
        COS(RADIANS(s.longitude) - RADIANS(c.longitude)) +
        SIN(RADIANS(c.latitude)) *
        SIN(RADIANS(s.latitude))
    )) AS distance_km,

    ((6371 * ACOS(
        COS(RADIANS(c.latitude)) *
        COS(RADIANS(s.latitude)) *
        COS(RADIANS(s.longitude) - RADIANS(c.longitude)) +
        SIN(RADIANS(c.latitude)) *
        SIN(RADIANS(s.latitude))
    )) / 40) * 60 AS travel_time_minutes

FROM customers c
JOIN stores s
HAVING distance_km <= 10
ORDER BY c.customer_id, distance_km
LIMIT 5;