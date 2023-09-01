CREATE VIEW uihelper_product_details
AS
SELECT
	p.id,
	plp.purchased_date AS last_purchased_date,
	plp.price AS last_purchased_price,
	plp.shopping_location_id AS last_purchased_shopping_location_id,
	pap.price AS average_price,
	sl.average_shelf_life_days,
	pcp.price AS current_price,
	last_used.used_date AS last_used_date,
	next_due.best_before_date AS next_due_date,
	IFNULL((spoil_count.amount * 100.0) / consume_count.amount, 0) AS spoil_rate,
	CAST(IFNULL(quc_purchase2stock.factor, 1.0) AS REAL) AS qu_factor_purchase_to_stock,
	CAST(IFNULL(quc_price2stock.factor, 1.0) AS REAL) AS qu_factor_price_to_stock,
	CASE WHEN EXISTS(SELECT 1 FROM products px WHERE px.parent_product_id = p.id) THEN 1 ELSE 0 END AS has_childs
FROM products p
LEFT JOIN cache__products_last_purchased plp
	ON p.id = plp.product_id
LEFT JOIN cache__products_average_price pap
	ON p.id = pap.product_id
LEFT JOIN stock_average_product_shelf_life sl
	ON p.id = sl.id
LEFT JOIN products_current_price pcp
	ON p.id = pcp.product_id
LEFT JOIN cache__quantity_unit_conversions_resolved quc_purchase2stock
	ON p.id = quc_purchase2stock.product_id
	AND p.qu_id_purchase = quc_purchase2stock.from_qu_id
	AND p.qu_id_stock = quc_purchase2stock.to_qu_id
LEFT JOIN cache__quantity_unit_conversions_resolved quc_price2stock
	ON p.id = quc_price2stock.product_id
	AND p.qu_id_price = quc_price2stock.from_qu_id
	AND p.qu_id_stock = quc_price2stock.to_qu_id
LEFT JOIN (
	SELECT product_id, MAX(used_date) AS used_date
	FROM stock_log
	WHERE transaction_type = 'consume'
		AND undone = 0
	GROUP BY product_id
) last_used
	ON p.id = last_used.product_id
LEFT JOIN (
	SELECT product_id,MIN(best_before_date) AS best_before_date
	FROM stock
	GROUP BY product_id
) next_due
	ON p.id = next_due.product_id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM stock_log
	WHERE transaction_type = 'consume'
		AND undone = 0
	GROUP BY product_id
) consume_count
	ON p.id = consume_count.product_id
LEFT JOIN (
	SELECT product_id, SUM(amount) AS amount
	FROM stock_log
	WHERE transaction_type = 'consume'
		AND undone = 0
		AND spoiled = 1
	GROUP BY product_id
) spoil_count
	ON p.id = spoil_count.product_id;
