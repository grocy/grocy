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
