-- Delete once all barcodes without a valid product_id (those are not visible anywhere)
DELETE FROM product_barcodes
WHERE product_id NOT IN (SELECT id FROM products);

CREATE TRIGGER prevent_adding_barcodes_for_not_existing_products AFTER INSERT ON product_barcodes
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM products p
		WHERE id = NEW.product_id
	) ISNULL) THEN RAISE(ABORT, "product_id doesn't reference a existing product") END;
END;
