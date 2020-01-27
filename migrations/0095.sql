CREATE TRIGGER set_products_default_location_if_empty_stock AFTER INSERT ON stock
BEGIN
	UPDATE stock
	SET location_id = (SELECT location_id FROM products where id = product_id)
	WHERE id = NEW.id
		AND location_id IS NULL;
END;

UPDATE stock
SET location_id = (SELECT location_id FROM products where id = product_id)
WHERE location_id IS NULL;

CREATE TRIGGER set_products_default_location_if_empty_stock_log AFTER INSERT ON stock_log
BEGIN
	UPDATE stock_log
	SET location_id = (SELECT location_id FROM products where id = product_id)
	WHERE id = NEW.id
		AND location_id IS NULL;
END;

UPDATE stock_log
SET location_id = (SELECT location_id FROM products where id = product_id)
WHERE location_id IS NULL;

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

ALTER TABLE recipes
ADD product_id INTEGER;

DROP VIEW recipes_resolved;
CREATE VIEW recipes_resolved
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	r.id AS recipe_id,
	IFNULL(MIN(rpr.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rpr.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list, (
	SELECT COUNT(*) FROM recipes_pos_resolved WHERE recipe_id = r.id AND need_fulfilled = 0) AS missing_products_count,
	IFNULL(SUM(rpr.costs), 0) AS costs,
	IFNULL(SUM(rpr.calories), 0) AS calories
FROM recipes r
LEFT JOIN recipes_pos_resolved rpr
	ON r.id = rpr.recipe_id
GROUP BY r.id;
