DROP VIEW stock_edited_entries;
CREATE VIEW stock_edited_entries
AS
/*
	Returns stock_id's which have been edited manually
*/
SELECT
	sl_add.stock_id,
	MAX(sl_edit.id) AS stock_log_id_of_newest_edited_entry,
	sl_origin.id AS stock_log_id_of_origin_entry
FROM stock_log sl_add
JOIN stock_log sl_edit
	ON sl_add.stock_id = sl_edit.stock_id
	AND sl_edit.transaction_type = 'stock-edit-new'
JOIN stock_log sl_origin
	ON sl_add.stock_id = sl_origin.stock_id
	AND sl_origin.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
WHERE sl_add.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
	AND sl_add.amount > 0
GROUP BY sl_add.stock_id, sl_origin.id;

DROP VIEW products_average_price;
CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	SUM(IFNULL(sl_origin.amount, sl.amount) * sl.price) / SUM(IFNULL(sl_origin.amount, sl.amount)) as price
FROM stock_log sl
LEFT JOIN stock_edited_entries see
	ON sl.stock_id = see.stock_id
LEFT JOIN stock_log sl_origin
	ON sl.stock_id = sl_origin.stock_id
	AND see.stock_log_id_of_origin_entry = sl_origin.id
WHERE sl.undone = 0
	AND (
		(sl.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
		OR (sl.transaction_type = 'stock-edit-new' AND sl.stock_id IN (SELECT stock_id FROM stock_edited_entries) AND sl.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries))
	)
	AND IFNULL(sl.price, 0) > 0
	AND IFNULL(sl.amount, 0) > 0
GROUP BY sl.product_id;

-- Update products_average_price cache
INSERT OR REPLACE INTO cache__products_average_price
	(product_id, price)
SELECT product_id, price
FROM products_average_price;
