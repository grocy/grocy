/*

Parent/child product relations are currently limited to 1 level.

This was enforced by the UI/frontend since ever, but on the backend/database only
since v3.0.0 (by the trigger enfore_product_nesting_level).

So via the API (or any third party app/tool which utilizes it), it was potentially possible
to create > 1 level nestings before v3.0.0.

The ALTER TABLE statement below does technically an update on all product rows (due to the column default value),
so > 1 level nestings would make this fail.

=> So clean up those unsupported nesting levels here.

*/

-- Clears parent_product_id for any > 1 level nesting (which is currently unsupported)
UPDATE products
SET parent_product_id = NULL
WHERE id IN (
	SELECT
		p_child.id
	FROM products p_parent
	JOIN products p_child
		ON p_parent.parent_product_id = p_child.id
	WHERE p_parent.parent_product_id IS NOT NULL
	)
	AND parent_product_id IS NOT NULL;

ALTER TABLE products
ADD default_print_stock_label INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET default_print_stock_label = 0;

ALTER TABLE products
ADD allow_label_per_unit INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET allow_label_per_unit = 0;
