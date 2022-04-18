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
WHERE p.qu_id_stock != p.qu_id_purchase -- => Only when QU stock is not the same as QU purchase

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

DROP VIEW recipes_pos_resolved;
CREATE VIEW recipes_pos_resolved
AS

-- Multiplication by 1.0 to force conversion to float (REAL)

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END AS recipe_amount,
	IFNULL(sc.amount_aggregated, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 0.00000001 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 0.00000001 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END < 0 THEN ABS(IFNULL(sc.amount_aggregated, 0) - (CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) + (CASE WHEN r.not_check_shoppinglist = 1 THEN 0 ELSE IFNULL(sl.amount, 0) END * p.qu_factor_purchase_to_stock) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 0.00000001 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * rp.amount * IFNULL(pop.price, 0) * rp.price_factor * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	pg.name as product_group,
	rp.id, -- Just a dummy id column
	r.type as recipe_type,
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount,
	rp.only_check_single_unit_in_stock,
	rp.amount / r.base_servings*1.0 * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * IFNULL(p.calories, 0) * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS calories,
	p.active AS product_active
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN product_groups pg
	ON p.product_group_id = pg.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
LEFT JOIN products_oldest_stock_unit_price pop
	ON rp.product_id = pop.product_id
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON rp.product_id = qucr.product_id
	AND rp.qu_id  = qucr.from_qu_id
	AND p.qu_id_stock = qucr.to_qu_id
WHERE rp.not_check_stock_fulfillment = 0

UNION

-- Just add all recipe positions which should not be checked against stock with fulfilled need

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END AS recipe_amount,
	IFNULL(sc.amount_aggregated, 0) AS stock_amount,
	1 AS need_fulfilled,
	0 AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * rp.amount * IFNULL(pop.price, 0) * rp.price_factor * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	pg.name as product_group,
	rp.id, -- Just a dummy id column
	r.type as recipe_type,
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount,
	rp.only_check_single_unit_in_stock,
	rp.amount / r.base_servings*1.0 * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * IFNULL(p.calories, 0) * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS calories,
	p.active AS product_active
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN product_groups pg
	ON p.product_group_id = pg.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
LEFT JOIN products_oldest_stock_unit_price pop
	ON rp.product_id = pop.product_id
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON rp.product_id = qucr.product_id
	AND rp.qu_id  = qucr.from_qu_id
	AND p.qu_id_stock = qucr.to_qu_id
WHERE rp.not_check_stock_fulfillment = 1;
