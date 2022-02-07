ALTER TABLE products
ADD treat_opened_as_out_of_stock TINYINT NOT NULL DEFAULT 1 CHECK(treat_opened_as_out_of_stock IN (0, 1));

DROP VIEW stock_missing_products_including_opened;

DROP VIEW stock_missing_products;
CREATE VIEW stock_missing_products
AS

-- Products WITHOUT sub products where the amount of the sub products SHOULD NOT be cumulated
SELECT
	p.id,
	MAX(p.name) AS name,
	p.min_stock_amount - IFNULL(SUM(s.amount), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products_view p
LEFT JOIN stock_current s
	ON p.id = s.product_id
WHERE p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 0
	AND p.has_sub_products = 0
	AND p.parent_product_id IS NULL
	AND IFNULL(p.active, 0) = 1
GROUP BY p.id
HAVING IFNULL(SUM(s.amount), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END < p.min_stock_amount

UNION

-- Parent products WITH sub products where the amount of the sub products SHOULD be cumulated
SELECT
	p.id,
	MAX(p.name) AS name,
	SUM(sub_p.min_stock_amount) - IFNULL(SUM(s.amount_aggregated), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products_view p
JOIN products_resolved pr
	ON p.id = pr.parent_product_id
JOIN products sub_p
	ON pr.sub_product_id = sub_p.id
LEFT JOIN stock_current s
	ON pr.sub_product_id = s.product_id
WHERE sub_p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 1
	AND IFNULL(p.active, 0) = 1
GROUP BY p.id
HAVING IFNULL(SUM(s.amount_aggregated), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END < SUM(sub_p.min_stock_amount)

UNION

-- Sub products where the amount SHOULD NOT be cumulated into the parent product
SELECT
	sub_p.id,
	MAX(sub_p.name) AS name,
	SUM(sub_p.min_stock_amount) - IFNULL(SUM(s.amount_aggregated), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened_aggregated), 0) ELSE 0 END AS amount_missing,
	CASE WHEN IFNULL(SUM(s.amount), 0) > 0 THEN 1 ELSE 0 END AS is_partly_in_stock
FROM products p
JOIN products_resolved pr
	ON p.id = pr.parent_product_id
JOIN products sub_p
	ON pr.sub_product_id = sub_p.id
LEFT JOIN stock_current s
	ON pr.sub_product_id = s.product_id
WHERE sub_p.min_stock_amount != 0
	AND p.cumulate_min_stock_amount_of_sub_products = 0
	AND IFNULL(p.active, 0) = 1
GROUP BY sub_p.id
HAVING IFNULL(SUM(s.amount), 0) - CASE WHEN p.treat_opened_as_out_of_stock = 1 THEN IFNULL(SUM(s.amount_opened), 0) ELSE 0 END < sub_p.min_stock_amount;

DROP VIEW uihelper_stock_current_overview_including_opened;

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
	p.picture_file_name AS product_picture_file_name
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
