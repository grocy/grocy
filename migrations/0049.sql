ALTER TABLE products
ADD allow_partial_units_in_stock TINYINT NOT NULL DEFAULT 0;

PRAGMA legacy_alter_table = ON;

ALTER TABLE stock RENAME TO stock_old;

CREATE TABLE stock (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER NOT NULL,
	amount DECIMAL(15, 2) NOT NULL,
	best_before_date DATE,
	purchased_date DATE DEFAULT (datetime('now', 'localtime')),
	stock_id TEXT NOT NULL,
	price DECIMAL(15, 2),
	open TINYINT NOT NULL DEFAULT 0 CHECK(open IN (0, 1)),
	opened_date DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO stock
	(product_id, amount, best_before_date, purchased_date, stock_id, price, open, opened_date, row_created_timestamp)
SELECT product_id, amount, best_before_date, purchased_date, stock_id, price, open, opened_date, row_created_timestamp
FROM stock_old;

DROP TABLE stock_old;

ALTER TABLE stock_log RENAME TO stock_log_old;

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
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO stock_log
	(product_id, amount, best_before_date, purchased_date, used_date, spoiled, stock_id, transaction_type, price, undone, undone_timestamp, opened_date, row_created_timestamp)
SELECT product_id, amount, best_before_date, purchased_date, used_date, spoiled, stock_id, transaction_type, price, undone, undone_timestamp, opened_date, row_created_timestamp
FROM stock_log_old;

DROP TABLE stock_log_old;

ALTER TABLE shopping_list RENAME TO shopping_list_old;

CREATE TABLE shopping_list (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER,
	note TEXT,
	amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO shopping_list
	(product_id, amount, note, row_created_timestamp)
SELECT product_id, amount, note, row_created_timestamp
FROM shopping_list_old;

DROP TABLE shopping_list_old;
