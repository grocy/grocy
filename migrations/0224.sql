DROP VIEW products_last_price;

DROP VIEW stock_edited_entries;
CREATE VIEW stock_edited_entries
AS
/*
	Returns stock_id's which have been edited manually
*/
SELECT
	sl_add.stock_id,
	MAX(sl_edit.id) AS stock_log_id_of_newest_edited_entry
FROM stock_log sl_add
JOIN stock_log sl_edit
	ON sl_add.stock_id = sl_edit.stock_id
	AND sl_edit.transaction_type = 'stock-edit-new'
WHERE sl_add.transaction_type IN ('purchase', 'inventory-correction', 'self-production')
	AND sl_add.amount > 0
GROUP BY sl_add.stock_id;

DROP VIEW products_last_purchased;
CREATE VIEW products_last_purchased
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.amount,
	sl.best_before_date,
	sl.purchased_date,
	IFNULL(sl.price, 0) AS price,
	sl.location_id,
	sl.shopping_location_id
FROM stock_log sl
JOIN (
	/*
		This subquery gets the ID of the stock_log row (per product) which referes to the last purchase transaction,
		while taking undone and edited transactions into account
	*/
	SELECT
		sl1.product_id,
		MAX(sl1.id) stock_log_id_of_last_purchase
	FROM stock_log sl1
	JOIN (
		/*
			This subquery finds the last purchased date per product,
			there can be multiple purchase transactions per day, therefore a JOIN by purchased_date
			for the outer query on this and then take MAX id of stock_log (of that day)
		*/
		SELECT
			sl2.product_id,
			MAX(sl2.purchased_date) AS last_purchased_date
		FROM stock_log sl2
		WHERE sl2.undone = 0
			AND (
				(sl2.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl2.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
				OR (sl2.transaction_type = 'stock-edit-new' AND sl2.stock_id IN (SELECT stock_id FROM stock_edited_entries) AND sl2.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries))
			)
		GROUP BY sl2.product_id
	) x2
		ON sl1.product_id = x2.product_id
		AND sl1.purchased_date = x2.last_purchased_date
	WHERE sl1.undone = 0
		AND (
			(sl1.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl1.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
			OR (sl1.transaction_type = 'stock-edit-new' AND sl1.stock_id IN (SELECT stock_id FROM stock_edited_entries) AND sl1.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries))
		)
	GROUP BY sl1.product_id
) x
	ON sl.product_id = x.product_id
	AND sl.id = x.stock_log_id_of_last_purchase;

DROP VIEW uihelper_shopping_list;
CREATE VIEW uihelper_shopping_list
AS
SELECT
	sl.*,
	p.name AS product_name,
	plp.price AS last_price_unit,
	plp.price * sl.amount AS last_price_total,
	st.name AS default_shopping_location_name,
	qu.name AS qu_name,
	qu.name_plural AS qu_name_plural,
	pg.id AS product_group_id,
	pg.name AS product_group_name,
	pbcs.barcodes AS product_barcodes
FROM shopping_list sl
LEFT JOIN products p
	ON sl.product_id = p.id
LEFT JOIN products_last_purchased plp
	ON sl.product_id = plp.product_id
LEFT JOIN shopping_locations st
	ON p.shopping_location_id = st.id
LEFT JOIN quantity_units qu
	ON sl.qu_id = qu.id
LEFT JOIN product_groups pg
	ON p.product_group_id = pg.id
LEFT JOIN product_barcodes_comma_separated pbcs
	ON sl.product_id = pbcs.product_id;

DROP VIEW products_average_price;
CREATE VIEW products_average_price
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	s.product_id,
	SUM(s.amount * s.price) / SUM(s.amount) as price
FROM stock_log s
WHERE s.undone = 0
	AND (
		(s.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND s.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
		OR (s.transaction_type = 'stock-edit-new' AND s.stock_id IN (SELECT stock_id FROM stock_edited_entries) AND s.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries))
	)
	AND IFNULL(s.price, 0) > 0
	AND IFNULL(s.amount, 0) > 0
GROUP BY s.product_id;

DROP VIEW product_price_history;
CREATE VIEW products_price_history
AS
SELECT
	sl.product_id AS id, -- Dummy, LessQL needs an id column
	sl.product_id,
	sl.price,
	sl.amount,
	sl.purchased_date,
	sl.shopping_location_id
FROM stock_log sl
WHERE sl.undone = 0
	AND (
		(sl.transaction_type IN ('purchase', 'inventory-correction', 'self-production') AND sl.stock_id NOT IN (SELECT stock_id FROM stock_edited_entries))
		OR (sl.transaction_type = 'stock-edit-new' AND sl.stock_id IN (SELECT stock_id FROM stock_edited_entries) AND sl.id IN (SELECT stock_log_id_of_newest_edited_entry FROM stock_edited_entries))
	)
	AND IFNULL(sl.price, 0) > 0
	AND IFNULL(sl.amount, 0) > 0;
