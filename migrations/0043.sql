CREATE TABLE recipes_nestings (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	recipe_id INTEGER NOT NULL,
	includes_recipe_id INTEGER NOT NULL,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime')),

	UNIQUE(recipe_id, includes_recipe_id)
);

CREATE VIEW recipes_nestings_resolved
AS
WITH RECURSIVE r1(recipe_id, includes_recipe_id)
AS (
	SELECT id, id
	FROM recipes
	
	UNION ALL
	
	SELECT rn.recipe_id, r1.includes_recipe_id
	FROM recipes_nestings rn, r1 r1
	WHERE rn.includes_recipe_id = r1.recipe_id
	LIMIT 100 -- This is just a safety limit to prevent infinite loops due to infinite nested recipes
)
SELECT *
FROM r1;
