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
	p.qu_factor_purchase_to_stock AS factor
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
	1 / p.qu_factor_purchase_to_stock AS factor
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
	quc.factor AS factor
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
	quc.factor AS factor
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
	quc.factor * (SELECT factor FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) AS factor
FROM products p
JOIN product_qu_relations pqr
	ON p.id = pqr.product_id
JOIN quantity_unit_conversions quc
	ON pqr.qu_id = quc.from_qu_id
	AND quc.product_id IS NULL
JOIN quantity_units qu_from
	ON (SELECT from_qu_id FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) = qu_from.id
JOIN quantity_units qu_to
	ON quc.from_qu_id = qu_to.id;
