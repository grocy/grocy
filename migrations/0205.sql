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
