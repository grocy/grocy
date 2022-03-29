CREATE VIEW products_volatile_status
AS
SELECT
	-1 AS id, -- Dummy
	p.id AS product_id,
	p.name AS product_name,
	CASE WHEN JULIANDAY(sc.best_before_date) - JULIANDAY('now', 'localtime') < 0 THEN
		CASE WHEN p.due_type = 1 THEN 'overdue' ELSE 'expired' END
	ELSE
		CASE WHEN JULIANDAY(sc.best_before_date) - JULIANDAY('now', 'localtime') < CAST(grocy_user_setting('stock_due_soon_days') AS INT) THEN
			'due_soon'
		ELSE
			'ok'
		END
	END AS current_due_status,
	CASE WHEN smp.id IS NOT NULL THEN 1 ELSE 0 END AS is_currently_below_min_stock_amount
FROM products p
LEFT JOIN stock_current sc
	ON p.id = sc.product_id
LEFT JOIN stock_missing_products smp
	ON p.id = smp.id;

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
	CASE WHEN IFNULL(sc.amount_aggregated, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 0.00000001 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END < 0 THEN ABS(IFNULL(sc.amount_aggregated, 0) - (CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN ROUND(IFNULL(sc.amount_aggregated, 0) + (CASE WHEN r.not_check_shoppinglist = 1 THEN 0 ELSE IFNULL(sl.amount, 0) END * p.qu_factor_purchase_to_stock), 2) >= ROUND(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 0.00000001 ELSE CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) ELSE rp.amount * ((r.desired_servings*1.0) / (r.base_servings*1.0)) * ((rnr.includes_servings*1.0) / (rnrr.base_servings*1.0)) END END, 2) THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * rp.amount * IFNULL(pop.price, IFNULL(plp.price, 0)) * rp.price_factor * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS costs,
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
	p.active AS product_active,
	CASE pvs.current_due_status
		WHEN 'ok' THEN 0
		WHEN 'due_soon' THEN 1
		WHEN 'overdue' THEN 10
		WHEN 'expired' THEN 20
	END AS due_score
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
JOIN products_volatile_status pvs
	ON rp.product_id = pvs.product_id
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
LEFT JOIN products_last_purchased plp
	ON rp.product_id = plp.product_id
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
	(r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * rp.amount * IFNULL(pop.price, IFNULL(plp.price, 0)) * rp.price_factor * CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN IFNULL(qucr.factor, 1) ELSE 1 END AS costs,
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
	p.active AS product_active,
	CASE pvs.current_due_status
		WHEN 'ok' THEN 0
		WHEN 'due_soon' THEN 1
		WHEN 'overdue' THEN 10
		WHEN 'expired' THEN 20
	END AS due_score
FROM recipes r
JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
JOIN recipes rnrr
	ON rnr.includes_recipe_id = rnrr.id
JOIN recipes_pos rp
	ON rnr.includes_recipe_id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
JOIN products_volatile_status pvs
	ON rp.product_id = pvs.product_id
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
LEFT JOIN products_last_purchased plp
	ON rp.product_id = plp.product_id
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON rp.product_id = qucr.product_id
	AND rp.qu_id  = qucr.from_qu_id
	AND p.qu_id_stock = qucr.to_qu_id
WHERE rp.not_check_stock_fulfillment = 1;

DROP VIEW recipes_resolved;
CREATE VIEW recipes_resolved
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	r.id AS recipe_id,
	IFNULL(MIN(rpr.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rpr.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list,
	IFNULL(rmpc.missing_products_count, 0) AS missing_products_count,
	IFNULL(SUM(rpr.costs), 0) AS costs,
	IFNULL(SUM(rpr.costs) / CASE WHEN IFNULL(r.desired_servings, 0) = 0 THEN 1 ELSE r.desired_servings END, 0) AS costs_per_serving,
	IFNULL(SUM(rpr.calories), 0) AS calories,
	IFNULL(SUM(rpr.due_score), 0) AS due_score
FROM recipes r
LEFT JOIN recipes_pos_resolved rpr
	ON r.id = rpr.recipe_id
LEFT JOIN recipes_missing_product_counts rmpc
	ON r.id = rmpc.recipe_id
GROUP BY r.id;
