<?php

class GrocyDemoDataGenerator
{
	public static function PopulateDemoData(PDO $pdo)
	{
		$pdo->exec(utf8_encode("
			UPDATE locations SET name = 'Vorratskammer', description = '' WHERE id = 1;
			INSERT INTO locations (name) VALUES ('Süßigkeitenschrank');
			INSERT INTO locations (name) VALUES ('Konvervenschrank');

			UPDATE quantity_units SET name = 'Stück' WHERE id = 1;
			INSERT INTO quantity_units (name) VALUES ('Packung');

			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Gummibärchen', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Chips', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Eier', 1, 2, 1, 10);

			INSERT INTO stock (product_id, amount, best_before_date, stock_id) VALUES (3, 5, date('now', '+180 day'), '".uniqid()."');
			INSERT INTO stock (product_id, amount, best_before_date, stock_id) VALUES (4, 5, date('now', '+180 day'), '".uniqid()."');
			INSERT INTO stock (product_id, amount, best_before_date, stock_id) VALUES (5, 5, date('now', '+25 day'), '".uniqid()."');
		"));
	}
}
