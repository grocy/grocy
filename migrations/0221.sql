CREATE VIEW products_last_price
AS
SELECT
	product_id,
	MAX(purchased_date) AS purchased_date,
	price -- Bare column, ref https://www.sqlite.org/lang_select.html#bare_columns_in_an_aggregate_query
FROM stock_log
WHERE transaction_type IN ('purchase', 'stock-edit-new', 'inventory-correction')
	AND IFNULL(price, 0) > 0
	AND IFNULL(amount, 0) > 0
	AND undone = 0
GROUP BY product_id;

DROP VIEW products_last_purchased;
CREATE VIEW products_last_purchased
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.amount,
	sl.best_before_date,
	sl.purchased_date,
	IFNULL(plp.price, 0) AS price,
	sl.location_id,
	sl.shopping_location_id
	FROM stock_log sl
	JOIN (
		SELECT
			s1.product_id,
			MAX(s1.id) max_stock_id
			FROM stock_log s1
			JOIN (
					SELECT
						s.product_id,
						MAX(s.purchased_date) max_purchased_date
					FROM stock_log s
					WHERE undone = 0
						AND transaction_type in ('purchase', 'stock-edit-new', 'inventory-correction')
					GROUP BY s.product_id) sp2
				ON s1.product_id = sp2.product_id
				AND s1.purchased_date = sp2.max_purchased_date
			WHERE undone = 0
				AND transaction_type in ('purchase', 'stock-edit-new', 'inventory-correction')
			GROUP BY s1.product_id) sp3
		ON sl.product_id = sp3.product_id
		AND sl.id = sp3.max_stock_id
	LEFT JOIN products_last_price plp
		ON sl.product_id = plp.product_id;
