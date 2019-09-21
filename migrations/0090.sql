DROP VIEW stock_average_product_shelf_life;
CREATE VIEW stock_average_product_shelf_life
AS
SELECT
	p.id,
	CASE WHEN x.product_id IS NULL THEN -1 ELSE AVG(x.shelf_life_days) END AS average_shelf_life_days
FROM products p
LEFT JOIN (
		SELECT
			sl_p.product_id,
			JULIANDAY(sl_p.best_before_date) - JULIANDAY(sl_p.purchased_date) AS shelf_life_days
		FROM stock_log sl_p
		WHERE sl_p.transaction_type = 'purchase'
			AND sl_p.undone = 0
	) x
	ON p.id = x.product_id
GROUP BY p.id;
