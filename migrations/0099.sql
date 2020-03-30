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

ALTER TABLE products
ADD shopping_location_id INTEGER;

DROP VIEW stock_current_locations;
CREATE VIEW stock_current_locations
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
        SUM(s.amount) as amount,
	s.location_id AS location_id,
	l.name AS location_name,
	l.is_freezer AS location_is_freezer
FROM stock s
JOIN locations l
	ON s.location_id = l.id
GROUP BY s.product_id, s.location_id, l.name;
