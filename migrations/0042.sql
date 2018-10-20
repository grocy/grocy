CREATE TRIGGER cascade_change_qu_id_stock AFTER UPDATE ON products
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products WHERE id = NEW.id)
	WHERE product_id IN (SELECT id FROM products WHERE id = NEW.id)
		AND only_check_single_unit_in_stock = 0;
END;
