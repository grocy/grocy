CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	session_key TEXT NOT NULL UNIQUE,
	expires DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
