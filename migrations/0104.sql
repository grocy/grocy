-- Deprecate unused view to instead use products_last_purchased
DROP VIEW products_current_price;

CREATE VIEW products_last_purchased
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.amount,
	sl.best_before_date,
	sl.purchased_date,
	sl.price,
	sl.location_id,
	sl.shopping_location_id
	FROM stock_log sl
	JOIN (
		SELECT
			s1.product_id,
			MAX(s1.id) max_stock_id
			FROM stock_log s1
			JOIN (
					SELECT
						s.product_id,
						MAX(s.purchased_date) max_purchased_date
					FROM stock_log s
					WHERE undone = 0
						AND transaction_type in ('purchase', 'stock-edit-new', 'inventory-correction')
					GROUP BY s.product_id) sp2
				ON s1.product_id = sp2.product_id
				AND s1.purchased_date = sp2.max_purchased_date
			WHERE undone = 0
				AND transaction_type in ('purchase', 'stock-edit-new', 'inventory-correction')
			GROUP BY s1.product_id) sp3
		ON sl.product_id = sp3.product_id
		AND sl.id = sp3.max_stock_id;

CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	round(sum(s.amount * s.price) / sum(s.amount), 2) as price
FROM stock s
GROUP BY s.product_id;

CREATE VIEW products_oldest_stock_unit_price
AS
-- Find oldest best_before_date then oldest purchased_date then make sure to return one stock row using max
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sw.product_id,
	sw.amount,
	sw.best_before_date,
	sw.purchased_date,
	sw.price,
	sw.location_id,
	sw.shopping_location_id
	FROM stock sw
	JOIN (
			SELECT
				s1.product_id,
				MIN(s1.id) min_stock_id
			FROM stock s1
			JOIN (
				SELECT
					s.product_id,
					sp.oldest_date,
					MIN(s.purchased_date) min_purchased_date
				FROM stock s
				JOIN (
						SELECT
							product_id,
							MIN(best_before_date) as oldest_date
						FROM stock
						GROUP BY product_id) sp
					ON s.product_id = sp.product_id
						AND s.best_before_date = sp.oldest_date
				GROUP BY s.product_id, sp.oldest_date) sp2
			ON s1.product_id = sp2.product_id
				AND s1.best_before_date = sp2.oldest_date
				AND s1.purchased_date = sp2.min_purchased_date
			GROUP BY s1.product_id) sp3
ON sw.product_id = sp3.product_id
AND sw.id = sp3.min_stock_id;

DROP VIEW recipes_pos_resolved;
CREATE VIEW recipes_pos_resolved
AS

-- Multiplication by 1.0 to force conversion to float (REAL)

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) AS recipe_amount,
	IFNULL(sc.amount_aggregated, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END < 0 THEN ABS(IFNULL(sc.amount_aggregated, 0) - (CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount_aggregated, 0) + (CASE WHEN r.not_check_shoppinglist = 1 THEN 0 ELSE IFNULL(sl.amount, 0) END * p.qu_factor_purchase_to_stock) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END) * rp.amount * pop.price * rp.price_factor AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	pg.name as product_group,
	rp.id, -- Just a dummy id column
	r.type as recipe_type,
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount,
	rp.only_check_single_unit_in_stock,
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * IFNULL(p.calories, 0) AS calories,
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
WHERE rp.not_check_stock_fulfillment = 0

UNION

-- Just add all recipe positions which should not be checked against stock with fulfilled need

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) AS recipe_amount,
	IFNULL(sc.amount_aggregated, 0) AS stock_amount,
	1 AS need_fulfilled,
	0 AS missing_amount,
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id,
	(CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) END) * rp.amount * IFNULL(pop.price, 0) * rp.price_factor AS costs,
	CASE WHEN rnr.recipe_id = rnr.includes_recipe_id THEN 0 ELSE 1 END AS is_nested_recipe_pos,
	rp.ingredient_group,
	pg.name as product_group,
	rp.id, -- Just a dummy id column
	r.type as recipe_type,
	rnr.includes_recipe_id as child_recipe_id,
	rp.note,
	rp.variable_amount AS recipe_variable_amount,
	rp.only_check_single_unit_in_stock,
	rp.amount * (r.desired_servings*1.0 / r.base_servings*1.0) * (rnr.includes_servings*1.0 / CASE WHEN rnr.recipe_id != rnr.includes_recipe_id THEN rnrr.base_servings*1.0 ELSE 1 END) * IFNULL(p.calories, 0) AS calories,
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
WHERE rp.not_check_stock_fulfillment = 1;
