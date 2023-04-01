DROP VIEW meal_plan_internal_recipe_relation;
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
	ON r.name = LTRIM(STRFTIME('%Y-%W', mp.day), '0')
	AND r.type = 'mealplan-week'

UNION

SELECT mp.day, r.id AS recipe_id
FROM meal_plan mp
JOIN recipes r
	ON r.name = CAST(mp.day AS TEXT) || '#' || CAST(mp.id AS TEXT)
	AND r.type = 'mealplan-shadow';
