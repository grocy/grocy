-- Remove including the product's qu_factor_purchase_to_stock
DROP TRIGGER qu_conversions_custom_constraint_INS;
CREATE TRIGGER qu_conversions_custom_constraint_INS BEFORE INSERT ON quantity_unit_conversions
BEGIN
	/*
		Necessary because unique constraints don't include NULL values in SQLite
	*/
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
	)
	NOTNULL) THEN RAISE(ABORT, "QU conversion already exists") END;
END;

-- Remove including the product's qu_factor_purchase_to_stock
DROP TRIGGER qu_conversions_custom_constraint_UPD;
CREATE TRIGGER qu_conversions_custom_constraint_UPD BEFORE UPDATE ON quantity_unit_conversions
BEGIN
	/* This contains practically the same logic as the trigger qu_conversions_custom_constraint_INS */

	/*
		Necessary because unique constraints don't include NULL values in SQLite
	*/
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
		AND id != NEW.id
	)
	NOTNULL) THEN RAISE(ABORT, "QU conversion already exists") END;
END;

-- Migrate qu_factor_purchase_to_stock to product specific QU conversions
INSERT INTO quantity_unit_conversions
	(from_qu_id, to_qu_id, factor, product_id)
SELECT p.qu_id_purchase, p.qu_id_stock, IFNULL(p.qu_factor_purchase_to_stock, 1.0), p.id
FROM products p
WHERE p.qu_id_stock != p.qu_id_purchase
	AND NOT EXISTS(SELECT 1 FROM quantity_unit_conversions WHERE product_id = p.id AND from_qu_id = p.qu_id_stock AND to_qu_id = p.qu_id_purchase)
	AND NOT EXISTS(SELECT 1 FROM quantity_unit_conversions WHERE product_id = p.id AND from_qu_id = p.qu_id_purchase AND to_qu_id = p.qu_id_stock);

-- ALTER TABLE DROP COLUMN is only available in SQLite >= 3.35.0 (we require 3.34.0 as of now), so can't be used
PRAGMA legacy_alter_table = ON;
ALTER TABLE products RENAME TO products_old;

-- Remove qu_factor_purchase_to_stock column
CREATE TABLE products (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	product_group_id INTEGER,
	active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1)),
	location_id INTEGER NOT NULL,
	shopping_location_id INTEGER,
	qu_id_purchase INTEGER NOT NULL,
	qu_id_stock INTEGER NOT NULL,
	min_stock_amount INTEGER NOT NULL DEFAULT 0,
	default_best_before_days INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_open INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_freezing INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_thawing INTEGER NOT NULL DEFAULT 0,
	picture_file_name TEXT,
	enable_tare_weight_handling TINYINT NOT NULL DEFAULT 0,
	tare_weight REAL NOT NULL DEFAULT 0,
	not_check_stock_fulfillment_for_recipes TINYINT DEFAULT 0,
	parent_product_id INT,
	calories INTEGER,
	cumulate_min_stock_amount_of_sub_products TINYINT DEFAULT 0,
	due_type TINYINT NOT NULL DEFAULT 1 CHECK(due_type IN (1, 2)),
	quick_consume_amount REAL NOT NULL DEFAULT 1,
	hide_on_stock_overview TINYINT NOT NULL DEFAULT 0 CHECK(hide_on_stock_overview IN (0, 1)),
	default_stock_label_type INTEGER NOT NULL DEFAULT 0,
	should_not_be_frozen TINYINT NOT NULL DEFAULT 0 CHECK(should_not_be_frozen IN (0, 1)),
	treat_opened_as_out_of_stock TINYINT NOT NULL DEFAULT 1 CHECK(treat_opened_as_out_of_stock IN (0, 1)),
	no_own_stock TINYINT NOT NULL DEFAULT 0 CHECK(no_own_stock IN (0, 1)),
	default_consume_location_id INTEGER,
	move_on_open TINYINT NOT NULL DEFAULT 0 CHECK(move_on_open IN (0, 1)),
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO products
	(id, name, description, product_group_id, active, location_id, shopping_location_id, qu_id_purchase, qu_id_stock, min_stock_amount, default_best_before_days, default_best_before_days_after_open, default_best_before_days_after_freezing,
	default_best_before_days_after_thawing, picture_file_name, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, due_type, quick_consume_amount,
	hide_on_stock_overview, default_stock_label_type, should_not_be_frozen, row_created_timestamp,
	treat_opened_as_out_of_stock, no_own_stock, default_consume_location_id, move_on_open)
