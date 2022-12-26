ALTER TABLE products
ADD qu_id_consume INTEGER;

UPDATE products
SET qu_id_consume = qu_id_stock;

CREATE TRIGGER default_qu_id_consume AFTER INSERT ON products
BEGIN
	UPDATE products
	SET qu_id_consume = qu_id_stock
	WHERE id = NEW.id
		AND IFNULL(qu_id_consume, 0) = 0;
END;

DROP TRIGGER default_qu_conversion;
CREATE TRIGGER default_qu_conversions AFTER INSERT ON products
BEGIN
	/*
		Automatically create a default (product specific 1 to 1) QU conversion when a product
		with qu_stock != qu_purchase was created and when no default QU conversion applies
	*/
	INSERT INTO quantity_unit_conversions
		(from_qu_id, to_qu_id, factor, product_id)
	SELECT p.qu_id_purchase, p.qu_id_stock, 1, p.id
	FROM products p
	WHERE p.id = NEW.id
		AND p.qu_id_stock != qu_id_purchase
		AND NOT EXISTS(SELECT 1 FROM quantity_unit_conversions_resolved WHERE product_id = p.id AND from_qu_id = p.qu_id_stock AND to_qu_id = p.qu_id_purchase);

	/*
		Automatically create a default (product specific 1 to 1) QU conversion when a product
		with qu_stock != qu_consume was created and when no default QU conversion applies
	*/
	INSERT INTO quantity_unit_conversions
		(from_qu_id, to_qu_id, factor, product_id)
	SELECT p.qu_id_consume, p.qu_id_stock, 1, p.id
	FROM products p
	WHERE p.id = NEW.id
		AND p.qu_id_stock != qu_id_consume
		AND NOT EXISTS(SELECT 1 FROM quantity_unit_conversions_resolved WHERE product_id = p.id AND from_qu_id = p.qu_id_stock AND to_qu_id = p.qu_id_consume);
END;

DROP VIEW products_view;
CREATE VIEW products_view
AS
SELECT
	p.*,
	CASE WHEN (SELECT 1 FROM products WHERE parent_product_id = p.id) NOTNULL THEN 1 ELSE 0 END AS has_sub_products,
	IFNULL(quc_purchase.factor, 1.0) AS qu_factor_purchase_to_stock,
	IFNULL(quc_consume.factor, 1.0) AS qu_factor_consume_to_stock
FROM products p
LEFT JOIN quantity_unit_conversions_resolved quc_purchase
	ON p.id = quc_purchase.product_id
	AND p.qu_id_purchase = quc_purchase.from_qu_id
	AND p.qu_id_stock = quc_purchase.to_qu_id
LEFT JOIN quantity_unit_conversions_resolved quc_consume
	ON p.id = quc_consume.product_id
	AND p.qu_id_consume = quc_consume.from_qu_id
	AND p.qu_id_stock = quc_consume.to_qu_id;

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
	p.name AS product_name,
	pg.name AS product_group_name,
	EXISTS(SELECT * FROM shopping_list WHERE shopping_list.product_id = sc.product_id) AS on_shopping_list,
	qu_stock.name AS qu_stock_name,
	qu_stock.name_plural AS qu_stock_name_plural,
	qu_purchase.name AS qu_purchase_name,
	qu_purchase.name_plural AS qu_purchase_name_plural,
	qu_consume.name AS qu_consume_name,
	qu_consume.name_plural AS qu_consume_name_plural,
	sc.is_aggregated_amount,
	sc.amount_opened_aggregated,
	sc.amount_aggregated,
	p.calories AS product_calories,
	sc.amount * p.calories AS calories,
	sc.amount_aggregated * p.calories AS calories_aggregated,
	p.quick_consume_amount,
	p.quick_consume_amount / p.qu_factor_consume_to_stock AS quick_consume_amount_qu_consume,
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
	p.no_own_stock AS product_no_own_stock,
	p.qu_factor_purchase_to_stock AS product_qu_factor_purchase_to_stock
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
JOIN products_view p
    ON sc.product_id = p.id
JOIN locations l
	ON p.location_id = l.id
JOIN quantity_units qu_stock
	ON p.qu_id_stock = qu_stock.id
JOIN quantity_units qu_purchase
	ON p.qu_id_purchase = qu_purchase.id
JOIN quantity_units qu_consume
	ON p.qu_id_consume = qu_consume.id
LEFT JOIN product_groups pg
	ON p.product_group_id = pg.id
LEFT JOIN products_last_purchased plp
	ON sc.product_id = plp.product_id
LEFT JOIN products_average_price pap
	ON sc.product_id = pap.product_id
LEFT JOIN product_barcodes_comma_separated pbcs
	ON sc.product_id = pbcs.product_id
LEFT JOIN products p_parent
	ON p.parent_product_id = p_parent.id
WHERE p.hide_on_stock_overview = 0;
