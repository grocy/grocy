-- Remove QU conversions which are already defined by the products qu_factor_purchase_to_stock
DELETE FROM quantity_unit_conversions
WHERE id IN (
	SELECT quc.id
	FROM quantity_unit_conversions quc
	JOIN products p
		ON quc.product_id = p.id
	WHERE (p.qu_id_purchase = quc.to_qu_id AND p.qu_id_stock = quc.from_qu_id)
		OR (p.qu_id_purchase = quc.from_qu_id AND p.qu_id_stock = quc.to_qu_id)
);

DROP TRIGGER quantity_unit_conversions_custom_unique_constraint_INS;
CREATE TRIGGER qu_conversions_custom_constraint_INS BEFORE INSERT ON quantity_unit_conversions
BEGIN
	/*
		Necessary because unique constraints don't include NULL values in SQLite,
		and also because the constraint should include the products default conversion factor
	*/
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
	UNION
	SELECT 1
	FROM products
	WHERE id = NEW.product_id
		AND qu_id_purchase = NEW.to_qu_id
		AND qu_id_stock = NEW.from_qu_id
	)
	NOTNULL) THEN RAISE(ABORT, "QU conversion already exists") END;
END;

DROP TRIGGER quantity_unit_conversions_custom_unique_constraint_UPD;
CREATE TRIGGER qu_conversions_custom_constraint_UPD BEFORE UPDATE ON quantity_unit_conversions
BEGIN
	/* This contains practically the same logic as the trigger qu_conversions_custom_constraint_INS */

	/*
		Necessary because unique constraints don't include NULL values in SQLite,
		and also because the constraint should include the products default conversion factor
	*/
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
	UNION
	SELECT 1
	FROM products
	WHERE id = NEW.product_id
		AND qu_id_purchase = NEW.to_qu_id
		AND qu_id_stock = NEW.from_qu_id
	)
	NOTNULL) THEN RAISE(ABORT, "QU conversion already exists") END;
END;

CREATE TRIGGER qu_conversions_inverse_INS AFTER INSERT ON quantity_unit_conversions
BEGIN
	/*
		Create the inverse QU conversion
	*/

	INSERT OR REPLACE INTO quantity_unit_conversions
		(from_qu_id, to_qu_id, factor, product_id)
	VALUES
		(NEW.to_qu_id, NEW.from_qu_id, 1 / IFNULL(NEW.factor, 1), NEW.product_id);
END;

CREATE TRIGGER qu_conversions_inverse_UPD AFTER UPDATE ON quantity_unit_conversions
BEGIN
	/*
		Update the inverse QU conversion
	*/

	UPDATE quantity_unit_conversions
	SET factor = 1 / IFNULL(NEW.factor, 1)
	WHERE from_qu_id = NEW.to_qu_id
		AND to_qu_id = NEW.from_qu_id
		AND IFNULL(product_id, -1) = IFNULL(NEW.product_id, -1);
END;

CREATE TRIGGER qu_conversions_inverse_DEL AFTER DELETE ON quantity_unit_conversions
BEGIN
	/*
		Delete the inverse QU conversion
	*/

	DELETE FROM quantity_unit_conversions
	WHERE from_qu_id = OLD.to_qu_id
		AND to_qu_id = OLD.from_qu_id
		AND IFNULL(product_id, -1) = IFNULL(OLD.product_id, -1);
END;
