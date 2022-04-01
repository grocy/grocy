ALTER TABLE products
ADD no_own_stock TINYINT NOT NULL DEFAULT 0 CHECK(no_own_stock IN (0, 1));

DROP VIEW uihelper_stock_current_overview;
CREATE VIEW uihelper_stock_current_overview
AS
SELECT
    p.id,
    sc.amount_opened AS amount_opened,
    p.tare_weight AS tare_weight,
    p.enable_tare_weight_handling AS enable_tare_weight_handling,
    sc.amount AS amount,
    sc.value as value,
    sc.product_id AS product_id,
    sc.best_before_date AS best_before_date,
    EXISTS(SELECT id FROM stock_missing_products WHERE id = sc.product_id) AS product_missing,
    (SELECT name FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name,
    (SELECT name_plural FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name_plural,
    p.name AS product_name,
    (SELECT name FROM product_groups WHERE product_groups.id = p.product_group_id) AS product_group_name,
    EXISTS(SELECT * FROM shopping_list WHERE shopping_list.product_id = sc.product_id) AS on_shopping_list,
    (SELECT name FROM quantity_units WHERE quantity_units.id = p.qu_id_purchase) AS qu_purchase_unit_name,
    (SELECT name_plural FROM quantity_units WHERE quantity_units.id = p.qu_id_purchase) AS qu_purchase_unit_name_plural,
    sc.is_aggregated_amount,
    sc.amount_opened_aggregated,
    sc.amount_aggregated,
	p.calories AS product_calories,
	sc.amount * p.calories AS calories,
	sc.amount_aggregated * p.calories AS calories_aggregated,
	p.quick_consume_amount,
	p.due_type,
	plp.purchased_date AS last_purchased,
	plp.price AS last_price,
	pap.price as average_price,
	p.min_stock_amount,
	pbcs.barcodes AS product_barcodes,
	p.description AS product_description,
	l.name AS product_default_location_name,
	p_parent.id AS parent_product_id,
	p_parent.name AS parent_product_name,
	p.picture_file_name AS product_picture_file_name,
	p.no_own_stock AS product_no_own_stock
FROM (
	SELECT *
	FROM stock_current
	WHERE best_before_date IS NOT NULL
	UNION
	SELECT m.id, 0, 0, 0, null, 0, 0, 0, p.due_type
	FROM stock_missing_products m
	JOIN products p
		ON m.id = p.id
	WHERE m.id NOT IN (SELECT product_id FROM stock_current)
    ) sc
LEFT JOIN products_last_purchased plp
	ON sc.product_id = plp.product_id
LEFT JOIN products_average_price pap
	ON sc.product_id = pap.product_id
LEFT JOIN products p
    ON sc.product_id = p.id
LEFT JOIN product_barcodes_comma_separated pbcs
	ON sc.product_id = pbcs.product_id
LEFT JOIN products p_parent
	ON p.parent_product_id = p_parent.id
LEFT JOIN locations l
	ON p.location_id = l.id
WHERE p.hide_on_stock_overview = 0;

CREATE TRIGGER prevent_adding_no_own_stock_products_to_stock AFTER INSERT ON stock
BEGIN
	SELECT CASE WHEN((
		SELECT 1
		FROM products p
		WHERE id = NEW.product_id
			AND no_own_stock = 1
	) NOTNULL) THEN RAISE(ABORT, "no_own_stock=1 products can't be added to stock") END;
END;
