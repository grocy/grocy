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

DROP VIEW stock_current_locations;
CREATE VIEW stock_current_locations AS
SELECT 
	s.id,
	s.product_id,
	IFNULL(s.location_id, p.location_id) AS location_id,
	l.name AS name
	FROM stock s
	JOIN products p ON s.product_id = p.id
	JOIN locations l on IFNULL(s.location_id, p.location_id) = l.id
GROUP BY s.product_id, IFNULL(s.location_id, p.location_id);
