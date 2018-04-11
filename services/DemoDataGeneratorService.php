<?php

namespace Grocy\Services;

class DemoDataGeneratorService extends BaseService
{
	public function PopulateDemoData()
	{
		$rowCount = $this->DatabaseService->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = -1')->fetchColumn();
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

				INSERT INTO habits (name, period_type, period_days) VALUES ('Changed towels in the bathroom', 'manually', 5); --1
				INSERT INTO habits (name, period_type, period_days) VALUES ('Cleaned the kitchen floor', 'dynamic-regular', 7); --2

				INSERT INTO batteries (name, description, used_in) VALUES ('Battery1', 'Warranty ends 2022', 'TV remote control'); --1
				INSERT INTO batteries (name, description, used_in) VALUES ('Battery2', 'Warranty ends 2022', 'Alarm clock'); --2
				INSERT INTO batteries (name, description, used_in, charge_interval_days) VALUES ('Battery3', 'Warranty ends 2022', 'Heat remote control', 60); --3

				INSERT INTO migrations (migration) VALUES (-1);
			";

			$this->DatabaseService->ExecuteDbStatement($sql);

			$stockService = new StockService();
			$stockService->AddProduct(3, 5, date('Y-m-d', strtotime('+180 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(4, 5, date('Y-m-d', strtotime('+180 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(5, 5, date('Y-m-d', strtotime('+20 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(6, 5, date('Y-m-d', strtotime('+600 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(7, 5, date('Y-m-d', strtotime('+800 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(8, 5, date('Y-m-d', strtotime('+900 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(9, 5, date('Y-m-d', strtotime('+14 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(10, 5, date('Y-m-d', strtotime('+21 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(11, 5, date('Y-m-d', strtotime('+10 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(12, 5, date('Y-m-d', strtotime('+2 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(13, 5, date('Y-m-d', strtotime('-2 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(14, 5, date('Y-m-d', strtotime('+2 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddProduct(15, 5, date('Y-m-d', strtotime('-2 days')), StockService::TRANSACTION_TYPE_PURCHASE);
			$stockService->AddMissingProductsToShoppingList();

			$habitsService = new HabitsService();
			$habitsService->TrackHabit(1, date('Y-m-d H:i:s', strtotime('-5 days')));
			$habitsService->TrackHabit(1, date('Y-m-d H:i:s', strtotime('-10 days')));
			$habitsService->TrackHabit(1, date('Y-m-d H:i:s', strtotime('-15 days')));
			$habitsService->TrackHabit(2, date('Y-m-d H:i:s', strtotime('-10 days')));
			$habitsService->TrackHabit(2, date('Y-m-d H:i:s', strtotime('-20 days')));

			$batteriesService = new BatteriesService();
			$batteriesService->TrackChargeCycle(1, date('Y-m-d H:i:s', strtotime('-200 days')));
			$batteriesService->TrackChargeCycle(1, date('Y-m-d H:i:s', strtotime('-150 days')));
			$batteriesService->TrackChargeCycle(1, date('Y-m-d H:i:s', strtotime('-100 days')));
			$batteriesService->TrackChargeCycle(1, date('Y-m-d H:i:s', strtotime('-50 days')));
			$batteriesService->TrackChargeCycle(2, date('Y-m-d H:i:s', strtotime('-200 days')));
			$batteriesService->TrackChargeCycle(2, date('Y-m-d H:i:s', strtotime('-150 days')));
			$batteriesService->TrackChargeCycle(2, date('Y-m-d H:i:s', strtotime('-100 days')));
			$batteriesService->TrackChargeCycle(2, date('Y-m-d H:i:s', strtotime('-50 days')));
			$batteriesService->TrackChargeCycle(3, date('Y-m-d H:i:s', strtotime('-65 days')));
		}
	}

	public function RecreateDemo()
	{
		unlink(__DIR__ . '/data/grocy.db');
		$this->PopulateDemoData($this->DatabaseService->GetDbConnectionRaw(true));
	}
}
