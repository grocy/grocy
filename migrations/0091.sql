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
SELECT
	*,
	1 AS id -- Dummy, LessQL needs an id column
FROM r1;
