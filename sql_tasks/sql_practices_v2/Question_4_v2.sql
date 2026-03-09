CREATE DATABASE Question_4;

CREATE TABLE user_events (
    event_id SERIAL PRIMARY KEY,
    user_id INT,
    event_type VARCHAR(50),
    event_time TIMESTAMP,
    product_id INT,
    
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

INSERT INTO user_events (user_id,event_type,event_time,product_id) VALUES
(1,'login','2026-03-08 10:00:00',NULL),
(1,'browse','2026-03-08 10:05:00',101),
(1,'add_to_cart','2026-03-08 10:10:00',101),
(1,'logout','2026-03-08 10:20:00',NULL),

(2,'login','2026-03-08 11:00:00',NULL),
(2,'browse','2026-03-08 11:05:00',102),
(2,'add_to_cart','2026-03-08 11:10:00',102),
(2,'purchase','2026-03-08 11:25:00',102),

(3,'login','2026-03-08 12:00:00',NULL),
(3,'browse','2026-03-08 12:10:00',103),
(3,'add_to_cart','2026-03-08 12:20:00',103),
(3,'logout','2026-03-08 12:40:00',NULL);


SELECT 
    ue1.user_id,
    ue1.event_time AS add_to_cart_time,
    ue2.event_time AS purchase_time

FROM user_events ue1

LEFT JOIN user_events ue2
ON ue1.user_id = ue2.user_id
AND ue2.event_type = 'purchase'
AND ue2.event_time BETWEEN ue1.event_time 
AND ue1.event_time + INTERVAL 24 HOUR

WHERE ue1.event_type = 'add_to_cart'
AND ue2.event_id IS NULL;

SELECT 
    user_id,
    event_type,
    event_time,
    
    LEAD(event_type) OVER(PARTITION BY user_id ORDER BY event_time) AS next_event,
    
    LEAD(event_time) OVER(PARTITION BY user_id ORDER BY event_time) AS next_event_time,

    TIMESTAMPDIFF(MINUTE,
        event_time,
        LEAD(event_time) OVER(PARTITION BY user_id ORDER BY event_time)
    ) AS minutes_between_events

FROM user_events
WHERE event_type = 'add_to_cart';

SELECT 
    user_id,
    GROUP_CONCAT(event_type ORDER BY event_time) AS event_sequence
FROM user_events
GROUP BY user_id;