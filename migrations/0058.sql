ALTER TABLE recipes_nestings
ADD servings INTEGER DEFAULT 1;

DROP VIEW recipes_nestings_resolved;
CREATE VIEW recipes_nestings_resolved
AS
WITH RECURSIVE r1(recipe_id, includes_recipe_id, includes_servings)
AS (
	SELECT id, id, 1
	FROM recipes
	
	UNION ALL
	
	SELECT rn.recipe_id, r1.includes_recipe_id, rn.servings
	FROM recipes_nestings rn, r1 r1
	WHERE rn.includes_recipe_id = r1.recipe_id
	LIMIT 100 -- This is just a safety limit to prevent infinite loops due to infinite nested recipes
)
SELECT *
FROM r1;

DROP VIEW recipes_fulfillment;
CREATE VIEW recipes_pos_resolved
AS
SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount * (r.desired_servings / r.base_servings) * rnr.includes_servings AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) * (r.desired_servings / r.base_servings) * rnr.includes_servings END THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) * (r.desired_servings / r.base_servings) * rnr.includes_servings END < 0 THEN ABS(IFNULL(sc.amount, 0) - (CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) * (r.desired_servings / r.base_servings) * rnr.includes_servings END)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount, 0) + (CASE WHEN r.not_check_shoppinglist = 1 THEN 0 ELSE IFNULL(sl.amount, 0) END * p.qu_factor_purchase_to_stock) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) * (r.desired_servings / r.base_servings) * rnr.includes_servings END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings / r.base_servings) * rnr.includes_servings END / p.qu_factor_purchase_to_stock) * pcp.last_price AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	rp.id, -- Just a dummy id column
	rnr.includes_recipe_id as child_recipe_id
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
LEFT JOIN products_current_price pcp
	ON rp.product_id = pcp.product_id
WHERE rp.not_check_stock_fulfillment = 0

UNION

-- Just add all recipe positions which should not be checked against stock with fulfilled need

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount * (r.desired_servings / r.base_servings) * rnr.includes_servings AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	1 AS need_fulfilled,
	0 AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings / r.base_servings) * rnr.includes_servings END / p.qu_factor_purchase_to_stock) * pcp.last_price AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	rp.id, -- Just a dummy id column
	rnr.includes_recipe_id as child_recipe_id
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
LEFT JOIN products_current_price pcp
	ON rp.product_id = pcp.product_id
WHERE rp.not_check_stock_fulfillment = 1;

DROP VIEW recipes_fulfillment_sum;
CREATE VIEW recipes_resolved
AS
SELECT
	r.id AS recipe_id,
	IFNULL(MIN(rpr.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rpr.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list,
	(SELECT COUNT(*) FROM recipes_pos_resolved WHERE recipe_id = r.id AND need_fulfilled = 0) AS missing_products_count,
	SUM(rpr.costs) AS costs
FROM recipes r
LEFT JOIN recipes_pos_resolved rpr
	ON r.id = rpr.recipe_id
GROUP BY r.id;
