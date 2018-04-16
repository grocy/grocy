<?php

namespace Grocy\Services;

use \Grocy\Services\LocalizationService;

class DemoDataGeneratorService extends BaseService
{
	public function PopulateDemoData()
	{
		$localizationService = new LocalizationService(CULTURE);

		$rowCount = $this->DatabaseService->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = -1')->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$sql = "
				INSERT INTO locations (name) VALUES ('{$localizationService->Localize('Pantry')}'); --2
				INSERT INTO locations (name) VALUES ('{$localizationService->Localize('Candy cupboard')}'); --3
				INSERT INTO locations (name) VALUES ('{$localizationService->Localize('Tinned food cupboard')}'); --4
				INSERT INTO locations (name) VALUES ('{$localizationService->Localize('Fridge')}'); --5

				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Piece')}'); --2
				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Pack')}'); --3
				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Glass')}'); --4
				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Tin')}'); --5
				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Can')}'); --6
				INSERT INTO quantity_units (name) VALUES ('{$localizationService->Localize('Bunch')}'); --7

				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('{$localizationService->Localize('Cookies')}', 3, 3, 3, 1, 8); --1
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('{$localizationService->Localize('Chocolate')}', 3, 3, 3, 1, 8); --2
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('{$localizationService->Localize('Gummy bears')}', 3, 3, 3, 1, 8); --3
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock, min_stock_amount) VALUES ('{$localizationService->Localize('Crisps')}', 3, 3, 3, 1, 10); --4
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Eggs')}', 5, 3, 2, 10); --5
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Noodles')}', 3, 3, 3, 1); --6
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Pickles')}', 4,4, 4, 1); --7
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Gulash soup')}', 4, 5, 5, 1); --8
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Yogurt')}', 5, 6, 6, 1); --9
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Cheese')}', 5, 3, 3, 1); --10
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Cold cuts')}', 5, 3, 3, 1); --11
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Paprika')}', 5, 2, 2, 1); --12
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Cucumber')}', 5, 2, 2, 1); --13
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Radish')}', 5, 7, 7, 1); --14
				INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('{$localizationService->Localize('Tomato')}', 5, 2, 2, 1); --15

				INSERT INTO habits (name, period_type, period_days) VALUES ('{$localizationService->Localize('Changed towels in the bathroom')}', 'manually', 5); --1
				INSERT INTO habits (name, period_type, period_days) VALUES ('{$localizationService->Localize('Cleaned the kitchen floor')}', 'dynamic-regular', 7); --2

				INSERT INTO batteries (name, description, used_in) VALUES ('{$localizationService->Localize('Battery')}1', '{$localizationService->Localize('Warranty ends')} 2023', '{$localizationService->Localize('TV remote control')}'); --1
				INSERT INTO batteries (name, description, used_in) VALUES ('{$localizationService->Localize('Battery')}2', '{$localizationService->Localize('Warranty ends')} 2022', '{$localizationService->Localize('Alarm clock')}'); --2
				INSERT INTO batteries (name, description, used_in, charge_interval_days) VALUES ('{$localizationService->Localize('Battery')}3', '{$localizationService->Localize('Warranty ends')} 2022', '{$localizationService->Localize('Heat remote control')}', 60); --3

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
		unlink(__DIR__ . '/../data/grocy.db');
		$this->PopulateDemoData();
	}
}
