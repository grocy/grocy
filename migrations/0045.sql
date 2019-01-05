ALTER TABLE recipes_pos RENAME TO recipes_pos_old;

CREATE TABLE recipes_pos (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	recipe_id INTEGER NOT NULL,
	product_id INTEGER NOT NULL,
	amount REAL NOT NULL DEFAULT 0,
	note TEXT,
	qu_id INTEGER,
	only_check_single_unit_in_stock TINYINT NOT NULL DEFAULT 0,
	ingredient_group TEXT,
	not_check_stock_fulfillment TINYINT NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

DROP TRIGGER recipes_pos_qu_id_default;
CREATE TRIGGER recipes_pos_qu_id_default AFTER INSERT ON recipes_pos
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products where id = product_id)
	WHERE qu_id IS NULL
		AND id = NEW.id;
END;

INSERT INTO recipes_pos
	(recipe_id, product_id, amount, note, qu_id, only_check_single_unit_in_stock, ingredient_group, row_created_timestamp)
SELECT recipe_id, product_id, amount, note, qu_id, only_check_single_unit_in_stock, ingredient_group, row_created_timestamp
FROM recipes_pos_old;

DROP TABLE recipes_pos_old;

DROP TRIGGER cascade_change_qu_id_stock;
CREATE TRIGGER cascade_change_qu_id_stock AFTER UPDATE ON products
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products WHERE id = NEW.id)
	WHERE product_id IN (SELECT id FROM products WHERE id = NEW.id)
		AND only_check_single_unit_in_stock = 0;
END;

DROP VIEW recipes_fulfillment;
CREATE VIEW recipes_fulfillment
AS
SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	CASE WHEN IFNULL(sc.amount, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) END THEN 1 ELSE 0 END AS need_fulfilled,
	CASE WHEN IFNULL(sc.amount, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) END < 0 THEN ABS(IFNULL(sc.amount, 0) - CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) END) ELSE 0 END AS missing_amount,
	IFNULL(sl.amount, 0) AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount, 0) + IFNULL(sl.amount, 0) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
LEFT JOIN (
	SELECT product_id, SUM(amount + amount_autoadded) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
WHERE rp.not_check_stock_fulfillment = 0

UNION

-- Just add all recipe positions which should not be checked against stock with fulfilled need

SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id AS product_id,
	rp.amount AS recipe_amount,
	IFNULL(sc.amount, 0) AS stock_amount,
	1 AS need_fulfilled,
	0 AS missing_amount,
	IFNULL(sl.amount, 0) AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
LEFT JOIN (
	SELECT product_id, SUM(amount + amount_autoadded) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
WHERE rp.not_check_stock_fulfillment = 1;
