ALTER TABLE product_barcodes
ADD note TEXT;

CREATE VIEW product_barcodes_comma_separated
AS
SELECT
	pb.id, -- Dummy, LessQL needs an id column
	pb.product_id,
	GROUP_CONCAT(pb.barcode) AS barcodes
FROM product_barcodes pb
JOIN products p
	ON pb.product_id = p.id
WHERE p.active = 1
GROUP BY pb.product_id;

CREATE VIEW uihelper_shopping_list
AS
SELECT
	sl.*,
	p.name AS product_name,
	plp.price * IFNULL(qucr.factor, 1.0) AS last_price_unit,
	plp.price * IFNULL(qucr.factor, 1.0) * sl.amount AS last_price_total,
	st.name AS default_shopping_location_name,
	qu.name AS qu_name,
	qu.name_plural AS qu_name_plural,
	pg.name AS product_group_name,
	pbcs.barcodes AS product_barcodes
FROM shopping_list sl
LEFT JOIN products p
	ON sl.product_id = p.id
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON sl.product_id = qucr.product_id
	AND p.qu_id_stock = qucr.from_qu_id
	AND sl.qu_id = qucr.to_qu_id
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
