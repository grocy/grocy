DROP VIEW IF EXISTS quantity_unit_conversions_resolved;

CREATE VIEW quantity_unit_conversions_resolved AS
WITH RECURSIVE
	single(product_id, from_qu_id, to_qu_id, factor, source)
AS (
	-- 1. Product "purchase to stock" conversion factor
	SELECT
		p.id AS product_id,
		p.qu_id_purchase AS from_qu_id,
		p.qu_id_stock AS to_qu_id,
		CASE WHEN p.qu_id_stock = p.qu_id_purchase THEN 1.0 ELSE p.qu_factor_purchase_to_stock END AS factor, -- Enforce a factor of 1 when QU stock = QU purchase
		'1 product purchase to stock factor' AS source
	FROM products p
	UNION -- Inversed
	SELECT
		p.id AS product_id,
		p.qu_id_stock AS from_qu_id,
		p.qu_id_purchase AS to_qu_id,
		1 / p.qu_factor_purchase_to_stock AS factor,
		'1 product purchase to stock factor (inversed)' AS source
	FROM products p
	WHERE p.qu_id_stock != p.qu_id_purchase -- => Only when QU stock is not the same as QU purchase

	UNION

	-- 2. Product specific QU overrides
	SELECT
		p.id AS product_id,
		quc.from_qu_id AS from_qu_id,
		quc.to_qu_id AS to_qu_id,
		quc.factor AS factor,
		'2 product override' AS source
	FROM products p
	JOIN quantity_unit_conversions quc
		ON p.id = quc.product_id

	UNION

	-- 3. Default (direct) QU conversion factors
	SELECT
		p.id AS product_id,
		p.qu_id_stock AS from_qu_id,
		quc.to_qu_id AS to_qu_id,
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
		p.id AS product_id,
		(SELECT from_qu_id FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) AS from_qu_id,
		quc.from_qu_id AS to_qu_id,
		(SELECT factor FROM quantity_unit_conversions WHERE to_qu_id = quc.to_qu_id AND product_id = p.id) / quc.factor AS factor,
		'4 default indirect factor' AS source
	FROM products p
	JOIN product_qu_relations pqr
		ON p.id = pqr.product_id
	JOIN quantity_unit_conversions quc
		ON pqr.qu_id = quc.from_qu_id
		AND quc.product_id IS NULL
	WHERE NOT EXISTS(SELECT 1 FROM quantity_unit_conversions qucx WHERE qucx.product_id = p.id AND qucx.from_qu_id = pqr.qu_id) -- => Product override exists
),
	closure(depth, product_id, path, from_qu_id, to_qu_id, factor, source)
AS (
	SELECT
		1 as depth, product_id,
		-- We need to keep track of the conversion path in order to prevent infinite loops
		'/' || from_qu_id || '/' || to_qu_id || '/',
		from_qu_id, to_qu_id,
		factor, source
	FROM single
	
	UNION
	
	SELECT
		c.depth + 1, c.product_id,
		c.path || s.to_qu_id || '/',
		c.from_qu_id, s.to_qu_id,
		c.factor * s.factor,
		c.source || ' ~~> ' || s.source
	FROM closure c
	JOIN single s
		ON  c.product_id = s.product_id
		AND c.to_qu_id = s.from_qu_id
	WHERE
		c.path NOT LIKE ('%/' || s.to_qu_id || '/%')
),
	closure_rn(row_num, product_id, from_qu_id, to_qu_id, factor, source)
AS (
	SELECT
		ROW_NUMBER() OVER(PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY depth ASC),
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		source
	FROM closure
)

SELECT DISTINCT
	-1 AS id, -- Dummy, LessQL needs an id column
	c.product_id,
	c.from_qu_id,
	qu_from.name AS from_qu_name,
	qu_from.name_plural AS from_qu_name_plural,
	c.to_qu_id,
	qu_to.name AS to_qu_name,
	qu_to.name_plural AS to_qu_name_plural,
	factor,
	source
FROM closure_rn c
	JOIN quantity_units qu_from
		ON c.from_qu_id = qu_from.id
	JOIN quantity_units qu_to
		ON c.to_qu_id = qu_to.id
WHERE
	c.row_num = 1
ORDER BY
	product_id, from_qu_id, to_qu_id;
