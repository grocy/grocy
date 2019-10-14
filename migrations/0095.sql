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
GROUP BY s.product_id, IFNULL(s.location_id, p.location_id)
