CREATE VIEW recipes_missing_product_counts
AS
SELECT
	recipe_id,
	COUNT(*) AS missing_products_count
FROM recipes_pos_resolved
WHERE need_fulfilled = 0
GROUP BY recipe_id;

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
	IFNULL(SUM(rpr.calories), 0) AS calories
FROM recipes r
LEFT JOIN recipes_pos_resolved rpr
	ON r.id = rpr.recipe_id
LEFT JOIN recipes_missing_product_counts rmpc
	ON r.id = rmpc.recipe_id
GROUP BY r.id;

DROP VIEW recipes_resolved2;
