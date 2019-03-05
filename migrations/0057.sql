ALTER TABLE products
ADD enable_tare_weight_handling TINYINT NOT NULL DEFAULT 0;

ALTER TABLE products
ADD tare_weight REAL NOT NULL DEFAULT 0;
