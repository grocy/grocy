DROP VIEW quantity_unit_conversions_resolved;
CREATE VIEW quantity_unit_conversions_resolved
AS

WITH RECURSIVE

-- Default QU conversions are handled in a later CTE, as we can't determine yet, for which products they are applicable.
default_conversions(from_qu_id, to_qu_id, factor)
AS (
	SELECT
		from_qu_id,
		to_qu_id,
		factor
	FROM quantity_unit_conversions
	WHERE product_id IS NULL
),

-- First find the closure for all default conversions. This will allow for further pruning when looking for product closure.
default_closure(depth, from_qu_id, to_qu_id, factor, path)
AS (
	-- As a base case, select all available default conversions
	SELECT
		1 as depth,
		from_qu_id,
		to_qu_id,
		factor,
		'/' || from_qu_id || '/' || to_qu_id || '/' -- We need to keep track of the conversion path in order to prevent cycles
	FROM default_conversions

	UNION

	-- Recursive case: Find all paths
	SELECT
		c.depth + 1,
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor,
		c.path || s.to_qu_id || '/'
	FROM default_closure c
	JOIN default_conversions s
		ON c.to_qu_id = s.from_qu_id
	WHERE c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- Prevent cycles
		AND NOT EXISTS(SELECT 1 FROM default_conversions ci WHERE ci.from_qu_id = c.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Prune if one of the existing conversions repeats (saves a lot of processing time)

),

default_closure_distinct(from_qu_id, to_qu_id, factor, path)
AS (
	SELECT DISTINCT
		from_qu_id,
		to_qu_id,
		FIRST_VALUE(factor) OVER win AS factor,
		FIRST_VALUE(path) OVER win AS path
	FROM default_closure
	GROUP BY from_qu_id, to_qu_id
	WINDOW win AS (PARTITION BY from_qu_id, to_qu_id ORDER BY depth)
	ORDER BY from_qu_id, to_qu_id
),

product_conversions(product_id, from_qu_id, to_qu_id, factor)
AS (
	-- Priority 1: Product-specific QU overrides
	-- Note that the quantity_unit_conversions table already contains both conversion directions for every conversion.
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor
	FROM quantity_unit_conversions
	WHERE product_id IS NOT NULL

	UNION

	-- Priority 2: QU conversions with a factor of 1.0 from the stock unit to the stock unit
	SELECT
		id,
		qu_id_stock,
		qu_id_stock,
		1.0
	FROM products
),

product_closure(depth, product_id, from_qu_id, to_qu_id, factor, path)
AS (
	-- As a base case, select all available product-specific conversions
	SELECT
		1 as depth,
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		'/' || from_qu_id || '/' || to_qu_id || '/' -- We need to keep track of the conversion path in order to prevent cycles
	FROM product_conversions

	UNION

	-- Recursive case: Find all paths
	SELECT
		c.depth + 1,
		c.product_id,
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor,
		c.path || s.to_qu_id || '/'
	FROM product_closure c
	JOIN product_conversions s
		ON c.product_id = s.product_id
		AND c.to_qu_id = s.from_qu_id
	WHERE c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- Prevent cycles
		AND NOT EXISTS(SELECT 1 FROM product_conversions ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = c.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Prune if one of the existing conversions repeats (saves a lot of processing time)
),

product_closure_distinct(product_id, from_qu_id, to_qu_id, factor, path)
AS (
	SELECT DISTINCT
		product_id,
		from_qu_id,
		to_qu_id,
		FIRST_VALUE(factor) OVER win AS factor,
		FIRST_VALUE(path) OVER win AS path
	FROM product_closure
	GROUP BY product_id, from_qu_id, to_qu_id
	WINDOW win AS (PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY depth)
	ORDER BY product_id, from_qu_id, to_qu_id
),

-- Now we connect the two closures by adding the reachable conversions from product specific conversions to default conversions
product_reachable(product_id, from_qu_id, to_qu_id, factor, path)
AS (
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		path
	FROM product_closure_distinct

	UNION

	SELECT
		cd.product_id,
		dcd.from_qu_id,
		dcd.to_qu_id,
		dcd.factor,
		'/' || dcd.from_qu_id || '/' || dcd.to_qu_id || '/'
	FROM product_closure_distinct cd
	JOIN default_closure_distinct dcd
		ON cd.to_qu_id = dcd.from_qu_id
		OR cd.to_qu_id = dcd.to_qu_id
	WHERE NOT EXISTS(SELECT 1 FROM product_closure_distinct ci WHERE ci.product_id = cd.product_id AND ci.from_qu_id = dcd.from_qu_id AND ci.to_qu_id = dcd.to_qu_id)
),

product_reachable_distinct(product_id, from_qu_id, to_qu_id, factor, path)
AS (
	SELECT DISTINCT
		product_id,
		from_qu_id,
		to_qu_id,
		FIRST_VALUE(factor) OVER win AS factor,
		FIRST_VALUE(path) OVER win AS path
	FROM product_reachable
	GROUP BY product_id, from_qu_id, to_qu_id
	WINDOW win AS (PARTITION BY product_id, from_qu_id, to_qu_id)
	ORDER BY product_id, from_qu_id, to_qu_id
),

-- Finally we build the combined closure
closure_final(depth, product_id, from_qu_id, to_qu_id, factor, path)
AS (
	-- As a base case, select the product closure
	SELECT
		1,
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		path -- We need to keep track of the conversion path in order to prevent cycles
	FROM product_reachable_distinct

	UNION

	-- Add a default unit conversion to the *end* of the conversion chain
	SELECT
		c.depth + 1,
		c.product_id,
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor,
		c.path || s.to_qu_id || '/'
	FROM closure_final c
	JOIN product_reachable_distinct s
		ON c.product_id = s.product_id
		AND c.to_qu_id = s.from_qu_id
	WHERE c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- Prevent cycles
		AND NOT EXISTS(SELECT 1 FROM product_reachable_distinct ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = c.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Prune (if already exists)
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
	FIRST_VALUE(c.factor) OVER win AS factor,
	FIRST_VALUE(c.path) OVER win AS path
FROM closure_final c
JOIN quantity_units qu_from
	ON c.from_qu_id = qu_from.id
JOIN quantity_units qu_to
	ON c.to_qu_id = qu_to.id
GROUP BY c.product_id, c.from_qu_id, c.to_qu_id
WINDOW win AS (PARTITION BY c.product_id, c.from_qu_id, c.to_qu_id ORDER BY c.depth)
ORDER BY c.product_id, c.from_qu_id, c.to_qu_id;
