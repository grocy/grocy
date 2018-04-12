CREATE TABLE battery_charge_cycles (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	battery_id TEXT NOT NULL,
	tracked_time DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
