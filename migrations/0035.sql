ALTER TABLE habits RENAME TO chores;

CREATE TABLE chores_log (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	chore_id INTEGER NOT NULL,
	tracked_time DATETIME,
	done_by_user_id INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO chores_log
	(chore_id, tracked_time, done_by_user_id, row_created_timestamp)
SELECT habit_id, tracked_time, done_by_user_id, row_created_timestamp
FROM habits_log;

DROP TABLE habits_log;

DROP VIEW habits_current;
CREATE VIEW chores_current
AS
SELECT
	h.id AS chore_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE h.period_type
		WHEN 'manually' THEN '2999-12-31 23:59:59'
		WHEN 'dynamic-regular' THEN datetime(MAX(l.tracked_time), '+' || CAST(h.period_days AS TEXT) || ' day')
	END AS next_estimated_execution_time
FROM chores h
LEFT JOIN chores_log l
	ON h.id = l.chore_id
GROUP BY h.id, h.period_days;
