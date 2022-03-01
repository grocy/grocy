DROP VIEW stock_missing_products;
CREATE VIEW stock_missing_products
AS

-- Products WITHOUT sub products where the amount of the sub products SHOULD NOT be cumulated
SELECT
	p.id,
	MAX(p.name) AS name,
	p.min_stock_amount - IFNULL(SUM(s.amount), 0) + (CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END) AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products_view p
LEFT JOIN stock_current s
	ON p.id = s.product_id
WHERE p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 0
	AND p.has_sub_products = 0
	AND p.parent_product_id IS NULL
	AND IFNULL(p.active, 0) = 1
GROUP BY p.id
HAVING IFNULL(SUM(s.amount), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END < p.min_stock_amount

UNION

-- Parent products WITH sub products where the amount of the sub products SHOULD be cumulated
SELECT
	p.id,
	MAX(p.name) AS name,
	SUM(sub_p.min_stock_amount) - IFNULL(SUM(s.amount_aggregated), 0) + (CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END) AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products_view p
JOIN products_resolved pr
	ON p.id = pr.parent_product_id
JOIN products sub_p
	ON pr.sub_product_id = sub_p.id
LEFT JOIN stock_current s
	ON pr.sub_product_id = s.product_id
WHERE sub_p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 1
	AND IFNULL(p.active, 0) = 1
GROUP BY p.id
HAVING IFNULL(SUM(s.amount_aggregated), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END < SUM(sub_p.min_stock_amount)

UNION

-- Sub products where the amount SHOULD NOT be cumulated into the parent product
SELECT
	sub_p.id,
	MAX(sub_p.name) AS name,
	SUM(sub_p.min_stock_amount) - IFNULL(SUM(s.amount_aggregated), 0) + (CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END) AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products p
JOIN products_resolved pr
	ON p.id = pr.parent_product_id
JOIN products sub_p
	ON pr.sub_product_id = sub_p.id
LEFT JOIN stock_current s
	ON pr.sub_product_id = s.product_id
WHERE sub_p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 0
	AND IFNULL(p.active, 0) = 1
GROUP BY sub_p.id
HAVING IFNULL(SUM(s.amount), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END < sub_p.min_stock_amount;
