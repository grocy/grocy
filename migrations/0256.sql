DROP TABLE sessions;

CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	token_type TINYINT NOT NULL,
	token_hash TEXT NOT NULL UNIQUE,
	expires DATETIME NOT NULL,
	last_used DATETIME,
	client_info TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE INDEX ix_sessions_performance1 ON sessions (
	token_type,
	token_hash
);
