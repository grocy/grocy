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

DROP VIEW recipes_fulfillment_sum;
CREATE VIEW recipes_fulfillment_sum
AS
SELECT
	r.id AS recipe_id,
	IFNULL(MIN(rf.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rf.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list,
	(SELECT COUNT(*) FROM recipes_fulfillment WHERE recipe_id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved rnr2 WHERE rnr2.recipe_id = r.id) AND need_fulfilled = 0 AND recipe_pos_id IS NOT NULL) AS missing_products_count
FROM recipes r
LEFT JOIN recipes_nestings_resolved rnr
	ON r.id = rnr.recipe_id
LEFT JOIN recipes_fulfillment rf
	ON rnr.includes_recipe_id = rf.recipe_id
GROUP BY r.id;

ALTER TABLE recipes_pos
ADD ingredient_group TEXT;
