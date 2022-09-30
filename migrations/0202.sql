DROP TRIGGER recipes_pos_qu_id_default;
CREATE TRIGGER recipes_pos_qu_id_default AFTER INSERT ON recipes_pos
BEGIN
	UPDATE recipes_pos
	SET qu_id = (SELECT qu_id_stock FROM products where id = product_id)
	WHERE id = NEW.id
		AND IFNULL(qu_id, '') = '';

	SELECT CASE WHEN((
		SELECT 1
		FROM recipes_pos rp
		JOIN quantity_unit_conversions_resolved qucr
			ON qucr.product_id = rp.product_id
			AND qucr.to_qu_id = rp.qu_id
		WHERE rp.id = NEW.id

		UNION

		-- only_check_single_unit_in_stock = 1 ingredients can have any QU
		SELECT 1
		FROM recipes_pos rp
		WHERE rp.id = NEW.id
			AND IFNULL(rp.only_check_single_unit_in_stock, 0) = 1
	) ISNULL) THEN RAISE(ABORT, "Provided qu_id doesn't have a related conversion for that product") END;
END;
