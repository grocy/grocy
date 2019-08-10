ALTER TABLE chores
ADD rollover TINYINT DEFAULT 0;

DROP VIEW chores_current;
CREATE VIEW chores_current
AS
SELECT
	x.chore_id,
	x.last_tracked_time,
	CASE WHEN x.rollover = 1 AND DATETIME('now', 'localtime') > x.next_estimated_execution_time THEN
		DATETIME(STRFTIME('%Y-%m-%d', DATETIME('now', 'localtime')) || ' ' || STRFTIME('%H:%M:%S', x.next_estimated_execution_time))
	ELSE
		x.next_estimated_execution_time
	END AS next_estimated_execution_time,
	x.track_date_only
FROM (

SELECT
	h.id AS chore_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE h.period_type
		WHEN 'manually' THEN '2999-12-31 23:59:59'
		WHEN 'dynamic-regular' THEN DATETIME(MAX(l.tracked_time), '+' || CAST(h.period_days AS TEXT) || ' day')
		WHEN 'daily' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '+1 day')
		WHEN 'weekly' THEN
			CASE
				WHEN period_config LIKE '%sunday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 0')
				WHEN period_config LIKE '%monday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 1')
				WHEN period_config LIKE '%tuesday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 2')
				WHEN period_config LIKE '%wednesday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 3')
				WHEN period_config LIKE '%thursday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 4')
				WHEN period_config LIKE '%friday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 5')
				WHEN period_config LIKE '%saturday%' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '1 days', 'weekday 6')
			END
		WHEN 'monthly' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '+1 month', 'start of month', '+' || CAST(h.period_days - 1 AS TEXT) || ' day')
	END AS next_estimated_execution_time,
	h.track_date_only,
	h.rollover
FROM chores h
LEFT JOIN chores_log l
	ON h.id = l.chore_id
	AND l.undone = 0
GROUP BY h.id, h.period_days

) x;
