DROP VIEW quantity_unit_conversions_resolved;
CREATE VIEW quantity_unit_conversions_resolved
AS

-- 1. Product "purchase to stock" conversion factor
SELECT
	-1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.qu_id_purchase AS from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	p.qu_id_stock AS to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	p.qu_factor_purchase_to_stock AS factor,
	'1 product purchase to stock factor' AS source
FROM products p
JOIN quantity_units qu_from
	ON p.qu_id_purchase = qu_from.id
JOIN quantity_units qu_to
	ON p.qu_id_stock = qu_to.id
UNION -- Inversed
SELECT
	-1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.qu_id_stock AS from_qu_id,
	qu_to.name AS from_qu_name,
	qu_to.name_plural AS from_qu_name_plural,
	p.qu_id_purchase AS to_qu_id,
	qu_from.name AS to_qu_name,
	qu_from.name_plural AS to_qu_name_plural,
	1 / p.qu_factor_purchase_to_stock AS factor,
	'1 product purchase to stock factor (inversed)' AS source
FROM products p
JOIN quantity_units qu_from
	ON p.qu_id_purchase = qu_from.id
JOIN quantity_units qu_to
	ON p.qu_id_stock = qu_to.id

UNION

-- 2. Product specific QU overrides
SELECT
	-1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	quc.from_qu_id AS from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	quc.to_qu_id AS to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	quc.factor AS factor,
	'2 product override' AS source
FROM products p
JOIN quantity_unit_conversions quc
	ON p.id = quc.product_id
JOIN quantity_units qu_from
	ON quc.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON quc.to_qu_id = qu_to.id

UNION

-- 3. Default (direct) QU conversion factors
SELECT
	-1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.qu_id_stock AS from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	quc.to_qu_id AS to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	quc.factor AS factor,
	'3 default direct factor' AS source
FROM products p
JOIN quantity_unit_conversions quc
	ON p.qu_id_stock = quc.from_qu_id
	AND quc.product_id IS NULL
JOIN quantity_units qu_from
	ON quc.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON quc.to_qu_id = qu_to.id

UNION

-- 4. Default (indirect) QU conversion factors
SELECT
	-1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	(SELECT from_qu_id FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) AS from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	quc.from_qu_id AS to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	(SELECT factor FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) / quc.factor AS factor,
	'4 default indirect factor' AS source
FROM products p
JOIN product_qu_relations pqr
	ON p.id = pqr.product_id
JOIN quantity_unit_conversions quc
	ON pqr.qu_id = quc.from_qu_id
	AND quc.product_id IS NULL
JOIN quantity_units qu_from
	ON (SELECT from_qu_id FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) = qu_from.id
JOIN quantity_units qu_to
	ON quc.from_qu_id = qu_to.id
WHERE NOT EXISTS(SELECT 1 FROM quantity_unit_conversions qucx WHERE qucx.product_id = p.id AND qucx.from_qu_id = pqr.qu_id); -- => Product override exists

DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT
	pr.parent_product_id AS product_id,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id), 0) AS amount,
	SUM(s.amount * IFNULL(qucr.factor, 1.0)) AS amount_aggregated,
	IFNULL(ROUND((SELECT SUM(IFNULL(price,0) * amount) FROM stock WHERE product_id = pr.parent_product_id), 2), 0)  AS value,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id AND open = 1), 0) AS amount_opened,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = pr.parent_product_id) AND open = 1), 0) * IFNULL(qucr.factor, 1) AS amount_opened_aggregated,
    CASE WHEN COUNT(p_sub.parent_product_id) > 0  THEN 1 ELSE 0 END AS is_aggregated_amount,
	MAX(p_parent.due_type) AS due_type
FROM products_resolved pr
JOIN stock s
	ON pr.sub_product_id = s.product_id
JOIN products p_parent
	ON pr.parent_product_id = p_parent.id
	AND p_parent.active = 1
JOIN products p_sub
	ON pr.sub_product_id = p_sub.id
	AND p_sub.active = 1
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON pr.sub_product_id = qucr.product_id
	AND p_sub.qu_id_stock = qucr.from_qu_id
	AND p_parent.qu_id_stock = qucr.to_qu_id
GROUP BY pr.parent_product_id
HAVING SUM(s.amount) > 0

UNION

-- This is the same as above but sub products not rolled up (no QU conversion and column is_aggregated_amount = 0 here)
SELECT
	pr.sub_product_id AS product_id,
	SUM(s.amount) AS amount,
	SUM(s.amount) AS amount_aggregated,
	ROUND(SUM(IFNULL(s.price, 0) * s.amount), 2) AS value,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened_aggregated,
	0 AS is_aggregated_amount,
	MAX(p_sub.due_type) AS due_type
FROM products_resolved pr
JOIN stock s
	ON pr.sub_product_id = s.product_id
JOIN products p_sub
	ON pr.sub_product_id = p_sub.id
	AND p_sub.active = 1
WHERE pr.parent_product_id != pr.sub_product_id
GROUP BY pr.sub_product_id
HAVING SUM(s.amount) > 0;
