DROP VIEW products_price_history;
CREATE VIEW products_price_history
AS
SELECT
	sl.product_id AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.price,
	IFNULL(sl.edited_origin_amount, sl.amount) AS amount,
	sl.purchased_date,
	sl.shopping_location_id,
	sl.transaction_type
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