SELECT id, name, description, product_group_id, active, location_id, shopping_location_id, qu_id_purchase, qu_id_stock, min_stock_amount, default_best_before_days, default_best_before_days_after_open, default_best_before_days_after_freezing,
default_best_before_days_after_thawing, picture_file_name, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, due_type, quick_consume_amount,
hide_on_stock_overview, default_stock_label_type, should_not_be_frozen, row_created_timestamp,
treat_opened_as_out_of_stock, no_own_stock, default_consume_location_id, move_on_open
FROM products_old;

DROP TABLE products_old;

-- Recreate all products-table triggers and indexes
CREATE TRIGGER enforce_parent_product_id_null_when_empty_INS AFTER INSERT ON products
BEGIN
	UPDATE products
	SET parent_product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_product_id, '') = '';
END;

CREATE TRIGGER enforce_parent_product_id_null_when_empty_UPD AFTER UPDATE ON products
BEGIN
	UPDATE products
	SET parent_product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_product_id, '') = '';
END;

CREATE TRIGGER cascade_product_removal AFTER DELETE ON products
BEGIN
	DELETE FROM stock
	WHERE product_id = OLD.id;

	DELETE FROM stock_log
	WHERE product_id = OLD.id;

	DELETE FROM product_barcodes
	WHERE product_id = OLD.id;

	DELETE FROM quantity_unit_conversions
	WHERE product_id = OLD.id;

	DELETE FROM recipes_pos
	WHERE product_id = OLD.id;

	UPDATE recipes
	SET product_id = NULL
	WHERE product_id = OLD.id;

	DELETE FROM meal_plan
	WHERE product_id = OLD.id
		AND type = 'product';

	DELETE FROM shopping_list
	WHERE product_id = OLD.id;

	DELETE FROM userfield_values
	WHERE object_id = OLD.id
		AND field_id IN (SELECT id FROM userfields WHERE entity = 'products');
END;

CREATE TRIGGER enfore_product_nesting_level BEFORE UPDATE ON products
BEGIN
	-- Currently only 1 level is supported
    SELECT CASE WHEN((
        SELECT 1
        FROM products p
        WHERE IFNULL(NEW.parent_product_id, '') != ''
            AND IFNULL(parent_product_id, '') = NEW.id
    ) NOTNULL) THEN RAISE(ABORT, "Unsupported product nesting level detected (currently only 1 level is supported)") END;
END;

CREATE TRIGGER enforce_min_stock_amount_for_cumulated_childs_INS AFTER INSERT ON products
BEGIN
	/*
		When a parent product has cumulate_min_stock_amount_of_sub_products enabled,
		the child should not have any min_stock_amount
	*/

	UPDATE products
	SET min_stock_amount = 0
	WHERE id IN (
			SELECT
				p_child.id
			FROM products p_parent
			JOIN products p_child
				ON p_child.parent_product_id = p_parent.id
			WHERE p_parent.id = NEW.id
				AND IFNULL(p_parent.cumulate_min_stock_amount_of_sub_products, 0) = 1
			)
		AND min_stock_amount > 0;
END;

CREATE TRIGGER enforce_min_stock_amount_for_cumulated_childs_UPD AFTER UPDATE ON products
BEGIN
	/*
		When a parent product has cumulate_min_stock_amount_of_sub_products enabled,
		the child should not have any min_stock_amount
	*/

	UPDATE products
	SET min_stock_amount = 0
	WHERE id IN (
			SELECT
				p_child.id
			FROM products p_parent
			JOIN products p_child
				ON p_child.parent_product_id = p_parent.id
			WHERE p_parent.id = NEW.id
				AND IFNULL(p_parent.cumulate_min_stock_amount_of_sub_products, 0) = 1
			)
		AND min_stock_amount > 0;
END;

CREATE TRIGGER cascade_change_qu_id_stock BEFORE UPDATE ON products WHEN NEW.qu_id_stock != OLD.qu_id_stock
BEGIN
	-- All amounts anywhere are related to the products stock QU,
	-- so apply the appropriate unit conversion to all amounts everywhere on change
	-- (and enforce that such a conversion need to exist when the product was once added to stock)

	SELECT CASE WHEN((
		SELECT 1
		FROM quantity_unit_conversions_resolved
		WHERE product_id = NEW.id
			AND from_qu_id = OLD.qu_id_stock
			AND to_qu_id = NEW.qu_id_stock
	) ISNULL)
	AND
	((
        SELECT 1
        FROM stock_log
		WHERE product_id = NEW.id
			AND NEW.qu_id_stock != OLD.qu_id_stock
    ) NOTNULL) THEN RAISE(ABORT, "qu_id_stock can only be changed when a corresponding QU conversion (old QU => new QU) exists when the product was once added to stock") END;

	UPDATE chores
	SET product_amount = product_amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE product_id = NEW.id;

	UPDATE meal_plan
	SET product_amount = product_amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE type = 'product'
		AND product_id = NEW.id;

	UPDATE recipes_pos
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE product_id = NEW.id;

	UPDATE shopping_list
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE product_id = NEW.id
		AND product_id IS NOT NULL;

	UPDATE stock
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0),
	price = price / IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE product_id = NEW.id;

	UPDATE stock_log
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0),
	price = price / IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE product_id = NEW.id;
