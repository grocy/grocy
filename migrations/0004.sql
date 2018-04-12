CREATE TABLE stock (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER NOT NULL,
	amount INTEGER NOT NULL,
	best_before_date DATE,
	purchased_date DATE DEFAULT (datetime('now', 'localtime')),
	stock_id TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
