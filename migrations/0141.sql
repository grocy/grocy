CREATE VIEW meal_plan_internal_recipe_relation
AS

-- Relation between a meal plan (day) and the corresponding internal recipe(s)

SELECT mp.day, r.id AS recipe_id
FROM meal_plan mp
JOIN recipes r
	ON r.name = CAST(mp.day AS TEXT)
	AND r.type = 'mealplan-day'

UNION

SELECT mp.day, r.id AS recipe_id
FROM meal_plan mp
JOIN recipes r
	ON r.name = STRFTIME('%Y-%W', mp.day)
	AND r.type = 'mealplan-week'

UNION

SELECT mp.day, r.id AS recipe_id
FROM meal_plan mp
JOIN recipes r
	ON r.name = CAST(mp.day AS TEXT) || '#' || CAST(mp.id AS TEXT)
	AND r.type = 'mealplan-shadow';

CREATE VIEW recipes_resolved2
AS

-- The same as recipes_resolved but without the column "missing_products_count" to improve performance when this is not needed

SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	r.id AS recipe_id,
	IFNULL(MIN(rpr.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rpr.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list,
	IFNULL(SUM(rpr.costs), 0) AS costs,
	IFNULL(SUM(rpr.calories), 0) AS calories
FROM recipes r
LEFT JOIN recipes_pos_resolved rpr
	ON r.id = rpr.recipe_id
GROUP BY r.id;
