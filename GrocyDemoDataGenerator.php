<?php

class GrocyDemoDataGenerator
{
	public static function PopulateDemoData(PDO $pdo)
	{
		$sql = "
			UPDATE locations SET name = 'Vorratskammer', description = '' WHERE id = 1;
			INSERT INTO locations (name) VALUES ('Süßigkeitenschrank');
			INSERT INTO locations (name) VALUES ('Konvervenschrank');

			UPDATE quantity_units SET name = 'Stück' WHERE id = 1;
			INSERT INTO quantity_units (name) VALUES ('Packung');

			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Gummibärchen', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Chips', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Eier', 1, 2, 1, 10);
		";

		if ($pdo->exec(utf8_encode($sql)) === false)
		{
			throw new Exception($pdo->errorInfo());
		}

		GrocyLogicStock::AddProduct(3, 5, date('Y-m-d', strtotime('+180 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
		GrocyLogicStock::AddProduct(4, 5, date('Y-m-d', strtotime('+180 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
		GrocyLogicStock::AddProduct(5, 5, date('Y-m-d', strtotime('+25 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
	}
}
