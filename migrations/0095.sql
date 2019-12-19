CREATE TRIGGER set_products_default_location_if_empty_stock AFTER INSERT ON stock
BEGIN
	UPDATE stock
	SET location_id = (SELECT location_id FROM products where id = product_id)
	WHERE id = NEW.id
		AND location_id IS NULL;
END;

CREATE TRIGGER set_products_default_location_if_empty_stock_log AFTER INSERT ON stock_log
BEGIN
	UPDATE stock_log
	SET location_id = (SELECT location_id FROM products where id = product_id)
	WHERE id = NEW.id
		AND location_id IS NULL;
END;

ALTER TABLE stock_log
ADD correlation_id TEXT;

ALTER TABLE stock_log
ADD transaction_id TEXT;

ALTER TABLE stock_log
ADD stock_row_id INTEGER;

DROP VIEW stock_current_locations;
CREATE VIEW stock_current_locations
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	s.location_id AS location_id,
	l.name AS location_name
FROM stock s
JOIN locations l
	ON s.location_id = l.id
GROUP BY s.product_id, s.location_id, l.name;

ALTER TABLE locations
ADD freezer TINYINT DEFAULT 0;
