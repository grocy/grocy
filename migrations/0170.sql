CREATE TRIGGER default_qu_INS AFTER INSERT ON product_barcodes
BEGIN
	UPDATE product_barcodes
	SET qu_id = (SELECT qu_id_stock FROM products WHERE id = product_barcodes.product_id)
	WHERE id = NEW.id
		AND IFNULL(qu_id, 0) = 0;
END;

CREATE TRIGGER default_qu_UPD AFTER UPDATE ON product_barcodes
BEGIN
	UPDATE product_barcodes
	SET qu_id = (SELECT qu_id_stock FROM products WHERE id = product_barcodes.product_id)
	WHERE id = NEW.id
		AND IFNULL(qu_id, 0) = 0;
END;

UPDATE product_barcodes
SET qu_id = (SELECT qu_id_stock FROM products WHERE id = product_barcodes.product_id)
WHERE IFNULL(qu_id, 0) = 0;
