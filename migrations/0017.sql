CREATE TABLE shopping_list (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER,
	note TEXT,
	amount INTEGER NOT NULL DEFAULT 0,
	amount_autoadded INTEGER NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
