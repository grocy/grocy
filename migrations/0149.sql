CREATE TABLE meal_plan_sections (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	sort_number INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO meal_plan_sections
	(id, name, sort_number)
VALUES
	(-1, '', -1);

ALTER TABLE meal_plan
ADD section_id INTEGER NOT NULL DEFAULT -1;

CREATE TRIGGER prevent_internal_meal_plan_section_removal BEFORE DELETE ON meal_plan_sections
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM meal_plan_sections
		WHERE id = OLD.id
			AND id = -1
	) NOTNULL) THEN RAISE(ABORT, "This is an internally used/required default section and therefore can't be deleted") END;
END;
