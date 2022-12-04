DROP VIEW quantity_unit_conversions_resolved;

CREATE VIEW quantity_unit_conversions_resolved
AS

/*
	First, determine conversions that are a single step.
	There may be multiple definitions for conversions between two units
	(e.g. due to purchase-to-stock, product-specific and default conversions),
	thus priorities are used to disambiguate conversions.
	Later, we'll only use the factor with the highest priority to convert between two units.
*/

WITH RECURSIVE conversion_factors_dup(product_id, from_qu_id, to_qu_id, factor, priority)
AS (
	-- 1. Product "purchase to stock" factors ...
	SELECT
		p.id,
		p.qu_id_purchase,
		p.qu_id_stock,
		p.qu_factor_purchase_to_stock,
		50
	FROM products p
	WHERE p.qu_id_stock != p.qu_id_purchase
	UNION -- ... and the inverse factors
	SELECT
		p.id,
		p.qu_id_stock,
		p.qu_id_purchase,
		1.0 / p.qu_factor_purchase_to_stock,
		50
	FROM products p
	WHERE p.qu_id_stock != p.qu_id_purchase

	UNION

	-- 2. Product specific QU overrides
	-- Note that the quantity_unit_conversions table already contains both conversion directions for every conversion.
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		30
	FROM quantity_unit_conversions
	WHERE product_id IS NOT NULL

	UNION

	-- 3. Default QU conversions are handled in a later CTE, as we can't determine yet, for which products they are applicable.
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		factor,
		10
	FROM quantity_unit_conversions
	WHERE product_id IS NULL
),

-- Now, remove duplicate conversions, only retaining the entries with the highest priority
conversion_factors(product_id, from_qu_id, to_qu_id, factor)
AS (
	SELECT
		product_id,
		from_qu_id,
		to_qu_id,
		FIRST_VALUE(factor) OVER win
	FROM conversion_factors_dup
	GROUP BY product_id, from_qu_id, to_qu_id
	WINDOW win AS(PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY priority DESC)
),

-- Now build the closure of posisble conversions using a recursive CTE
closure(depth, product_id, path, from_qu_id, to_qu_id, factor)
AS (
	-- As a base case, select the conversions that refer to a concrete product
	SELECT
		1 as depth,
		product_id,
		'/' || from_qu_id || '/' || to_qu_id || '/', -- We need to keep track of the conversion path in order to prevent cycles
		from_qu_id,
		to_qu_id,
		factor
	FROM conversion_factors
	WHERE product_id IS NOT NULL

	UNION

	-- First recursive case: Add a product-associated conversion to the chain
	SELECT
		c.depth + 1,
		c.product_id,
		c.path || s.to_qu_id || '/',
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor
	FROM closure c
	JOIN conversion_factors s
		ON c.product_id = s.product_id
		AND c.to_qu_id = s.from_qu_id
	WHERE c.path NOT LIKE ('%/' || s.to_qu_id || '/%') -- prevent cycles

	UNION

	-- Second recursive case: Add a default unit conversion to the *start* of the conversion chain
	SELECT
		c.depth + 1,
		c.product_id,
		'/' || s.from_qu_id || c.path,
		s.from_qu_id,
		c.to_qu_id,
		s.factor * c.factor
	FROM closure c
	JOIN conversion_factors s
		ON s.to_qu_id = c.from_qu_id
		AND s.product_id IS NULL
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Do this only, if there is no product_specific conversion between the units in s
	AND c.path NOT LIKE ('%/' || s.from_qu_id || '/%')

	UNION

	-- Third recursive case: Add a default unit conversion to the *end* of the conversion chain
	SELECT
		c.depth + 1,
		c.product_id,
		c.path || s.to_qu_id || '/',
		c.from_qu_id,
		s.to_qu_id,
		c.factor * s.factor
	FROM closure c
	JOIN conversion_factors s
		ON c.to_qu_id = s.from_qu_id
		AND s.product_id IS NULL
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id) -- Do this only, if there is no product_specific conversion between the units in s
	AND c.path NOT LIKE ('%/' || s.to_qu_id || '/%')

	UNION

	-- Fourth case: Add the default unit conversions that are reachable by a given product.
	-- We cannot start with them directly, as we only want to add default conversions,
	-- where at least one of the units is 'reachable' from the product's storage quantity unit.
	-- Thus we add these cases here.
	SELECT DISTINCT
		1, c.product_id,
		'/' || s.from_qu_id || '/' || s.to_qu_id || '/',
		s.from_qu_id, s.to_qu_id,
		s.factor
	FROM closure c, conversion_factors s
	WHERE NOT EXISTS(SELECT 1 FROM conversion_factors ci WHERE ci.product_id = c.product_id AND ci.from_qu_id = s.from_qu_id AND ci.to_qu_id = s.to_qu_id)
	AND c.path LIKE ('%/' || s.from_qu_id || '/' || s.to_qu_id || '/%')
)

SELECT *
FROM (
	SELECT DISTINCT
		-1 AS id, -- Dummy, LessQL needs an id column
		c.product_id,
		c.from_qu_id,
		qu_from.name AS from_qu_name,
		qu_from.name_plural AS from_qu_name_plural,
		c.to_qu_id,
		qu_to.name AS to_qu_name,
		qu_to.name_plural AS to_qu_name_plural,
		FIRST_VALUE(factor) OVER (PARTITION BY product_id, from_qu_id, to_qu_id ORDER BY depth ASC) AS factor,
		c.path AS source
	FROM closure c
	JOIN quantity_units qu_from
		ON c.from_qu_id = qu_from.id
	JOIN quantity_units qu_to
		ON c.to_qu_id = qu_to.id

	UNION

	-- Also return a conversion from/to the products stock QU (with a factor of 1)
	SELECT
		-1 AS id, -- Dummy, LessQL needs an id column
		p.id AS product_id,
		p.qu_id_stock AS from_qu_id,
		qu.name AS from_qu_name,
		qu.name_plural AS from_qu_name_plural,
		p.qu_id_stock AS to_qu_id,
		qu.name AS to_qu_name,
		qu.name_plural AS to_qu_name_plural,
		1.0 AS factor,
		'/' || p.qu_id_stock AS source
	FROM products p
	JOIN quantity_units qu
		ON p.qu_id_stock = qu.id
) x
ORDER BY x.product_id, x.from_qu_id, x.to_qu_id;
