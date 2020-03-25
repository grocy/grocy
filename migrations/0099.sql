CREATE TABLE shopping_locations (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

ALTER TABLE stock_log
ADD shopping_location_id INTEGER;

ALTER TABLE stock
ADD shopping_location_id INTEGER;
