PRAGMA legacy_alter_table = ON;
ALTER TABLE products RENAME TO products_old;

-- Remove allow_label_per_unit column
-- Rename default_print_stock_label column to default_stock_label_type
CREATE TABLE products (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	product_group_id INTEGER,
	active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1)),
	location_id INTEGER NOT NULL,
	shopping_location_id INTEGER,
	qu_id_purchase INTEGER NOT NULL,
	qu_id_stock INTEGER NOT NULL,
	qu_factor_purchase_to_stock REAL NOT NULL,
	min_stock_amount INTEGER NOT NULL DEFAULT 0,
	default_best_before_days INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_open INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_freezing INTEGER NOT NULL DEFAULT 0,
	default_best_before_days_after_thawing INTEGER NOT NULL DEFAULT 0,
	picture_file_name TEXT,
	enable_tare_weight_handling TINYINT NOT NULL DEFAULT 0,
	tare_weight REAL NOT NULL DEFAULT 0,
	not_check_stock_fulfillment_for_recipes TINYINT DEFAULT 0,
	parent_product_id INT,
	calories INTEGER,
	cumulate_min_stock_amount_of_sub_products TINYINT DEFAULT 0,
	due_type TINYINT NOT NULL DEFAULT 1 CHECK(due_type IN (1, 2)),
	quick_consume_amount REAL NOT NULL DEFAULT 1,
	hide_on_stock_overview TINYINT NOT NULL DEFAULT 0 CHECK(hide_on_stock_overview IN (0, 1)),
	default_stock_label_type INTEGER NOT NULL DEFAULT 0,
	should_not_be_frozen TINYINT NOT NULL DEFAULT 0 CHECK(should_not_be_frozen IN (0, 1)),
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO products
	(id, name, description, product_group_id, active, location_id, shopping_location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount, default_best_before_days, default_best_before_days_after_open, default_best_before_days_after_freezing, default_best_before_days_after_thawing, picture_file_name, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, due_type, quick_consume_amount, hide_on_stock_overview, default_stock_label_type, should_not_be_frozen, row_created_timestamp)
SELECT id, name, description, product_group_id, active, location_id, shopping_location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount, default_best_before_days, default_best_before_days_after_open, default_best_before_days_after_freezing, default_best_before_days_after_thawing, picture_file_name, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, due_type, quick_consume_amount, hide_on_stock_overview, default_print_stock_label, should_not_be_frozen, row_created_timestamp
FROM products_old;

DROP TABLE products_old;

CREATE TRIGGER prevent_qu_stock_change_after_first_purchase AFTER UPDATE ON products
BEGIN
	SELECT CASE WHEN((
        SELECT 1
        FROM stock_log
		WHERE product_id = NEW.id
			AND NEW.qu_id_stock != OLD.qu_id_stock
    ) NOTNULL) THEN RAISE(ABORT, "qu_id_stock cannot be changed when the product was once added to stock") END;
END;

CREATE TRIGGER enforce_parent_product_id_null_when_empty_INS AFTER INSERT ON products
BEGIN
	UPDATE products
	SET parent_product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_product_id, '') = '';
END;

CREATE TRIGGER enforce_parent_product_id_null_when_empty_UPD AFTER UPDATE ON products
BEGIN
	UPDATE products
	SET parent_product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(parent_product_id, '') = '';
END;

CREATE TRIGGER cascade_product_removal AFTER DELETE ON products
BEGIN
	DELETE FROM stock
	WHERE product_id = OLD.id;

	DELETE FROM stock_log
	WHERE product_id = OLD.id;

	DELETE FROM product_barcodes
	WHERE product_id = OLD.id;

	DELETE FROM quantity_unit_conversions
	WHERE product_id = OLD.id;

	DELETE FROM recipes_pos
	WHERE product_id = OLD.id;

	UPDATE recipes
	SET product_id = NULL
	WHERE product_id = OLD.id;

	DELETE FROM meal_plan
	WHERE product_id = OLD.id
		AND type = 'product';

	DELETE FROM shopping_list
	WHERE product_id = OLD.id;
END;

CREATE TRIGGER enfore_product_nesting_level BEFORE UPDATE ON products
BEGIN
	-- Currently only 1 level is supported
    SELECT CASE WHEN((
        SELECT 1
        FROM products p
        WHERE IFNULL(NEW.parent_product_id, '') != ''
            AND IFNULL(parent_product_id, '') = NEW.id
    ) NOTNULL) THEN RAISE(ABORT, "Unsupported product nesting level detected (currently only 1 level is supported)") END;
END;

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

CREATE INDEX ix_products_performance1 ON products (
    parent_product_id
);

CREATE INDEX ix_products_performance2 ON products (
    CASE WHEN parent_product_id IS NULL THEN id ELSE parent_product_id END,
    active
);
