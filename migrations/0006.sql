INSERT INTO locations
	(name, description)
VALUES
	('DefaultLocation', 'This is the first default location, edit or delete it');

INSERT INTO quantity_units
	(name, description)
VALUES
	('DefaultQuantityUnit', 'This is the first default quantity unit, edit or delete it');

INSERT INTO products
	(name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock)
VALUES
	('DefaultProduct1', 'This is the first default product, edit or delete it', 1, 1, 1, 1);

INSERT INTO products
	(name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock)
VALUES
	('DefaultProduct2', 'This is the second default product, edit or delete it', 1, 1, 1, 1);
