DROP VIEW uihelper_stock_current_overview_including_opened;
CREATE VIEW uihelper_stock_current_overview_including_opened
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
    EXISTS(SELECT id FROM stock_missing_products_including_opened WHERE id = sc.product_id) AS product_missing,
    (SELECT name FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name,
    (SELECT name_plural FROM quantity_units WHERE quantity_units.id = p.qu_id_stock) AS qu_unit_name_plural,
    p.name AS product_name,
    (SELECT name FROM product_groups WHERE product_groups.id = product_group_id) AS product_group_name,
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
	p.min_stock_amount
FROM (
        SELECT *
        FROM stock_current
        WHERE best_before_date IS NOT NULL
        UNION
        SELECT m.id, 0, 0, 0, null, 0, 0, 0, p.due_type
        FROM stock_missing_products_including_opened m
		JOIN products p
			ON m.id = p.id
        WHERE m.id NOT IN (SELECT product_id FROM stock_current)
    ) sc
LEFT JOIN products_last_purchased plp
	ON sc.product_id = plp.product_id
LEFT JOIN products p
    ON sc.product_id = p.id
WHERE p.hide_on_stock_overview = 0;

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
    (SELECT name FROM product_groups WHERE product_groups.id = product_group_id) AS product_group_name,
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
	p.min_stock_amount
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
LEFT JOIN products p
    ON sc.product_id = p.id
WHERE p.hide_on_stock_overview = 0;
