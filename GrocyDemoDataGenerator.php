<?php

class GrocyDemoDataGenerator
{
	public static function PopulateDemoData(PDO $pdo)
	{
		$rowCount = Grocy::ExecuteDbQuery($pdo, 'SELECT COUNT(*) FROM migrations WHERE migration = -1')->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$sql = "
				UPDATE locations SET name = 'Vorratskammer', description = '' WHERE id = 1;
				INSERT INTO locations (name) VALUES ('Süßigkeitenschrank'); --2
				INSERT INTO locations (name) VALUES ('Konservenschrank'); --3
				INSERT INTO locations (name) VALUES ('Kühlschrank'); --4

				UPDATE quantity_units SET name = 'Stück' WHERE id = 1;
				INSERT INTO quantity_units (name) VALUES ('Packung'); --2
				INSERT INTO quantity_units (name) VALUES ('Glas'); --3
				INSERT INTO quantity_units (name) VALUES ('Dose'); --4
				INSERT INTO quantity_units (name) VALUES ('Becher'); --5
				INSERT INTO quantity_units (name) VALUES ('Bund'); --6

				DELETE FROM products WHERE id IN (1, 2);
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('Gummibärchen', 2, 2, 2, 1, 8); --3
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('Chips', 2, 2, 2, 1, 10); --4
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Eier', 1, 2, 1, 10); --5
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Nudeln', 1, 2, 2, 1); --6
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Essiggurken', 3, 3, 3, 1); --7
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Gulaschsuppe', 3, 4, 4, 1); --8
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Joghurt', 4, 5, 5, 1); --9
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Käse', 4, 2, 2, 1); --10
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Aufschnitt', 4, 2, 2, 1); --11
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Paprika', 4, 1, 1, 1); --12
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Gurke', 4, 1, 1, 1); --13
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Radieschen', 4, 6, 6, 1); --14
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Tomate', 4, 1, 1, 1); --15

				INSERT INTO migrations (migration) VALUES (-1);
			";

			Grocy::ExecuteDbStatement($pdo, $sql);

			GrocyLogicStock::AddProduct(3, 5, date('Y-m-d', strtotime('+180 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(4, 5, date('Y-m-d', strtotime('+180 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(5, 5, date('Y-m-d', strtotime('+20 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(6, 5, date('Y-m-d', strtotime('+600 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(7, 5, date('Y-m-d', strtotime('+800 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(8, 5, date('Y-m-d', strtotime('+900 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(9, 5, date('Y-m-d', strtotime('+14 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(10, 5, date('Y-m-d', strtotime('+21 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(11, 5, date('Y-m-d', strtotime('+10 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(12, 5, date('Y-m-d', strtotime('+2 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(13, 5, date('Y-m-d', strtotime('-2 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(14, 5, date('Y-m-d', strtotime('+2 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
			GrocyLogicStock::AddProduct(15, 5, date('Y-m-d', strtotime('-2 days')), GrocyLogicStock::TRANSACTION_TYPE_PURCHASE);
		}
	}
}
