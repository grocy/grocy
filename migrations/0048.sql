PRAGMA legacy_alter_table = ON;

ALTER TABLE shopping_list RENAME TO shopping_list_old;

CREATE TABLE shopping_list (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INTEGER,
	note TEXT,
	amount INTEGER NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO shopping_list
	(product_id, amount, note, row_created_timestamp)
SELECT product_id, amount + amount_autoadded, note, row_created_timestamp
FROM shopping_list_old;

DROP TABLE shopping_list_old;

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
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	CASE WHEN IFNULL(sc.amount, 0) + (IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock) >= CASE WHEN rp.only_check_single_unit_in_stock = 1 THEN 1 ELSE IFNULL(rp.amount, 0) END THEN 1 ELSE 0 END AS need_fulfilled_with_shopping_list,
	rp.qu_id
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
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
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
WHERE rp.not_check_stock_fulfillment = 1;
