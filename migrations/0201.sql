DROP VIEW products_average_price;
CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	SUM(s.amount * s.price) / SUM(s.amount) as price
FROM stock s
GROUP BY s.product_id;