END;

CREATE TRIGGER cascade_change_qu_id_stock2 AFTER UPDATE ON products WHEN NEW.qu_id_stock != OLD.qu_id_stock
BEGIN
	-- See also the trigger "cascade_change_qu_id_stock BEFORE UPDATE ON products"
	-- This here applies the needed changes to the products table itself only AFTER the update

	UPDATE products
	SET quick_consume_amount = quick_consume_amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0),
	calories = calories / IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0),
	tare_weight = tare_weight * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock LIMIT 1), 1.0)
	WHERE id = NEW.id;
END;

CREATE INDEX ix_products_performance1 ON products (
    parent_product_id
);

CREATE INDEX ix_products_performance2 ON products (
    CASE WHEN parent_product_id IS NULL THEN id ELSE parent_product_id END,
    active
);

-- Remove including the product's qu_factor_purchase_to_stock
DROP VIEW quantity_unit_conversions_resolved;
CREATE VIEW quantity_unit_conversions_resolved
AS

/*
	First, determine conversions that are a single step.
	There may be multiple definitions for conversions between two units
	(e.g. due to purchase-to-stock, product-specific and default conversions),
	thus priorities are used to disambiguate conversions.
	Later, we'll only use the factor with the highest priority to convert between two units.
*/

WITH RECURSIVE conversion_factors_dup(product_id, from_qu_id, to_qu_id, factor, priority)
AS (
	-- Priority 1: Product specific QU overrides
	-- Note that the quantity_unit_conversions table already contains both conversion directions for every conversion.
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		30
	FROM quantity_unit_conversions
	WHERE product_id IS NOT NULL

	UNION

	-- Priority 2: Default QU conversions are handled in a later CTE, as we can't determine yet, for which products they are applicable.
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		20
	FROM quantity_unit_conversions
	WHERE product_id IS NULL

	UNION

	-- Priority 3: QU conversions with a factor of 1.0 from the stock unit to the stock unit
	SELECT
		id,
		qu_id_stock,
		qu_id_stock,
		1.0,
		10
	FROM products
),

-- Now, remove duplicate conversions, only retaining the entries with the highest priority
conversion_factors(product_id, from_qu_id, to_qu_id, factor)
AS (
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		FIRST_VALUE(factor) OVER win
	FROM conversion_factors_dup
	GROUP BY product_id, from_qu_id, to_qu_id
	WINDOW win AS(PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY priority DESC)
),

-- Now build the closure of posisble conversions using a recursive CTE
closure(depth, product_id, from_qu_id, to_qu_id, factor, path)
AS (
	-- As a base case, select the conversions that refer to a concrete product
	SELECT
		1 as depth,
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		'/' || from_qu_id || '/' || to_qu_id || '/' -- We need to keep track of the conversion path in order to prevent cycles
	FROM conversion_factors
	WHERE product_id IS NOT NULL

	UNION

	-- First recursive case: Add a product-associated conversion to the chain
	SELECT
		c.depth + 1,
		c.product_id,
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor,
		c.path || s.to_qu_id || '/'
	FROM closure c
	JOIN conversion_factors s
		ON c.product_id = s.product_id
		AND c.to_qu_id = s.from_qu_id
	WHERE c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- Prevent cycles

	UNION

	-- Second recursive case: Add a default unit conversion to the *start* of the conversion chain
	SELECT
		c.depth + 1,
		c.product_id,
		s.from_qu_id,
		c.to_qu_id,
		s.factor * c.factor,
		'/' || s.from_qu_id || c.path
	FROM closure c
	JOIN conversion_factors s
		ON s.to_qu_id = c.from_qu_id
		AND s.product_id IS NULL
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Do this only, if there is no product_specific conversion between the units in s
		AND c.path NOT LIKE ('%/' || s.from_qu_id || '/%') -- Prevent cycles

	UNION

	-- Third recursive case: Add a default unit conversion to the *end* of the conversion chain
	SELECT
		c.depth + 1,
		c.product_id,
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor,
		c.path || s.to_qu_id || '/'
	FROM closure c
	JOIN conversion_factors s
		ON c.to_qu_id = s.from_qu_id
		AND s.product_id IS NULL
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Do this only, if there is no product_specific conversion between the units in s
		AND c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- Prevent cycles

	UNION

	-- Fourth case: Add the default unit conversions that are reachable by a given product.
	-- We cannot start with them directly, as we only want to add default conversions,
	-- where at least one of the units is 'reachable' from the product's stock quantity unit.
	-- Thus we add these cases here.
	SELECT DISTINCT
		1, c.product_id,
		s.from_qu_id, s.to_qu_id,
		s.factor,
		'/' || s.from_qu_id || '/' || s.to_qu_id || '/'
	FROM closure c, conversion_factors s
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id)
		AND c.path LIKE ('%/' || s.from_qu_id || '/' || s.to_qu_id || '/%') -- Prevent cycles
)

