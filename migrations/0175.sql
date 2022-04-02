DROP TRIGGER prevent_qu_stock_change_after_first_purchase;

CREATE TRIGGER cascade_change_qu_id_stock AFTER UPDATE ON products WHEN NEW.qu_id_stock != OLD.qu_id_stock
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
			AND source NOT LIKE '1%'
	) ISNULL)
	AND
	((
        SELECT 1
        FROM stock_log
		WHERE product_id = NEW.id
			AND NEW.qu_id_stock != OLD.qu_id_stock
    ) NOTNULL) THEN RAISE(ABORT, "qu_id_stock can only be changed when a corresponding QU conversion (old QU => new QU) exists when the product was once added to stock") END;

	UPDATE chores
	SET product_amount = product_amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id;

	UPDATE meal_plan
	SET product_amount = product_amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE type = 'product'
		AND product_id = NEW.id;

	UPDATE product_barcodes
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id
		AND amount IS NOT NULL;

	UPDATE recipes_pos
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id;

	UPDATE shopping_list
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id
		AND product_id IS NOT NULL;

	UPDATE stock
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0),
	price = price / IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id;

	UPDATE stock_log
	SET amount = amount * IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0),
	price = price / IFNULL((SELECT factor FROM quantity_unit_conversions_resolved WHERE product_id = NEW.id AND from_qu_id = OLD.qu_id_stock AND to_qu_id = NEW.qu_id_stock AND source NOT LIKE '1%'), 1.0)
	WHERE product_id = NEW.id;
END;
