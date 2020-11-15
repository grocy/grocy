PRAGMA legacy_alter_table = ON;

ALTER TABLE tasks RENAME TO tasks_old;

CREATE TABLE tasks (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
	description TEXT,
	due_date DATETIME,
	done TINYINT NOT NULL DEFAULT 0 CHECK(done IN (0, 1)),
	done_timestamp DATETIME,
	category_id INTEGER,
	assigned_to_user_id INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO tasks
	(id, name, description, due_date, done, done_timestamp, category_id, assigned_to_user_id, row_created_timestamp)
SELECT id, name, description, due_date, done, done_timestamp, category_id, assigned_to_user_id, row_created_timestamp
FROM tasks_old;

DROP TABLE tasks_old;
