ALTER TABLE stock
ADD note TEXT;

ALTER TABLE stock_log
ADD note TEXT;

PRAGMA legacy_alter_table = ON;

ALTER TABLE userfield_values RENAME TO userfield_values_old;

CREATE TABLE userfield_values (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	field_id INTEGER NOT NULL,
	object_id TEXT NOT NULL,
	value TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(field_id, object_id)
);

INSERT INTO userfield_values
	(id, field_id, object_id, value, row_created_timestamp)
SELECT id, field_id, object_id, value, row_created_timestamp
FROM userfield_values_old;

DROP TABLE userfield_values_old;

CREATE TRIGGER userfield_values_special_handling AFTER INSERT ON userfield_values
BEGIN
	-- Entity stock:
	-- object_id is the transaction_id on insert -> replace it by the corresponding stock_id
	INSERT OR REPLACE INTO userfield_values
		(field_id, object_id, value)
	SELECT uv.field_id, sl.stock_id, uv.value
	FROM userfield_values uv
	JOIN stock_log sl
		ON uv.object_id = sl.transaction_id
		AND sl.transaction_type IN ('purchase', 'inventory-correction')
	WHERE uv.field_id IN (SELECT id FROM userfields WHERE entity = 'stock')
		AND uv.field_id = NEW.field_id
		AND uv.object_id = NEW.object_id;

	DELETE FROM userfield_values
	WHERE field_id IN (SELECT id FROM userfields WHERE entity = 'stock')
		AND field_id = NEW.field_id
		AND object_id = NEW.object_id;
END;

DROP VIEW stock_splits;
CREATE VIEW stock_splits
AS

/*
	Helper view which shows splitted stock rows which could be compacted
	(a stock_id starting with "x" indicates that this entry shouldn't be compacted)
*/

SELECT
	product_id,
	SUM(amount) AS total_amount,
	MIN(stock_id) AS stock_id_to_keep,
	MAX(id) AS id_to_keep,
	GROUP_CONCAT(id) AS id_group,
	GROUP_CONCAT(stock_id) AS stock_id_group,
	id -- Dummy
FROM stock
WHERE stock_id NOT LIKE 'x%'
GROUP BY product_id, best_before_date, purchased_date, price, open, opened_date, location_id, shopping_location_id, IFNULL(note, '')
HAVING COUNT(*) > 1;

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
	p.id AS product_id,
	sl.note,
	sl.stock_id
FROM stock_log sl
LEFT JOIN users_dto u
	ON sl.user_id = u.id
JOIN products p
	ON sl.product_id = p.id
JOIN locations l
	ON sl.location_id = l.id
JOIN quantity_units qu
	ON p.qu_id_stock = qu.id;
