DROP VIEW habits_current;
CREATE VIEW habits_current
AS
SELECT
	h.id AS habit_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE h.period_type
		WHEN 'manually' THEN '2999-12-31 23:59:59'
		WHEN 'dynamic-regular' THEN datetime(MAX(l.tracked_time), '+' || CAST(h.period_days AS TEXT) || ' day')
	END AS next_estimated_execution_time
FROM habits h
LEFT JOIN habits_log l
	ON h.id = l.habit_id
GROUP BY h.id, h.period_days;

DROP VIEW batteries_current;
CREATE VIEW batteries_current
AS
SELECT
	b.id AS battery_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE WHEN b.charge_interval_days = 0
		THEN '2999-12-31 23:59:59'
		ELSE datetime(MAX(l.tracked_time), '+' || CAST(b.charge_interval_days AS TEXT) || ' day')
	END AS next_estimated_charge_time
FROM batteries b
LEFT JOIN battery_charge_cycles l
	ON b.id = l.battery_id
GROUP BY b.id, b.charge_interval_days;
