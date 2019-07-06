DROP VIEW recipes_pos_resolved;
CREATE VIEW recipes_pos_resolved
AS

-- Multiplication by 1.0 to force conversion to float (REAL)

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END < 0 THEN ABS(IFNULL(sc.amount, 0) - (CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount, 0) + (CASE WHEN r.not_check_shoppinglist = 1 THEN 0 ELSE IFNULL(sl.amount, 0) END * p.qu_factor_purchase_to_stock) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END / p.qu_factor_purchase_to_stock) * pcp.last_price AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	rp.id, -- Just a dummy id column
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
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
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	1 AS need_fulfilled,
	0 AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END / p.qu_factor_purchase_to_stock) * pcp.last_price AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	rp.id, -- Just a dummy id column
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
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
