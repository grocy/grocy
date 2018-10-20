DROP VIEW stock_missing_products;
CREATE VIEW stock_missing_products
AS
SELECT
	p.id,
	MAX(p.name) AS name,
	p.min_stock_amount - IFNULL(SUM(s.amount), 0) AS amount_missing,
	CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products p
LEFT JOIN stock s
	ON p.id = s.product_id
WHERE p.min_stock_amount != 0
GROUP BY p.id
HAVING IFNULL(SUM(s.amount), 0) < p.min_stock_amount;

DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date
FROM stock
GROUP BY product_id

UNION

SELECT id, 0, null
FROM stock_missing_products
WHERE is_partly_in_stock = 0;
