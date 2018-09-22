CREATE TABLE tasks (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	due DATETIME,
	started TINYINT NOT NULL DEFAULT 0 CHECK(started IN (0, 1)),
	done TINYINT NOT NULL DEFAULT 0 CHECK(done IN (0, 1)),
	category_id INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE TABLE task_categories (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE VIEW tasks_current
AS
SELECT *
FROM tasks
WHERE due IS NULL
	OR (due IS NOT NULL AND due > datetime('now', 'localtime'));
