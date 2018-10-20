CREATE TABLE user_settings (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	key TEXT NOT NULL,
	value TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),
	row_updated_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(user_id, key)
);
