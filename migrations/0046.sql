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
