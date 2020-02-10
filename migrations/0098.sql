ALTER TABLE recipes
ADD recipe_catagory_id INTEGER;

CREATE TABLE recipe_catagories (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
