CREATE TABLE api_keys (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	api_key TEXT NOT NULL UNIQUE,
	expires DATETIME,
	last_used DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
