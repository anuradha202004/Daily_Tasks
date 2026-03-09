CREATE DATABASE Question_1;

CREATE TABLE user_activity (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(50),
    activity_time DATETIME NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,

    deleted_at TIMESTAMP NULL,
    deleted_by INT
);

INSERT INTO user_activity 
(user_id, activity_type, activity_time, created_by)
VALUES
(1,'login','2026-03-09 10:00:00',1),
(1,'view_page','2026-03-09 10:05:00',1),
(2,'login','2026-03-09 11:00:00',2),
(2,'logout','2026-03-09 11:10:00',2);

WITH activity_gap AS (
    SELECT 
        user_id,
        activity_time,
        LAG(activity_time) OVER (PARTITION BY user_id ORDER BY activity_time) AS prev_time
    FROM user_activity
),

session_mark AS (
    SELECT *,
        CASE 
            WHEN TIMESTAMPDIFF(MINUTE, prev_time, activity_time) > 30 
                 OR prev_time IS NULL
            THEN 1
            ELSE 0
        END AS new_session
    FROM activity_gap
),

session_group AS (
    SELECT *,
        SUM(new_session) OVER (PARTITION BY user_id ORDER BY activity_time) AS session_id
    FROM session_mark
)

SELECT 
    user_id,
    session_id,
    MIN(activity_time) AS session_start,
    MAX(activity_time) AS session_end,
    TIMESTAMPDIFF(MINUTE, MIN(activity_time), MAX(activity_time)) AS duration_minutes,
    COUNT(*) AS activity_count,
    CASE 
        WHEN COUNT(*) > 1 THEN 'Active Session'
        ELSE 'Fragmented'
    END AS session_status
FROM session_group
GROUP BY user_id, session_id
ORDER BY user_id, session_start;