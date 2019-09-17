CREATE TABLE quantity_unit_conversions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	from_qu_id INT NOT NULL,
	to_qu_id INT NOT NULL,
	factor REAL NOT NULL,
	product_id INT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE TRIGGER quantity_unit_conversions_custom_unique_constraint_INS BEFORE INSERT ON quantity_unit_conversions
BEGIN
-- Necessary because unique constraints don't include NULL values in SQLite, and also because the constraint should include the products default conversion factor
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
	UNION
	SELECT 1
	FROM products
	WHERE id = NEW.product_id
		AND qu_id_purchase = NEW.from_qu_id
		AND qu_id_stock = NEW.to_qu_id
	)
	NOTNULL) THEN RAISE(ABORT, "Unique constraint violation") END;
END;

CREATE TRIGGER quantity_unit_conversions_custom_unique_constraint_UPD BEFORE UPDATE ON quantity_unit_conversions
BEGIN
-- Necessary because unique constraints don't include NULL values in SQLite, and also because the constraint should include the products default conversion factor
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
		AND id != NEW.id
	UNION
	SELECT 1
	FROM products
	WHERE id = NEW.product_id
		AND qu_id_purchase = NEW.from_qu_id
		AND qu_id_stock = NEW.to_qu_id
	)
	NOTNULL) THEN RAISE(ABORT, "Unique constraint violation") END;
END;

CREATE VIEW quantity_unit_conversions_resolved
AS
-- First: Product "purchase to stock" conversion factor
SELECT
	p.id AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.qu_id_purchase AS from_qu_id,
	qu_from.name AS from_qu_name,
	p.qu_id_stock AS to_qu_id,
	qu_to.name AS to_qu_name,
	p.qu_factor_purchase_to_stock AS factor
FROM products p
JOIN quantity_units qu_from
	ON p.qu_id_purchase = qu_from.id
JOIN quantity_units qu_to
	ON p.qu_id_stock = qu_to.id

UNION

-- Second: Product specific overrides
SELECT
	p.id AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	quc.from_qu_id AS from_qu_id,
	qu_from.name AS from_qu_name,
	quc.to_qu_id AS to_qu_id,
	qu_to.name AS to_qu_name,
	quc.factor AS factor
FROM products p
JOIN quantity_unit_conversions quc
	ON p.qu_id_stock = quc.from_qu_id
	AND p.id = quc.product_id
JOIN quantity_units qu_from
	ON quc.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON quc.to_qu_id = qu_to.id

UNION

-- Third: Default quantity unit conversion factors
SELECT
	p.id AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.qu_id_stock AS from_qu_id,
	qu_from.name AS from_qu_name,
	quc.to_qu_id AS to_qu_id,
	qu_to.name AS to_qu_name,
	quc.factor AS factor
FROM products p
JOIN quantity_unit_conversions quc
	ON p.qu_id_stock = quc.from_qu_id
	AND quc.product_id IS NULL
JOIN quantity_units qu_from
	ON quc.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON quc.to_qu_id = qu_to.id;

DROP TRIGGER cascade_change_qu_id_stock;
CREATE TRIGGER cascade_change_qu_id_stock AFTER UPDATE ON products
BEGIN
	UPDATE recipes_pos
	SET qu_id = NEW.qu_id_stock
	WHERE product_id = NEW.id
		AND qu_id = OLD.qu_id_stock;
END;
