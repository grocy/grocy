DROP VIEW stock_edited_entries;
CREATE VIEW stock_edited_entries
AS
/*
	Returns stock_id's which have been edited manually
*/
SELECT
	x.stock_id,
	x.stock_log_id_of_newest_edited_entry,

	-- When an origin entry was edited, the new origin amount is the one of the newest "stock-edit-new" + all
	-- previous consume transactions (mind that consume transaction amounts are negative, hence here - instead of +)
	(
		SELECT amount
		FROM stock_log sli
		WHERE sli.id = x.stock_log_id_of_newest_edited_entry
	)
	-
	IFNULL((
		SELECT SUM(amount)
		FROM stock_log sli_consumed
		WHERE sli_consumed.stock_id = x.stock_id
			AND sli_consumed.transaction_type IN ('consume', 'inventory-correction')
			AND sli_consumed.id < x.stock_log_id_of_newest_edited_entry
			AND sli_consumed.amount < 0
			AND sli_consumed.undone = 0), 0) AS edited_origin_amount
FROM (
	SELECT
		sl_add.stock_id,
		MAX(sl_edit.id) AS stock_log_id_of_newest_edited_entry
	FROM stock_log sl_add
	JOIN stock_log sl_edit
		ON sl_add.stock_id = sl_edit.stock_id
		AND sl_edit.transaction_type = 'stock-edit-new'
	WHERE sl_add.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
		AND sl_add.amount > 0
GROUP BY sl_add.stock_id
) x
JOIN stock_log sl_edit
	ON x.stock_log_id_of_newest_edited_entry = sl_edit.id;

DROP VIEW products_average_price;
CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	SUM(IFNULL(sl.edited_origin_amount, sl.amount) * sl.price) / SUM(IFNULL(sl.edited_origin_amount, sl.amount)) as price
FROM (
	SELECT sl.*, CASE WHEN sl.transaction_type = 'stock-edit-new' THEN see.edited_origin_amount END AS edited_origin_amount
	FROM stock_log sl
	LEFT JOIN stock_edited_entries see
		ON sl.stock_id = see.stock_id
) sl
WHERE sl.undone = 0
	AND (
		(sl.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries)) -- Unedited origin entries
		OR (sl.transaction_type = 'stock-edit-new' AND sl.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries)) -- Edited origin entries => take the newest "stock-edit-new" one
	)
	AND IFNULL(sl.price, 0) > 0
	AND IFNULL(sl.amount, 0) > 0
GROUP BY sl.product_id;

-- Update products_average_price cache
INSERT OR REPLACE INTO cache__products_average_price
	(product_id, price)
SELECT product_id, price
FROM products_average_price;

DROP VIEW products_price_history;
CREATE VIEW products_price_history
AS
SELECT
	sl.product_id AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.price,
	IFNULL(sl.edited_origin_amount, sl.amount) AS amount,
	sl.purchased_date,
	sl.shopping_location_id
FROM (
	SELECT sl.*, CASE WHEN sl.transaction_type = 'stock-edit-new' THEN see.edited_origin_amount END AS edited_origin_amount
	FROM stock_log sl
	LEFT JOIN stock_edited_entries see
		ON sl.stock_id = see.stock_id
) sl
WHERE sl.undone = 0
	AND (
		(sl.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries)) -- Unedited origin entries
		OR (sl.transaction_type = 'stock-edit-new' AND sl.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries)) -- Edited origin entries => take the newest "stock-edit-new" one
	)
	AND IFNULL(sl.price, 0) > 0
	AND IFNULL(sl.amount, 0) > 0;
