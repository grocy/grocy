CREATE TRIGGER remove_conversions AFTER DELETE ON quantity_units
BEGIN
	DELETE FROM quantity_unit_conversions
	WHERE from_qu_id = OLD.id
		OR to_qu_id = OLD.id;
END;

DELETE FROM quantity_unit_conversions
WHERE NOT EXISTS(SELECT 1 FROM quantity_units WHERE id = quantity_unit_conversions.from_qu_id)
	OR NOT EXISTS(SELECT 1 FROM quantity_units WHERE id = quantity_unit_conversions.to_qu_id);
