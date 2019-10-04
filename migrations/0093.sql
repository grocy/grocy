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
)
SELECT
	*,
	1 AS id -- Dummy, LessQL needs an id column
FROM r1;

CREATE TRIGGER prevent_self_nested_recipes_INS BEFORE INSERT ON recipes_nestings
BEGIN
SELECT CASE WHEN((
	SELECT 1
	FROM recipes_nestings
	WHERE NEW.recipe_id = NEW.includes_recipe_id
	)
	NOTNULL) THEN RAISE(ABORT, "Recursive nested recipe detected") END;
END;

CREATE TRIGGER prevent_self_nested_recipes_UPD BEFORE UPDATE ON recipes_nestings
BEGIN
SELECT CASE WHEN((
	SELECT 1
	FROM recipes_nestings
	WHERE NEW.recipe_id = NEW.includes_recipe_id
	)
	NOTNULL) THEN RAISE(ABORT, "Recursive nested recipe detected") END;
END;

DELETE FROM recipes_nestings
WHERE recipe_id = includes_recipe_id;
