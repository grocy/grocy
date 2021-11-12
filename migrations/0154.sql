DROP VIEW chores_current;
CREATE VIEW chores_current
AS
SELECT
	x.chore_id AS id, -- Dummy, LessQL needs an id column
	x.chore_id,
	x.chore_name,
	x.last_tracked_time,
	CASE WHEN x.rollover = 1 AND DATETIME('now', 'localtime') > x.next_estimated_execution_time THEN
		CASE WHEN IFNULL(x.track_date_only, 0) = 1 THEN
			DATETIME(STRFTIME('%Y-%m-%d', DATETIME('now', 'localtime')) || ' 23:59:59')
		ELSE
			DATETIME(STRFTIME('%Y-%m-%d', DATETIME('now', 'localtime')) || ' ' || STRFTIME('%H:%M:%S', x.next_estimated_execution_time))
		END
	ELSE
		CASE WHEN IFNULL(x.track_date_only, 0) = 1 THEN
			DATETIME(STRFTIME('%Y-%m-%d', x.next_estimated_execution_time) || ' 23:59:59')
		ELSE
			x.next_estimated_execution_time
		END
	END AS next_estimated_execution_time,
	x.track_date_only,
	x.next_execution_assigned_to_user_id
FROM (

SELECT
	h.id AS chore_id,
	h.name AS chore_name,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE h.period_type
		WHEN 'manually' THEN '2999-12-31 23:59:59'
		WHEN 'dynamic-regular' THEN DATETIME(MAX(l.tracked_time), '+' || CAST(h.period_days AS TEXT) || ' day')
		WHEN 'daily' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '+' || CAST(h.period_interval AS TEXT) || ' day')
		WHEN 'weekly' THEN (
			SELECT next
        		FROM (
				SELECT 'sunday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 0') AS next
				UNION
				SELECT 'monday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 1') AS next
				UNION
				SELECT 'tuesday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 2') AS next
				UNION
				SELECT 'wednesday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 3') AS next
				UNION
				SELECT 'thursday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 4') AS next
				UNION
				SELECT 'friday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 5') AS next
				UNION
				SELECT 'saturday' AS day, DATETIME(COALESCE((SELECT tracked_time FROM chores_log WHERE chore_id = h.id ORDER BY tracked_time DESC LIMIT 1), DATETIME('now', 'localtime')), '1 days', '+' || CAST((h.period_interval - 1) * 7 AS TEXT) || ' days', 'weekday 6') AS next
			)
			WHERE INSTR(period_config, day) > 0
			ORDER BY next
			LIMIT 1
		)
		WHEN 'monthly' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '+' || CAST(h.period_interval AS TEXT) || ' month', 'start of month', '+' || CAST(h.period_days - 1 AS TEXT) || ' day')
		WHEN 'yearly' THEN DATETIME(IFNULL(MAX(l.tracked_time), DATETIME('now', 'localtime')), '+' || CAST(h.period_interval AS TEXT) || ' years')
	END AS next_estimated_execution_time,
	h.track_date_only,
	h.rollover,
	h.next_execution_assigned_to_user_id
FROM chores h
LEFT JOIN chores_log l
	ON h.id = l.chore_id
	AND l.undone = 0
WHERE h.active = 1
GROUP BY h.id, h.name, h.period_days
) x;
