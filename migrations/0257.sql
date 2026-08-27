-- Remove all self referencing parent product definitions
UPDATE products
SET parent_product_id = null
WHERE id = parent_product_id;

CREATE TRIGGER prevent_self_referenced_parent_product_INS AFTER INSERT ON products
BEGIN
SELECT CASE WHEN((
	SELECT 1
	FROM products p
	WHERE p.id = NEW.id
		AND p.id = p.parent_product_id
	)
	NOTNULL) THEN RAISE(ABORT, 'A product cannot reference itself as a parent product') END;
END;

CREATE TRIGGER prevent_self_referenced_parent_product_UPD AFTER UPDATE ON products
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM products p
		WHERE p.id = NEW.id
			AND p.id = p.parent_product_id
    ) NOTNULL) THEN RAISE(ABORT, 'A product cannot reference itself as a parent product') END;
END;
