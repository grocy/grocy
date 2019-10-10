DROP VIEW stock_current_location_content;
CREATE VIEW stock_current_location_content
AS
SELECT
        IFNULL(s.location_id, p.location_id) AS location_id,
        s.product_id,
        SUM(s.amount) AS amount,
	SUM(s.amount / p.qu_factor_purchase_to_stock) as factor_purchase_amount,
        SUM(IFNULL(s.price, 0) * s.amount) AS value,
	MIN(s.best_before_date) AS best_before_date,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND location_id = s.location_id AND open = 1), 0) AS amount_opened
FROM stock s
JOIN products p
        ON s.product_id = p.id
GROUP BY IFNULL(s.location_id, p.location_id), s.product_id;

DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT
        pr.parent_product_id AS product_id,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id), 0) AS amount,
        IFNULL((SELECT SUM(amount / p.qu_factor_purchase_to_stock) FROM stock s join products p on s.product_id = p.id WHERE s.product_id = pr.parent_product_id), 0) AS factor_purchase_amount,
        SUM(s.amount) AS amount_aggregated,
        IFNULL((SELECT SUM(IFNULL(s.price,0) * IFNULL(amount,0)) FROM stock WHERE product_id = pr.parent_product_id),0) AS value,
        MIN(s.best_before_date) AS best_before_date,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = pr.parent_product_id AND open = 1), 0) AS amount_opened,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = pr.parent_product_id) AND open = 1), 0) AS amount_opened_aggregated,
        CASE WHEN p.parent_product_id IS NOT NULL THEN 1 ELSE 0 END AS is_aggregated_amount
FROM products_resolved pr
JOIN stock s
        ON pr.sub_product_id = s.product_id
JOIN products p
        ON pr.sub_product_id = p.id
GROUP BY pr.parent_product_id
HAVING SUM(s.amount) > 0

UNION

-- This is the same as above but sub products not rolled up (column is_aggregated_amount = 0 here)
SELECT
        pr.sub_product_id AS product_id,
        SUM(s.amount) AS amount,
	SUM(s.amount / p.qu_factor_purchase_to_stock) as factor_purchase_amount,
        SUM(s.amount) AS amount_aggregated,
        SUM(IFNULL(s.price, 0) * s.amount) AS value,
        MIN(s.best_before_date) AS best_before_date,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened,
        IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened_aggregated,
        0 AS is_aggregated_amount
FROM products_resolved pr
JOIN stock s
        ON pr.sub_product_id = s.product_id
JOIN products p
	ON p.id = s.product_id
WHERE pr.parent_product_id != pr.sub_product_id
GROUP BY pr.sub_product_id
HAVING SUM(s.amount) > 0;
