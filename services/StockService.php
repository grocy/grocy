<?php

namespace Grocy\Services;

class StockService extends BaseService
{
	const TRANSACTION_TYPE_PURCHASE = 'purchase';
	const TRANSACTION_TYPE_CONSUME = 'consume';
	const TRANSACTION_TYPE_INVENTORY_CORRECTION = 'inventory-correction';

	public function GetCurrentStock()
	{
		$sql = 'SELECT * from stock_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetMissingProducts()
	{
		$sql = 'SELECT * from stock_missing_products';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetExpiringProducts(int $days = 5)
	{
		$currentStock = $this->GetCurrentStock();
		return FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime("+$days days")), '<');
	}

	public function GetProductDetails(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$product = $this->Database->products($productId);
		$productStockAmount = $this->Database->stock()->where('product_id', $productId)->sum('amount');
		$productLastPurchased = $this->Database->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_PURCHASE)->max('purchased_date');
		$productLastUsed = $this->Database->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->max('used_date');
		$nextBestBeforeDate = $this->Database->stock()->where('product_id', $productId)->min('best_before_date');
		$quPurchase = $this->Database->quantity_units($product->qu_id_purchase);
		$quStock = $this->Database->quantity_units($product->qu_id_stock);
		
		$lastPrice = null;
		$lastLogRow = $this->Database->stock_log()->where('product_id = :1 AND transaction_type = :2', $productId, self::TRANSACTION_TYPE_PURCHASE)->orderBy('row_created_timestamp', 'DESC')->limit(1)->fetch();
		if ($lastLogRow !== null && !empty($lastLogRow))
		{
			$lastPrice = $lastLogRow->price;
		}

		return array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $productStockAmount,
			'quantity_unit_purchase' => $quPurchase,
			'quantity_unit_stock' => $quStock,
			'last_price' => $lastPrice,
			'next_best_before_date' => $nextBestBeforeDate
		);
	}

	public function GetProductPriceHistory(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$returnData = array();
		$rows = $this->Database->stock_log()->where('product_id = :1 AND transaction_type = :2', $productId, self::TRANSACTION_TYPE_PURCHASE)->whereNOT('price', null)->orderBy('purchased_date', 'DESC');
		foreach ($rows as $row)
		{
			$returnData[] = array(
				'date' => $row->purchased_date,
				'price' => $row->price
			);
		}
		return $returnData;
	}

	public function AddProduct(int $productId, int $amount, string $bestBeforeDate, $transactionType, $purchasedDate, $price)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			$stockId = uniqid();

			$logRow = $this->Database->stock_log()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => $purchasedDate,
				'stock_id' => $stockId,
				'transaction_type' => $transactionType,
				'price' => $price
			));
			$logRow->save();

			$stockRow = $this->Database->stock()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => $purchasedDate,
				'stock_id' => $stockId,
				'price' => $price
			));
			$stockRow->save();

			return true;
		}
		else
		{
			throw new \Exception("Transaction type $transactionType is not valid (StockService.AddProduct)");
		}
	}

	public function ConsumeProduct(int $productId, int $amount, bool $spoiled, $transactionType)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			$productStockAmount = $this->Database->stock()->where('product_id', $productId)->sum('amount');
			$potentialStockEntries = $this->Database->stock()->where('product_id', $productId)->orderBy('best_before_date', 'ASC')->orderBy('purchased_date', 'ASC')->fetchAll(); //First expiring first, then first in first out

			if ($amount > $productStockAmount)
			{
				return false;
			}

			foreach ($potentialStockEntries as $stockEntry)
			{
				if ($amount == 0)
				{
					break;
				}

				if ($amount >= $stockEntry->amount) //Take the whole stock entry
				{
					$logRow = $this->Database->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $stockEntry->amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType,
						'price' => $stockEntry->price
					));
					$logRow->save();

					$amount -= $stockEntry->amount;
					$stockEntry->delete();
				}
				else //Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
				{
					$logRow = $this->Database->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType,
						'price' => $stockEntry->price
					));
					$logRow->save();

					$restStockAmount = $stockEntry->amount - $amount;
					$amount = 0;

					$stockEntry->update(array(
						'amount' => $restStockAmount
					));
				}
			}

			return true;
		}
		else
		{
			throw new Exception("Transaction type $transactionType is not valid (StockService.ConsumeProduct)");
		}
	}

	public function InventoryProduct(int $productId, int $newAmount, string $bestBeforeDate)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}
		
		$productStockAmount = $this->Database->stock()->where('product_id', $productId)->sum('amount');

		if ($newAmount > $productStockAmount)
		{
			$productDetails = $this->GetProductDetails($productId);
			$amountToAdd = $newAmount - $productStockAmount;
			$this->AddProduct($productId, $amountToAdd, $bestBeforeDate, self::TRANSACTION_TYPE_INVENTORY_CORRECTION, date('Y-m-d'), $productDetails['last_price']);
		}
		else if ($newAmount < $productStockAmount)
		{
			$amountToRemove = $productStockAmount - $newAmount;
			$this->ConsumeProduct($productId, $amountToRemove, false, self::TRANSACTION_TYPE_INVENTORY_CORRECTION);
		}

		return true;
	}

	public function AddMissingProductsToShoppingList()
	{
		$missingProducts = $this->GetMissingProducts();
		foreach ($missingProducts as $missingProduct)
		{
			$product = $this->Database->products()->where('id', $missingProduct->id)->fetch();
			$amount = ceil($missingProduct->amount_missing / $product->qu_factor_purchase_to_stock);

			$alreadyExistingEntry = $this->Database->shopping_list()->where('product_id', $missingProduct->id)->fetch();
			if ($alreadyExistingEntry) //Update
			{
				$alreadyExistingEntry->update(array(
					'amount_autoadded' => $amount
				));
			}
			else //Insert
			{
				$shoppinglistRow = $this->Database->shopping_list()->createRow(array(
					'product_id' => $missingProduct->id,
					'amount_autoadded' => $amount
				));
				$shoppinglistRow->save();
			}
		}
	}

	public function ClearShoppingList()
	{
		$this->Database->shopping_list()->delete();
	}

	private function ProductExists($productId)
	{
		$productRow = $this->Database->products()->where('id = :1', $productId)->fetch();
		return $productRow !== null;
	}

	private function LoadBarcodeLookupPlugin()
	{
		$pluginName = defined('GROCY_STOCK_BARCODE_LOOKUP_PLUGIN') ? GROCY_STOCK_BARCODE_LOOKUP_PLUGIN : '';
		if (empty($pluginName))
		{
			throw new \Exception('No barcode lookup plugin defined');
		}

		$path = GROCY_DATAPATH . "/plugins/$pluginName.php";
		if (file_exists($path))
		{
			require_once $path;
			return new $pluginName($this->Database->locations()->fetchAll(), $this->Database->quantity_units()->fetchAll());
		}
		else
		{
			throw new \Exception("Plugin $pluginName was not found");
		}
	}

	public function ExternalBarcodeLookup($barcode, $addFoundProduct)
	{
		$plugin = $this->LoadBarcodeLookupPlugin();
		$pluginOutput = $plugin->Lookup($barcode);

		if ($pluginOutput !== null) // Lookup was successful
		{
			if ($addFoundProduct === true)
			{
				// Add product to database and include new product id in output
				$newRow = $this->Database->products()->createRow($pluginOutput);
				$newRow->save();

				$pluginOutput['id'] = $newRow->id;
			}
		}

		return $pluginOutput;
	}
}
