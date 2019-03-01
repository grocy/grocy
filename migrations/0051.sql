ALTER TABLE stock
ADD location_id INTEGER;

ALTER TABLE stock_log
ADD location_id INTEGER;

CREATE VIEW stock_current_locations
AS
SELECT
	s.product_id,
	IFNULL(s.location_id, p.location_id) AS location_id
FROM stock s
JOIN products p
	ON s.product_id = p.id
GROUP BY s.product_id, IFNULL(s.location_id, p.location_id);
