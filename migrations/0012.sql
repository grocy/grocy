CREATE VIEW habits_current
AS
SELECT habit_id, MAX(tracked_time) AS last_tracked_time
FROM habits_log
GROUP BY habit_id
