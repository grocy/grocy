CREATE VIEW stock_missing_products
AS
SELECT p.id, MAX(p.name) AS name, p.min_stock_amount - IFNULL(SUM(s.amount), 0) AS amount_missing
FROM products p
LEFT JOIN stock s
	ON p.id = s.product_id
WHERE p.min_stock_amount != 0
GROUP BY p.id
HAVING IFNULL(SUM(s.amount), 0) < p.min_stock_amount
