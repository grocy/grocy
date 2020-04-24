ALTER TABLE stock_log
ADD qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1.0;

ALTER TABLE stock
ADD qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1.0;

UPDATE stock 
	SET qu_factor_purchase_to_stock = (select qu_factor_purchase_to_stock from products where product_id = id);

UPDATE stock_log 
	SET qu_factor_purchase_to_stock = (select qu_factor_purchase_to_stock from products where product_id = id);

CREATE TABLE product_barcodes (
id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
product_id INT NOT NULL,
barcode TEXT NOT NULL,
qu_factor_purchase_to_stock REAL NOT NULL DEFAULT 1,
shopping_location_id INTEGER
);
