DROP VIEW recipes_nestings_resolved;
CREATE VIEW recipes_nestings_resolved
AS
WITH RECURSIVE r1(recipe_id, includes_recipe_id, includes_servings, level)
AS (
	SELECT
		id AS recipe_id,
		id AS includes_recipe_id,
		1 AS includes_servings,
		0 AS level
	FROM recipes

	UNION ALL

	SELECT
		rn.recipe_id,
		r1.includes_recipe_id,
		CASE WHEN r1.level = 0 THEN rn.servings ELSE (SELECT servings FROM recipes_nestings WHERE recipe_id = r1.recipe_id AND includes_recipe_id = r1.includes_recipe_id) END AS includes_servings,
		r1.level + 1 AS level
	FROM recipes_nestings rn, r1 r1
	WHERE rn.includes_recipe_id = r1.recipe_id
)
SELECT
	*,
	1 AS id -- Dummy, LessQL needs an id column
FROM r1;
