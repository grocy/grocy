ALTER TABLE recipes_pos
ADD qu_id INTEGER;

UPDATE recipes_pos
SET qu_id = (SELECT qu_id_stock FROM products where id = product_id);

CREATE TRIGGER recipes_pos_qu_id_default AFTER INSERT ON recipes_pos
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products where id = product_id)
	WHERE qu_id IS NULL
		AND id = NEW.id;
END;

ALTER TABLE recipes_pos
ADD only_check_single_unit_in_stock TINYINT NOT NULL DEFAULT 0;

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
	ON rp.product_id = sc.product_id;