SELECT DISTINCT
	-1 AS id, -- Dummy, LessQL needs an id column
	c.product_id,
	c.from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	c.to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	FIRST_VALUE(factor) OVER win AS factor,
	FIRST_VALUE(c.path) OVER win AS path
FROM closure c
JOIN quantity_units qu_from
	ON c.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON c.to_qu_id = qu_to.id
GROUP BY product_id, from_qu_id, to_qu_id
WINDOW win AS (PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY depth ASC)
ORDER BY product_id, from_qu_id, to_qu_id;

DROP VIEW uihelper_stock_current_overview;
CREATE VIEW uihelper_stock_current_overview
AS
SELECT
	p.id,
	sc.amount_opened AS amount_opened,
	p.tare_weight AS tare_weight,
	p.enable_tare_weight_handling AS enable_tare_weight_handling,
	sc.amount AS amount,
	sc.value as value,
	sc.product_id AS product_id,
	sc.best_before_date AS best_before_date,
	EXISTS(SELECT id FROM stock_missing_products WHERE id = sc.product_id) AS product_missing,
	(SELECT name FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name,
	(SELECT name_plural FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name_plural,
	p.name AS product_name,
	(SELECT name FROM product_groups WHERE product_groups.id = p.product_group_id) AS product_group_name,
	EXISTS(SELECT * FROM shopping_list WHERE shopping_list.product_id = sc.product_id) AS on_shopping_list,
	(SELECT name FROM quantity_units WHERE quantity_units.id = p.qu_id_purchase) AS qu_purchase_unit_name,
	(SELECT name_plural FROM quantity_units WHERE quantity_units.id = p.qu_id_purchase) AS qu_purchase_unit_name_plural,
	sc.is_aggregated_amount,
	sc.amount_opened_aggregated,
	sc.amount_aggregated,
	p.calories AS product_calories,
	sc.amount * p.calories AS calories,
	sc.amount_aggregated * p.calories AS calories_aggregated,
	p.quick_consume_amount,
	p.due_type,
	plp.purchased_date AS last_purchased,
	plp.price AS last_price,
	pap.price as average_price,
	p.min_stock_amount,
	pbcs.barcodes AS product_barcodes,
	p.description AS product_description,
	l.name AS product_default_location_name,
	p_parent.id AS parent_product_id,
	p_parent.name AS parent_product_name,
	p.picture_file_name AS product_picture_file_name,
	p.no_own_stock AS product_no_own_stock,
	IFNULL(quc.factor, 1.0) AS product_qu_factor_purchase_to_stock
FROM (
	SELECT *
	FROM stock_current
	WHERE best_before_date IS NOT NULL
	UNION
	SELECT m.id, 0, 0, 0, null, 0, 0, 0, p.due_type
	FROM stock_missing_products m
	JOIN products p
		ON m.id = p.id
	WHERE m.id NOT IN (SELECT product_id FROM stock_current)
	) sc
LEFT JOIN products_last_purchased plp
	ON sc.product_id = plp.product_id
LEFT JOIN products_average_price pap
	ON sc.product_id = pap.product_id
LEFT JOIN products p
    ON sc.product_id = p.id
LEFT JOIN product_barcodes_comma_separated pbcs
	ON sc.product_id = pbcs.product_id
LEFT JOIN products p_parent
	ON p.parent_product_id = p_parent.id
LEFT JOIN locations l
	ON p.location_id = l.id
LEFT JOIN quantity_unit_conversions quc
	ON sc.product_id = quc.product_id
	AND p.qu_id_purchase = quc.from_qu_id
	AND p.qu_id_stock = quc.to_qu_id
WHERE p.hide_on_stock_overview = 0;

CREATE VIEW uihelper_stock_entries
AS
SELECT
	*,
	IFNULL(quc.factor, 1.0) AS product_qu_factor_purchase_to_stock
FROM stock s
JOIN products p
    ON s.product_id = p.id
LEFT JOIN quantity_unit_conversions quc
	ON s.product_id = quc.product_id
	AND p.qu_id_purchase = quc.from_qu_id
	AND p.qu_id_stock = quc.to_qu_id;
