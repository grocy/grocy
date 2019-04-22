CREATE TABLE userfields (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	entity TEXT NOT NULL,
	name TEXT NOT NULL,
	caption TEXT NOT NULL,
	type TEXT NOT NULL,
	show_as_column_in_tables TINYINT NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(entity, name)
);

CREATE TABLE userfield_values (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	field_id INTEGER NOT NULL,
	object_id INTEGER NOT NULL,
	value TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(field_id, object_id)
);

CREATE VIEW userfield_values_resolved
AS
SELECT
	u.*,
	uv.object_id,
	uv.value
FROM userfields u
JOIN userfield_values uv
	ON u.id = uv.field_id;
