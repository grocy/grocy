CREATE TRIGGER enforce_min_stock_amount_for_cumulated_childs_INS AFTER INSERT ON products
BEGIN
	/*
		When a parent product has cumulate_min_stock_amount_of_sub_products enabled,
		the child should not have any min_stock_amount
	*/

	UPDATE products
	SET min_stock_amount = 0
	WHERE id IN (
			SELECT
				p_child.id
			FROM products p_parent
			JOIN products p_child
				ON p_child.parent_product_id = p_parent.id
			WHERE p_parent.id = NEW.id
				AND IFNULL(p_parent.cumulate_min_stock_amount_of_sub_products, 0) = 1
			)
		AND min_stock_amount > 0;
END;

CREATE TRIGGER enforce_min_stock_amount_for_cumulated_childs_UPD AFTER UPDATE ON products
BEGIN
	/*
		When a parent product has cumulate_min_stock_amount_of_sub_products enabled,
		the child should not have any min_stock_amount
	*/

	UPDATE products
	SET min_stock_amount = 0
	WHERE id IN (
			SELECT
				p_child.id
			FROM products p_parent
			JOIN products p_child
				ON p_child.parent_product_id = p_parent.id
			WHERE p_parent.id = NEW.id
				AND IFNULL(p_parent.cumulate_min_stock_amount_of_sub_products, 0) = 1
			)
		AND min_stock_amount > 0;
END;

UPDATE products
SET min_stock_amount = 0
WHERE id IN (
	SELECT
		p_child.id
	FROM products p_parent
	JOIN products p_child
		ON p_child.parent_product_id = p_parent.id
	WHERE IFNULL(p_parent.cumulate_min_stock_amount_of_sub_products, 0) = 1
	)
	AND min_stock_amount > 0;
