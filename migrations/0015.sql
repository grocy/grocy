CREATE VIEW batteries_current
AS
SELECT battery_id, MAX(tracked_time) AS last_tracked_time
FROM battery_charge_cycles
GROUP BY battery_id
