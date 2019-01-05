DROP TRIGGER cascade_change_qu_id_stock;
CREATE TRIGGER cascade_change_qu_id_stock AFTER UPDATE ON products
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products WHERE id = NEW.id)
	WHERE product_id IN (SELECT id FROM products WHERE id = NEW.id)
		AND only_check_single_unit_in_stock = 0;
END;

UPDATE recipes_pos
SET qu_id = (SELECT qu_id_stock FROM products WHERE id = recipes_pos.product_id)
WHERE only_check_single_unit_in_stock = 0;

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
	IFNULL(sl.amount, 0) * p.qu_factor_purchase_to_stock AS amount_on_shopping_list,
	1 AS need_fulfilled_with_shopping_list,
	rp.qu_id
FROM recipes r
JOIN recipes_pos rp
	ON r.id = rp.recipe_id
JOIN products p
	ON rp.product_id = p.id
LEFT JOIN (
	SELECT product_id, SUM(amount + amount_autoadded) AS amount
	FROM shopping_list
	GROUP BY product_id) sl
	ON rp.product_id = sl.product_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id
WHERE rp.not_check_stock_fulfillment = 1;
