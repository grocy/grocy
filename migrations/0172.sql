DROP VIEW batteries_current;
CREATE VIEW batteries_current
AS
SELECT
	b.id, -- Dummy, LessQL needs an id column
	b.id AS battery_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE WHEN b.charge_interval_days = 0
		THEN '2999-12-31 23:59:59'
		ELSE datetime(MAX(l.tracked_time), '+' || CAST(b.charge_interval_days AS TEXT) || ' day')
	END AS next_estimated_charge_time
FROM batteries b
LEFT JOIN battery_charge_cycles l
	ON b.id = l.battery_id
	AND l.undone = 0
WHERE b.active = 1
GROUP BY b.id, b.charge_interval_days;
