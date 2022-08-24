DROP TRIGGER qu_conversions_inverse_UPD;
CREATE TRIGGER qu_conversions_inverse_UPD AFTER UPDATE ON quantity_unit_conversions
BEGIN
	/*
		Update the inverse QU conversion
	*/

	UPDATE quantity_unit_conversions
	SET factor = 1 / IFNULL(NEW.factor, 1),
	from_qu_id = NEW.to_qu_id,
	to_qu_id = NEW.from_qu_id
	WHERE from_qu_id = OLD.to_qu_id
		AND to_qu_id = OLD.from_qu_id
		AND IFNULL(product_id, -1) = IFNULL(NEW.product_id, -1);
END;
