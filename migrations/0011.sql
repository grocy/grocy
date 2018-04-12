CREATE TABLE habits_log (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	habit_id INTEGER NOT NULL,
	tracked_time DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
