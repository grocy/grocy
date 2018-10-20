CREATE TABLE equipment (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	instruction_manual_file_name TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
)
