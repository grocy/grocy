CREATE TABLE habits (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	period_type TEXT NOT NULL,
	period_days INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
