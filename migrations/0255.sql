-- Add parent_location_id column and change uniqueness constraint for hierarchical locations
-- Allows same name under different parents (e.g., "Shelf 1" in Fridge and "Shelf 1" in Cupboard)

PRAGMA legacy_alter_table = ON;

-- Rename old table
ALTER TABLE locations RENAME TO locations_old;

-- Create new table with parent_location_id and without UNIQUE constraint on name
CREATE TABLE locations (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),
	is_freezer TINYINT NOT NULL DEFAULT 0,
	active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1)),
	parent_location_id INTEGER
);

-- Copy data
INSERT INTO locations (id, name, description, row_created_timestamp, is_freezer, active)
SELECT id, name, description, row_created_timestamp, is_freezer, active
FROM locations_old;

-- Drop old table
DROP TABLE locations_old;

-- Create partial unique indexes for composite uniqueness
-- Ensures name is unique within each parent (including NULL as a distinct parent)
CREATE UNIQUE INDEX ix_locations_name_parent ON locations(name, parent_location_id)
WHERE parent_location_id IS NOT NULL;

CREATE UNIQUE INDEX ix_locations_name_root ON locations(name)
WHERE parent_location_id IS NULL;

-- Create recursive view for resolving location hierarchy (ancestor-descendant pairs)
-- Used for finding all descendants of a location
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

-- Trigger to enforce NULL handling for empty parent_location_id
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
CREATE TRIGGER prevent_circular_location_hierarchy_UPD BEFORE UPDATE ON locations
WHEN NEW.parent_location_id IS NOT NULL
BEGIN
	SELECT CASE WHEN((
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

-- Trigger to inherit is_freezer from parent on INSERT
CREATE TRIGGER inherit_freezer_from_parent_INS AFTER INSERT ON locations
WHEN NEW.parent_location_id IS NOT NULL
BEGIN
	UPDATE locations
	SET is_freezer = 1
	WHERE id = NEW.id
		AND (SELECT is_freezer FROM locations WHERE id = NEW.parent_location_id) = 1;
END;

-- Trigger to inherit is_freezer from parent on UPDATE (when parent changes)
CREATE TRIGGER inherit_freezer_from_parent_UPD AFTER UPDATE ON locations
WHEN NEW.parent_location_id IS NOT NULL AND NEW.parent_location_id != IFNULL(OLD.parent_location_id, 0)
BEGIN
	UPDATE locations
	SET is_freezer = 1
	WHERE id = NEW.id
		AND (SELECT is_freezer FROM locations WHERE id = NEW.parent_location_id) = 1;
END;

-- Trigger to propagate is_freezer to all descendants when a location becomes a freezer
CREATE TRIGGER propagate_freezer_to_descendants_UPD AFTER UPDATE ON locations
WHEN NEW.is_freezer = 1 AND OLD.is_freezer = 0
BEGIN
	UPDATE locations
	SET is_freezer = 1
	WHERE id IN (
		WITH RECURSIVE descendants(id) AS (
			SELECT id FROM locations WHERE parent_location_id = NEW.id
			UNION ALL
			SELECT l.id
			FROM locations l
			JOIN descendants d ON l.parent_location_id = d.id
			LIMIT 100
		)
		SELECT id FROM descendants
	);
END;
