CREATE VIEW stock_current
AS
SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date
FROM stock
GROUP BY product_id
