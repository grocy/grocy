DROP VIEW userfield_values_resolved;
CREATE VIEW userfield_values_resolved
AS
SELECT
	u.id, -- Dummy, LessQL needs an id column
	u.entity,
	u.name,
	u.caption,
	u.type,
	u.show_as_column_in_tables,
	u.row_created_timestamp,
	u.config,
	uv.object_id,
	uv.value
FROM userfields u
JOIN userfield_values uv
	ON u.id = uv.field_id

UNION

-- Kind of a hack, include userentity userfields also for the table userobjects
SELECT
	u.id, -- Dummy, LessQL needs an id column,
	'userobjects',
	u.name,
	u.caption,
	u.type,
	u.show_as_column_in_tables,
	u.row_created_timestamp,
	u.config,
	uv.object_id,
	uv.value
FROM userfields u
JOIN userfield_values uv
	ON u.id = uv.field_id
WHERE u.entity like 'userentity-%';
