ALTER TABLE stock
ADD opened_date DATETIME;

ALTER TABLE stock_log
ADD opened_date DATETIME;

ALTER TABLE stock
ADD open TINYINT NOT NULL DEFAULT 0 CHECK(open IN (0, 1));

UPDATE stock
SET open = 0;

ALTER TABLE products
ADD default_best_before_days_after_open INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET default_best_before_days_after_open = 0;

DROP VIEW stock_current;
CREATE VIEW stock_current
AS
SELECT
	s.product_id,
	SUM(s.amount) AS amount,
	MIN(s.best_before_date) AS best_before_date,
	IFNULL((SELECT SUM(amount) FROM stock WHERE product_id = s.product_id AND open = 1), 0) AS amount_opened
FROM stock s
GROUP BY s.product_id

UNION

SELECT
	id,
	0,
	null,
	0
FROM stock_missing_products
WHERE is_partly_in_stock = 0;
