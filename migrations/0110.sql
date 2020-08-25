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
	AND sl.price IS NOT NULL
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
