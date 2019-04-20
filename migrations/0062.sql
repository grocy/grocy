ALTER TABLE shopping_list
ADD shopping_list_id INT DEFAULT 1;

CREATE TABLE shopping_lists (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO shopping_lists
	(name)
VALUES
	('Default');
