ALTER TABLE products
ADD default_best_before_days_after_freezing INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET default_best_before_days_after_freezing = 0;

ALTER TABLE products
ADD default_best_before_days_after_thawing INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET default_best_before_days_after_thawing = 0;

ALTER TABLE locations
ADD is_freezer TINYINT NOT NULL DEFAULT 0;

UPDATE locations
SET is_freezer = 0;

DROP VIEW stock_current_locations;
CREATE VIEW stock_current_locations
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	s.location_id AS location_id,
	l.name AS location_name,
	l.is_freezer AS location_is_freezer
FROM stock s
JOIN locations l
	ON s.location_id = l.id
GROUP BY s.product_id, s.location_id, l.name;
