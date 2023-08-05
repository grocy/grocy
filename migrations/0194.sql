CREATE VIEW stock_edited_entries
AS
/*
	Returns stock_id's which have been edited manually
*/
SELECT sl_add.stock_id
FROM stock_log sl_add
JOIN stock_log sl_edit
	ON sl_add.stock_id = sl_edit.stock_id
	AND sl_edit.transaction_type = 'stock-edit-new'
WHERE sl_add.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
	AND sl_add.amount > 0
GROUP BY sl_add.stock_id;

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
		WHERE sl_p.undone = 0
			AND (
				(sl_p.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl_p.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
				OR (sl_p.transaction_type = 'stock-edit-new' AND sl_p.stock_id IN (SELECT stock_id FROM stock_edited_entries))
			)
	) x
	ON p.id = x.product_id
GROUP BY p.id;
