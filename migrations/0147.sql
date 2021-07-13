DROP VIEW uihelper_stock_journal;
CREATE VIEW uihelper_stock_journal
AS
SELECT
	sl.id,
	sl.row_created_timestamp,
	sl.correlation_id,
	sl.undone,
	sl.undone_timestamp,
	sl.transaction_type,
	sl.spoiled,
	sl.amount,
	sl.location_id,
	l.name AS location_name,
	p.name AS product_name,
	qu.name AS qu_name,
	qu.name_plural AS qu_name_plural,
	u.display_name AS user_display_name,
	p.id AS product_id
FROM stock_log sl
JOIN users_dto u
	ON sl.user_id = u.id
JOIN products p
	ON sl.product_id = p.id
JOIN locations l
	ON sl.location_id = l.id
JOIN quantity_units qu
	ON p.qu_id_stock = qu.id;
