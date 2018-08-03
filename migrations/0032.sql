DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date
FROM stock
GROUP BY product_id;

DROP VIEW habits_current;
CREATE VIEW habits_current
AS
SELECT habit_id, MAX(tracked_time) AS last_tracked_time
FROM habits_log
GROUP BY habit_id;

DROP VIEW batteries_current;
CREATE VIEW batteries_current
AS
SELECT battery_id, MAX(tracked_time) AS last_tracked_time
FROM battery_charge_cycles
GROUP BY battery_id;
