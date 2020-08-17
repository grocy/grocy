ALTER TABLE stock_log
ADD qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1.0;

ALTER TABLE stock
ADD qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1.0;

UPDATE stock 
SET qu_factor_purchase_to_stock = (SELECT qu_factor_purchase_to_stock FROM products WHERE product_id = id);

UPDATE stock_log 
SET qu_factor_purchase_to_stock = (SELECT qu_factor_purchase_to_stock FROM products WHERE product_id = id);

--Price is now going forward to be saved as 1 QU Stock
UPDATE stock
SET price = ROUND(price / qu_factor_purchase_to_stock, 2);

UPDATE stock_log
SET price = ROUND(price / qu_factor_purchase_to_stock, 2);

CREATE TABLE product_barcodes (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	product_id INT NOT NULL,
	barcode TEXT NOT NULL UNIQUE,
	qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1,
	shopping_location_id INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

-- Convert product table to new product_barcodes table
INSERT INTO product_barcodes
	(product_id, barcode, qu_factor_purchase_to_stock, shopping_location_id)
WITH barcodes_splitted(id, barcode, str, qu_factor_purchase_to_stock, shopping_location_id) AS (
	SELECT id as product_id, '', barcode || ',', qu_factor_purchase_to_stock, shopping_location_id
	FROM products

    UNION ALL
	SELECT
		id as product_id,
		SUBSTR(str, 0, instr(str, ',')),
		SUBSTR(str, instr(str, ',') + 1),
		qu_factor_purchase_to_stock,
		shopping_location_id
    FROM barcodes_splitted
	WHERE str != ''
)
SELECT id as product_id, barcode, qu_factor_purchase_to_stock, shopping_location_id
FROM barcodes_splitted
WHERE barcode != '';

PRAGMA legacy_alter_table = ON;
ALTER TABLE products RENAME TO products_old;

-- Remove barcode column
-- Reorder columns
CREATE TABLE products (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	description TEXT,
	product_group_id INTEGER,
	active TINYINT NOT NULL DEFAULT 1,
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
	allow_partial_units_in_stock TINYINT NOT NULL DEFAULT 0,
	enable_tare_weight_handling TINYINT NOT NULL DEFAULT 0,
	tare_weight REAL NOT NULL DEFAULT 0,
	not_check_stock_fulfillment_for_recipes TINYINT DEFAULT 0,
	parent_product_id INT,
	calories INTEGER,
	cumulate_min_stock_amount_of_sub_products TINYINT DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO products
	(id, name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount, default_best_before_days, row_created_timestamp, product_group_id, picture_file_name, default_best_before_days_after_open, allow_partial_units_in_stock, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, default_best_before_days_after_freezing, default_best_before_days_after_thawing, shopping_location_id)
SELECT id, name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount,default_best_before_days, row_created_timestamp, product_group_id, picture_file_name, default_best_before_days_after_open, allow_partial_units_in_stock, enable_tare_weight_handling, tare_weight, not_check_stock_fulfillment_for_recipes, parent_product_id, calories, cumulate_min_stock_amount_of_sub_products, default_best_before_days_after_freezing, default_best_before_days_after_thawing, shopping_location_id
FROM products_old;

DROP TABLE products_old;

DROP VIEW stock_current_location_content;
CREATE VIEW stock_current_location_content
AS
SELECT
	IFNULL(s.location_id, p.location_id) AS location_id,
	s.product_id,
	SUM(s.amount) AS amount,
	ROUND(SUM(s.amount / s.qu_factor_purchase_to_stock),2) as factor_purchase_amount,
	ROUND(SUM(IFNULL(s.price, 0) * s.amount), 2) AS value,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND location_id = s.location_id AND open = 1), 0) AS amount_opened
FROM stock s
JOIN products p
	ON s.product_id = p.id
	AND p.active = 1
GROUP BY IFNULL(s.location_id, p.location_id), s.product_id;

DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT
	pr.parent_product_id AS product_id,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id), 0) AS amount,
	IFNULL(ROUND((SELECT SUM(amount / qu_factor_purchase_to_stock) FROM stock WHERE product_id = pr.parent_product_id), 2), 0)  as factor_purchase_amount,
	SUM(s.amount) * IFNULL(qucr.factor, 1) AS amount_aggregated,
	IFNULL(ROUND((SELECT SUM(IFNULL(price,0) * amount) FROM stock WHERE product_id = pr.parent_product_id), 2), 0)  AS value,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id AND open = 1), 0) AS amount_opened,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = pr.parent_product_id) AND open = 1), 0) * IFNULL(qucr.factor, 1) AS amount_opened_aggregated,
	CASE WHEN p_sub.parent_product_id IS NOT NULL THEN 1 ELSE 0 END AS is_aggregated_amount
FROM products_resolved pr
JOIN stock s
	ON pr.sub_product_id = s.product_id
JOIN products p_parent
	ON pr.parent_product_id = p_parent.id
	AND p_parent.active = 1
JOIN products p_sub
	ON pr.sub_product_id = p_sub.id
	AND p_sub.active = 1
LEFT JOIN quantity_unit_conversions_resolved qucr
	ON pr.sub_product_id = qucr.product_id
	AND p_sub.qu_id_stock = qucr.from_qu_id
	AND p_parent.qu_id_stock = qucr.to_qu_id
GROUP BY pr.parent_product_id
HAVING SUM(s.amount) > 0

UNION

-- This is the same as above but sub products not rolled up (no QU conversion and column is_aggregated_amount = 0 here)
SELECT
	pr.sub_product_id AS product_id,
	SUM(s.amount) AS amount,
	ROUND(SUM(s.amount / s.qu_factor_purchase_to_stock), 2) as factor_purchase_amount,
	SUM(s.amount) AS amount_aggregated,
	ROUND(SUM(IFNULL(s.price, 0) * s.amount), 2) AS value,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened_aggregated,
	0 AS is_aggregated_amount
FROM products_resolved pr
JOIN stock s
	ON pr.sub_product_id = s.product_id
WHERE pr.parent_product_id != pr.sub_product_id
GROUP BY pr.sub_product_id
HAVING SUM(s.amount) > 0;

DROP VIEW products_resolved;
CREATE VIEW products_resolved AS
SELECT
	p.parent_product_id parent_product_id,
	p.id as sub_product_id
FROM products p
	WHERE p.parent_product_id IS NOT NULL
	AND p.active = 1

UNION

SELECT
	p.id parent_product_id,
	p.id as sub_product_id
FROM products p
	WHERE p.parent_product_id IS NULL
	AND p.active = 1;
