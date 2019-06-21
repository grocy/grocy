CREATE VIEW stock_average_product_shelf_life
AS
SELECT
	p.id,
	CASE WHEN x.product_id IS NULL THEN -1 ELSE AVG(x.shelf_life_days) END AS average_shelf_life_days
FROM products p
LEFT JOIN (
		SELECT
			sl_p.product_id,
			JULIANDAY(MIN(sl_p.best_before_date)) - JULIANDAY(MIN(sl_c.used_date)) AS shelf_life_days
		FROM stock_log sl_p
		JOIN (
				SELECT
					product_id,
					stock_id,
					MAX(used_date) AS used_date
				FROM stock_log
				WHERE transaction_type = 'consume'
				GROUP BY product_id, stock_id
			) sl_c
			ON sl_p.stock_id = sl_c.stock_id
		WHERE sl_p.transaction_type = 'purchase'
		GROUP BY sl_p.product_id, sl_p.stock_id
	) x
	ON p.id = x.product_id
GROUP BY p.id;
