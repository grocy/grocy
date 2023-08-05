DROP VIEW stock_edited_entries;
CREATE VIEW stock_edited_entries
AS
/*
	Returns stock_id's which have been edited manually
*/
SELECT DISTINCT
	IFNULL(sl_add.stock_id, '') AS stock_id,
	IFNULL(MAX(sl_edit.id), -1) AS stock_log_id_of_newest_edited_entry
FROM stock_log sl_add
JOIN stock_log sl_edit
	ON sl_add.stock_id = sl_edit.stock_id
	AND sl_edit.transaction_type = 'stock-edit-new'
WHERE sl_add.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
	AND sl_add.amount > 0;
