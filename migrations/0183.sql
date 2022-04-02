DROP TRIGGER cascade_product_removal;
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

DROP TRIGGER cascade_chore_removal;
CREATE TRIGGER cascade_chore_removal AFTER DELETE ON chores
BEGIN
	DELETE FROM chores_log
	WHERE chore_id = OLD.id;

	DELETE FROM userfield_values
	WHERE object_id = OLD.id
		AND field_id IN (SELECT id FROM userfields WHERE entity = 'chores');
END;

DROP TRIGGER cascade_battery_removal;
CREATE TRIGGER cascade_battery_removal AFTER DELETE ON batteries
BEGIN
	DELETE FROM battery_charge_cycles
	WHERE battery_id = OLD.id;

	DELETE FROM userfield_values
	WHERE object_id = OLD.id
		AND field_id IN (SELECT id FROM userfields WHERE entity = 'batteries');
END;

CREATE TRIGGER cascade_userfield_removal AFTER DELETE ON userfields
BEGIN
	DELETE FROM userfield_values
	WHERE object_id = OLD.id
		AND field_id = OLD.id;
END;

DELETE FROM userfield_values
WHERE object_id NOT IN (SELECT id FROM products)
	AND field_id IN (SELECT id FROM userfields WHERE entity = 'products');

DELETE FROM userfield_values
WHERE object_id NOT IN (SELECT id FROM chores)
	AND field_id IN (SELECT id FROM userfields WHERE entity = 'chores');

DELETE FROM userfield_values
WHERE object_id NOT IN (SELECT id FROM batteries)
	AND field_id IN (SELECT id FROM userfields WHERE entity = 'batteries');
