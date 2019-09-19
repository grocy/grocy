CREATE VIEW stock_missing_products_including_opened
AS
SELECT
	p.id,
	MAX(p.name) AS name,
	p.min_stock_amount - (IFNULL(SUM(s.amount), 0) - IFNULL(SUM(s.amount_opened), 0)) AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products p
LEFT JOIN stock_current s
	ON p.id = s.product_id
WHERE p.min_stock_amount != 0
GROUP BY p.id
HAVING IFNULL(SUM(s.amount), 0) - IFNULL(SUM(s.amount_opened), 0) < p.min_stock_amount;
