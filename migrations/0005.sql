CREATE TABLE stock_log (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER NOT NULL,
	amount INTEGER NOT NULL,
	best_before_date DATE,
	purchased_date DATE,
	used_date DATE,
	spoiled INTEGER NOT NULL DEFAULT 0,
	stock_id TEXT NOT NULL,
	transaction_type TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
