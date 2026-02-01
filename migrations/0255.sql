-- Add parent_location_id column to locations table for hierarchical organization
ALTER TABLE locations ADD parent_location_id INTEGER;

-- Create recursive view for resolving location hierarchy (ancestor-descendant pairs)
-- Used for circular reference detection and finding all descendants
CREATE VIEW locations_resolved
AS
WITH RECURSIVE location_hierarchy(location_id, ancestor_location_id, level)
AS (
	-- Base case: all locations map to themselves at level 0
	SELECT id, id, 0
	FROM locations

	UNION ALL

	-- Recursive case: find ancestors by following parent_location_id chain
	SELECT lh.location_id, l.parent_location_id, lh.level + 1
	FROM location_hierarchy lh
	JOIN locations l ON lh.ancestor_location_id = l.id
	WHERE l.parent_location_id IS NOT NULL
	LIMIT 100 -- Safety limit to prevent infinite loops
)
SELECT
	location_id AS id,
	location_id,
	ancestor_location_id,
	level
FROM location_hierarchy;

-- Create view for location hierarchy display with computed path and depth
CREATE VIEW locations_hierarchy
AS
WITH RECURSIVE location_tree(id, name, description, parent_location_id, is_freezer, active, row_created_timestamp, path, depth)
AS (
	-- Base case: root locations (no parent)
	SELECT id, name, description, parent_location_id, is_freezer, active, row_created_timestamp, name, 0
	FROM locations
	WHERE parent_location_id IS NULL

	UNION ALL

	-- Recursive case: child locations
	SELECT l.id, l.name, l.description, l.parent_location_id, l.is_freezer, l.active, l.row_created_timestamp,
		lt.path || ' > ' || l.name,
		lt.depth + 1
	FROM locations l
	JOIN location_tree lt ON l.parent_location_id = lt.id
	LIMIT 100 -- Safety limit
)
SELECT
	id,
	name,
	description,
	parent_location_id,
	is_freezer,
	active,
	row_created_timestamp,
	path AS location_path,
	depth AS location_depth
FROM location_tree;

-- Trigger to enforce NULL handling for empty parent_location_id (matching product pattern)
CREATE TRIGGER enforce_parent_location_id_null_when_empty_INS AFTER INSERT ON locations
BEGIN
	UPDATE locations
	SET parent_location_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_location_id, '') = '';
END;

CREATE TRIGGER enforce_parent_location_id_null_when_empty_UPD AFTER UPDATE ON locations
BEGIN
	UPDATE locations
	SET parent_location_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_location_id, '') = '';
END;

-- Trigger to prevent setting self as parent
CREATE TRIGGER prevent_self_parent_location_INS BEFORE INSERT ON locations
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		WHERE NEW.parent_location_id IS NOT NULL
			AND NEW.parent_location_id = NEW.id
	) NOTNULL) THEN RAISE(ABORT, 'A location cannot be its own parent') END;
END;

CREATE TRIGGER prevent_self_parent_location_UPD BEFORE UPDATE ON locations
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		WHERE NEW.parent_location_id IS NOT NULL
			AND NEW.parent_location_id = NEW.id
	) NOTNULL) THEN RAISE(ABORT, 'A location cannot be its own parent') END;
END;

-- Trigger to prevent circular references in location hierarchy
-- Note: Uses a subquery approach since we can't reference the view during INSERT
CREATE TRIGGER prevent_circular_location_hierarchy_UPD BEFORE UPDATE ON locations
WHEN NEW.parent_location_id IS NOT NULL
BEGIN
	SELECT CASE WHEN((
		-- Check if the new parent is a descendant of this location
		-- This would create a circular reference
		WITH RECURSIVE descendants(id) AS (
			SELECT NEW.id
			UNION ALL
			SELECT l.id
			FROM locations l
			JOIN descendants d ON l.parent_location_id = d.id
			WHERE l.id != NEW.id
			LIMIT 100
		)
		SELECT 1 FROM descendants WHERE id = NEW.parent_location_id
	) NOTNULL) THEN RAISE(ABORT, 'Circular location hierarchy detected') END;
END;
