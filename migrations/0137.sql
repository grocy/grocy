PRAGMA legacy_alter_table = ON;

ALTER TABLE battery_charge_cycles RENAME TO battery_charge_cycles_old;

CREATE TABLE battery_charge_cycles (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	battery_id INTEGER NOT NULL,
	tracked_time DATETIME,
	undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1)),
	undone_timestamp DATETIME,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO battery_charge_cycles
	(id, battery_id, tracked_time, undone, undone_timestamp, row_created_timestamp)
SELECT id, battery_id, tracked_time, undone, undone_timestamp, row_created_timestamp
FROM battery_charge_cycles_old;

DROP TABLE battery_charge_cycles_old;
