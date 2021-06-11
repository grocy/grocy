ALTER TABLE products
ADD default_print_stock_label INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET default_print_stock_label = 0;

ALTER TABLE products
ADD allow_label_per_unit INTEGER NOT NULL DEFAULT 0;

UPDATE products
SET allow_label_per_unit = 0;