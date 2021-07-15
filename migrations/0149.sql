CREATE TABLE meal_plan_sections (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	sort_number INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO meal_plan_sections
	(id, name, sort_number)
VALUES
	(-1, '', -1);

ALTER TABLE meal_plan
ADD section_id INTEGER NOT NULL DEFAULT -1;
