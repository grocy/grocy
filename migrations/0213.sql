DROP VIEW products_average_price;
CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	SUM(s.amount * s.price) / SUM(s.amount) as price
FROM stock_log s
WHERE s.transaction_type IN ('purchase', 'inventory-correction', 'stock-edit-new')
	AND IFNULL(s.price, 0) > 0
	AND IFNULL(s.amount, 0) > 0
	AND undone = 0
GROUP BY s.product_id;

DROP VIEW products_last_purchased;
CREATE VIEW products_last_purchased
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.amount,
	sl.best_before_date,
	sl.purchased_date,
	(SELECT price FROM stock_log WHERE product_id = sl.product_id AND transaction_type IN ('purchase', 'stock-edit-new', 'inventory-correction') AND IFNULL(price, 0) > 0 AND IFNULL(amount, 0) > 0 AND undone = 0 ORDER BY purchased_date DESC LIMIT 1) AS price,
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
		AND sl.id = sp3.max_stock_id;

DROP VIEW product_price_history;
CREATE VIEW product_price_history
AS
SELECT
	sl.product_id AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.price,
	sl.purchased_date,
	sl.shopping_location_id
FROM stock_log sl
WHERE sl.transaction_type IN ('purchase', 'inventory-correction', 'stock-edit-new')
	AND sl.undone = 0
	AND IFNULL(sl.price, 0) > 0
	AND IFNULL(sl.amount, 0) > 0
	AND sl.id NOT IN (
			-- These are edited purchase and inventory-correction rows
			SELECT sl_origin.id
			FROM stock_log sl_origin
			JOIN stock_log sl_edit
				ON sl_origin.stock_id = sl_edit.stock_id
				AND sl_edit.transaction_type = 'stock-edit-new'
				AND sl_edit.id > sl_origin.id
			WHERE sl_origin.transaction_type IN ('purchase', 'inventory-correction')
		);
