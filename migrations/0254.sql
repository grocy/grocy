-- Recreate triggers that use double quoted string literals / convert them to single quoted ones

DROP TRIGGER prevent_self_nested_recipes_INS;
CREATE TRIGGER prevent_self_nested_recipes_INS BEFORE INSERT ON recipes_nestings
BEGIN
SELECT CASE WHEN((
	SELECT 1
	FROM recipes_nestings
	WHERE NEW.recipe_id = NEW.includes_recipe_id
	)
	NOTNULL) THEN RAISE(ABORT, 'Recursive nested recipe detected') END;
END;

DROP TRIGGER prevent_self_nested_recipes_UPD;
CREATE TRIGGER prevent_self_nested_recipes_UPD BEFORE UPDATE ON recipes_nestings
BEGIN
SELECT CASE WHEN((
	SELECT 1
	FROM recipes_nestings
	WHERE NEW.recipe_id = NEW.includes_recipe_id
	)
	NOTNULL) THEN RAISE(ABORT, 'Recursive nested recipe detected') END;
END;

DROP TRIGGER prevent_infinite_nested_recipes_INS;
CREATE TRIGGER prevent_infinite_nested_recipes_INS BEFORE INSERT ON recipes_nestings
BEGIN
    SELECT CASE WHEN((
        SELECT 1
        FROM recipes_nestings_resolved rnr
        WHERE NEW.recipe_id = rnr.includes_recipe_id
            AND NEW.includes_recipe_id = rnr.recipe_id
    ) NOTNULL) THEN RAISE(ABORT, 'Recursive nested recipe detected') END;
END;

DROP TRIGGER prevent_infinite_nested_recipes_UPD;
CREATE TRIGGER prevent_infinite_nested_recipes_UPD BEFORE UPDATE ON recipes_nestings
BEGIN
    SELECT CASE WHEN((
        SELECT 1
        FROM recipes_nestings_resolved rnr
        WHERE NEW.recipe_id = rnr.includes_recipe_id
            AND NEW.includes_recipe_id = rnr.recipe_id
    ) NOTNULL) THEN RAISE(ABORT, 'Recursive nested recipe detected') END;
END;

DROP TRIGGER enfore_product_nesting_level;
CREATE TRIGGER enfore_product_nesting_level BEFORE UPDATE ON products
BEGIN
	-- Currently only 1 level is supported
    SELECT CASE WHEN((
        SELECT 1
        FROM products p
        WHERE IFNULL(NEW.parent_product_id, '') != ''
            AND IFNULL(parent_product_id, '') = NEW.id
    ) NOTNULL) THEN RAISE(ABORT, 'Unsupported product nesting level detected (currently only 1 level is supported)') END;
END;

DROP TRIGGER prevent_internal_meal_plan_section_removal;
CREATE TRIGGER prevent_internal_meal_plan_section_removal BEFORE DELETE ON meal_plan_sections
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM meal_plan_sections
		WHERE id = OLD.id
			AND id = -1
	) NOTNULL) THEN RAISE(ABORT, 'This is an internally used/required default section and therefore can''t be deleted') END;
END;

DROP TRIGGER cascade_change_qu_id_stock;
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
    ) NOTNULL) THEN RAISE(ABORT, 'qu_id_stock can only be changed when a corresponding QU conversion (old QU => new QU) exists when the product was once added to stock') END;

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

DROP TRIGGER prevent_adding_no_own_stock_products_to_stock;
CREATE TRIGGER prevent_adding_no_own_stock_products_to_stock AFTER INSERT ON stock
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM products p
		WHERE id = NEW.product_id
			AND no_own_stock = 1
	) NOTNULL) THEN RAISE(ABORT, 'no_own_stock = 1 products can''t be added to stock') END;
END;

DROP TRIGGER qu_conversions_custom_constraint_INS;
CREATE TRIGGER qu_conversions_custom_constraint_INS BEFORE INSERT ON quantity_unit_conversions
BEGIN
	/*
		Necessary because unique constraints don''t include NULL values in SQLite
	*/
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
	)
	NOTNULL) THEN RAISE(ABORT, 'QU conversion already exists') END;
END;

DROP TRIGGER prevent_adding_barcodes_for_not_existing_products;
CREATE TRIGGER prevent_adding_barcodes_for_not_existing_products AFTER INSERT ON product_barcodes
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM products p
		WHERE id = NEW.product_id
	) ISNULL) THEN RAISE(ABORT, 'product_id doesn''t reference a existing product') END;
END;

DROP TRIGGER recipes_pos_qu_id_default;
CREATE TRIGGER recipes_pos_qu_id_default AFTER INSERT ON recipes_pos
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products where id = product_id)
	WHERE id = NEW.id
		AND IFNULL(qu_id, '') = '';

	SELECT CASE WHEN((
		SELECT 1
		FROM recipes_pos rp
		JOIN quantity_unit_conversions_resolved qucr
			ON qucr.product_id = rp.product_id
			AND qucr.to_qu_id = rp.qu_id
		WHERE rp.id = NEW.id

		UNION

		-- only_check_single_unit_in_stock = 1 ingredients can have any QU
		SELECT 1
		FROM recipes_pos rp
		WHERE rp.id = NEW.id
			AND IFNULL(rp.only_check_single_unit_in_stock, 0) = 1
	) ISNULL) THEN RAISE(ABORT, 'Provided qu_id doesn''t have a related conversion for that product') END;
END;

DROP TRIGGER qu_conversions_custom_constraint_UPD;
CREATE TRIGGER qu_conversions_custom_constraint_UPD BEFORE UPDATE ON quantity_unit_conversions
BEGIN
	/* This contains practically the same logic as the trigger qu_conversions_custom_constraint_INS */

	/*
		Necessary because unique constraints don''t include NULL values in SQLite
	*/
SELECT CASE WHEN((
	SELECT 1
	FROM quantity_unit_conversions
	WHERE from_qu_id = NEW.from_qu_id
		AND to_qu_id = NEW.to_qu_id
		AND IFNULL(product_id, 0) = IFNULL(NEW.product_id, 0)
		AND id != NEW.id
	)
	NOTNULL) THEN RAISE(ABORT, 'QU conversion already exists') END;
END;
