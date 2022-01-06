PRAGMA legacy_alter_table = ON;

ALTER TABLE stock_log RENAME TO stock_log_old;

-- user_id was introduced in migration 114 and set to 1 by default,
-- but this user could have been already deleted
-- => Change the user_id to the first existing user (which is the new defintion of "default user") for that case
UPDATE stock_log_old
SET user_id = (SELECT MIN(id) FROM users)
WHERE user_id NOT IN (SELECT id FROM users)
	AND user_id = 1;

CREATE TABLE stock_log (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER NOT NULL,
	amount DECIMAL(15, 2) NOT NULL,
	best_before_date DATE,
	purchased_date DATE,
	used_date DATE,
	spoiled INTEGER NOT NULL DEFAULT 0,
	stock_id TEXT NOT NULL,
	transaction_type TEXT NOT NULL,
	price DECIMAL(15, 2),
	undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1)),
	undone_timestamp DATETIME,
	opened_date DATETIME,
	location_id INTEGER,
	recipe_id INTEGER,
	correlation_id TEXT,
	transaction_id TEXT,
	stock_row_id INTEGER,
	shopping_location_id INTEGER,
	user_id INTEGER NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO stock_log
	(product_id, amount, best_before_date, purchased_date, used_date, spoiled, stock_id, transaction_type, price, undone, undone_timestamp, opened_date, location_id, recipe_id, correlation_id, transaction_id, stock_row_id, shopping_location_id, user_id, row_created_timestamp)
SELECT product_id, amount, best_before_date, purchased_date, used_date, spoiled, stock_id, transaction_type, price, undone, undone_timestamp, opened_date, location_id, recipe_id, correlation_id, transaction_id, stock_row_id, shopping_location_id, user_id, row_created_timestamp
FROM stock_log_old;

DROP TABLE stock_log_old;

CREATE TRIGGER set_products_default_location_if_empty_stock_log AFTER INSERT ON stock_log
BEGIN
	UPDATE stock_log
	SET location_id = (SELECT location_id FROM products where id = product_id)
	WHERE id = NEW.id
		AND location_id IS NULL;
END;

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
LEFT JOIN users_dto u
	ON sl.user_id = u.id
JOIN products p
	ON sl.product_id = p.id
JOIN locations l
	ON sl.location_id = l.id
JOIN quantity_units qu
	ON p.qu_id_stock = qu.id;
