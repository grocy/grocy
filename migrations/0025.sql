CREATE TABLE recipes (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE TABLE recipes_pos (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	recipe_id INTEGER NOT NULL,
	product_id INTEGER NOT NULL,
	amount INTEGER NOT NULL DEFAULT 0,
	note TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE VIEW recipes_fulfillment
AS
SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount, 0) >= rp.amount THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount, 0) - IFNULL(rp.amount, 0) < 0 THEN ABS(IFNULL(sc.amount, 0) - IFNULL(rp.amount, 0)) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount, 0) + IFNULL(sl.amount, 0) >= rp.amount THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
LEFT JOIN (
	SELECT product_id, SUM(amount + amount_autoadded) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id;

CREATE VIEW recipes_fulfillment_sum
AS
SELECT
	r.id AS recipe_id,
	IFNULL(MIN(rf.need_fulfilled), 1) AS need_fulfilled,
	IFNULL(MIN(rf.need_fulfilled_with_shopping_list), 1) AS need_fulfilled_with_shopping_list,
	(SELECT COUNT(*) FROM recipes_fulfillment WHERE recipe_id = rf.recipe_id AND need_fulfilled = 0 AND recipe_pos_id IS NOT NULL) AS missing_products_count
FROM recipes r
LEFT JOIN recipes_fulfillment rf
	ON rf.recipe_id = r.id
GROUP BY r.id;
