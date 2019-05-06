CREATE TABLE meal_plan (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	day DATE NOT NULL,
	recipe_id INTEGER NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(day, recipe_id)
);
