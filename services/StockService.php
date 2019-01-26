<?php

namespace Grocy\Services;

class StockService extends BaseService
{
	const TRANSACTION_TYPE_PURCHASE = 'purchase';
	const TRANSACTION_TYPE_CONSUME = 'consume';
	const TRANSACTION_TYPE_INVENTORY_CORRECTION = 'inventory-correction';
	const TRANSACTION_TYPE_PRODUCT_OPENED = 'product-opened';

	public function GetCurrentStock($includeNotInStockButMissingProducts = false)
	{
		$sql = 'SELECT * from stock_current';
		if ($includeNotInStockButMissingProducts)
		{
			$sql = 'SELECT * from stock_current WHERE best_before_date IS NOT NULL';
		}
		
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetMissingProducts()
	{
		$sql = 'SELECT * from stock_missing_products';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetExpiringProducts(int $days = 5, bool $excludeExpired = false)
	{
		$currentStock = $this->GetCurrentStock(true);
		$currentStock = FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime("+$days days")), '<');

		if ($excludeExpired)
		{
			$currentStock = FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime('now')), '>');
		}

		return $currentStock;
	}

	public function GetProductDetails(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$product = $this->Database->products($productId);
		$productStockAmount = $this->Database->stock()->where('product_id', $productId)->sum('amount');
		$productStockAmountOpened = $this->Database->stock()->where('product_id = :1 AND open = 1', $productId)->sum('amount');
		$productLastPurchased = $this->Database->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_PURCHASE)->where('undone', 0)->max('purchased_date');
		$productLastUsed = $this->Database->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->where('undone', 0)->max('used_date');
		$nextBestBeforeDate = $this->Database->stock()->where('product_id', $productId)->min('best_before_date');
		$quPurchase = $this->Database->quantity_units($product->qu_id_purchase);
		$quStock = $this->Database->quantity_units($product->qu_id_stock);
		
		$lastPrice = null;
		$lastLogRow = $this->Database->stock_log()->where('product_id = :1 AND transaction_type = :2 AND undone = 0', $productId, self::TRANSACTION_TYPE_PURCHASE)->orderBy('row_created_timestamp', 'DESC')->limit(1)->fetch();
		if ($lastLogRow !== null && !empty($lastLogRow))
		{
			$lastPrice = $lastLogRow->price;
		}

		return array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $productStockAmount,
			'stock_amount_opened' => $productStockAmountOpened,
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
		$rows = $this->Database->stock_log()->where('product_id = :1 AND transaction_type = :2 AND undone = 0', $productId, self::TRANSACTION_TYPE_PURCHASE)->whereNOT('price', null)->orderBy('purchased_date', 'DESC');
		foreach ($rows as $row)
		{
			$returnData[] = array(
				'date' => $row->purchased_date,
				'price' => $row->price
			);
		}
		return $returnData;
	}

	public function GetProductStockEntries($productId, $excludeOpened = false)
	{
		// In order of next use:
		// First expiring first, then first in first out

		if ($excludeOpened)
		{
			return $this->Database->stock()->where('product_id = :1 AND open = 0', $productId)->orderBy('best_before_date', 'ASC')->orderBy('purchased_date', 'ASC')->fetchAll();
		}
		else
		{
			return $this->Database->stock()->where('product_id', $productId)->orderBy('best_before_date', 'ASC')->orderBy('purchased_date', 'ASC')->fetchAll();
		}
	}

	public function AddProduct(int $productId, int $amount, string $bestBeforeDate, $transactionType, $purchasedDate, $price)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if ($transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
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

			$returnValue = $this->Database->lastInsertId();

			$stockRow = $this->Database->stock()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => $purchasedDate,
				'stock_id' => $stockId,
				'price' => $price
			));
			$stockRow->save();

			return $returnValue;
		}
		else
		{
			throw new \Exception("Transaction type $transactionType is not valid (StockService.AddProduct)");
		}
	}

	public function ConsumeProduct(int $productId, int $amount, bool $spoiled, $transactionType, $specificStockEntryId = 'default')
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			$productStockAmount = $this->Database->stock()->where('product_id', $productId)->sum('amount');
			$potentialStockEntries = $this->GetProductStockEntries($productId);

			if ($amount > $productStockAmount)
			{
				return false;
			}

			if ($specificStockEntryId !== 'default')
			{
				$potentialStockEntries = FindAllObjectsInArrayByPropertyValue($potentialStockEntries, 'stock_id', $specificStockEntryId);
			}

			foreach ($potentialStockEntries as $stockEntry)
			{
				if ($amount == 0)
				{
					break;
				}

				if ($amount >= $stockEntry->amount) // Take the whole stock entry
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
						'price' => $stockEntry->price,
						'opened_date' => $stockEntry->opened_date
					));
					$logRow->save();

					$stockEntry->delete();

					$amount -= $stockEntry->amount;
				}
				else // Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
				{
					$restStockAmount = $stockEntry->amount - $amount;

					$logRow = $this->Database->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType,
						'price' => $stockEntry->price,
						'opened_date' => $stockEntry->opened_date
					));
					$logRow->save();

					$stockEntry->update(array(
						'amount' => $restStockAmount
					));

					$amount = 0;
				}
			}

			return $this->Database->lastInsertId();
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

		return $this->Database->lastInsertId();
	}

	public function OpenProduct(int $productId, int $amount, $specificStockEntryId = 'default')
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$productStockAmountUnopened = $this->Database->stock()->where('product_id = :1 AND open = 0', $productId)->sum('amount');
		$potentialStockEntries = $this->GetProductStockEntries($productId, true);
		$product = $this->Database->products($productId);

		if ($amount > $productStockAmountUnopened)
		{
			return false;
		}

		if ($specificStockEntryId !== 'default')
		{
			$potentialStockEntries = FindAllObjectsInArrayByPropertyValue($potentialStockEntries, 'stock_id', $specificStockEntryId);
		}

		foreach ($potentialStockEntries as $stockEntry)
		{
			if ($amount == 0)
			{
				break;
			}

			$newBestBeforeDate = $stockEntry->best_before_date;
			if ($product->default_best_before_days_after_open > 0)
			{
				 $newBestBeforeDate = date("Y-m-d", strtotime('+' . $product->default_best_before_days_after_open . ' days'));
			}

			if ($amount >= $stockEntry->amount) // Mark the whole stock entry as opened
			{
				$logRow = $this->Database->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d')
				));
				$logRow->save();

				$stockEntry->update(array(
					'open' => 1,
					'opened_date' => date('Y-m-d'),
					'best_before_date' => $newBestBeforeDate
				));

				$amount -= $stockEntry->amount;
			}
			else // Stock entry amount is > than needed amount -> split the stock entry
			{
				$restStockAmount = $stockEntry->amount - $amount;

				$newStockRow = $this->Database->stock()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $restStockAmount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'price' => $stockEntry->price
				));
				$newStockRow->save();

				$logRow = $this->Database->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d')
				));
				$logRow->save();

				$stockEntry->update(array(
					'amount' => $amount,
					'open' => 1,
					'opened_date' => date('Y-m-d'),
					'best_before_date' => $newBestBeforeDate
				));

				$amount = 0;
			}
		}

		return $this->Database->lastInsertId();
	}

	public function AddMissingProductsToShoppingList()
	{
		$missingProducts = $this->GetMissingProducts();
		foreach ($missingProducts as $missingProduct)
		{
			$product = $this->Database->products()->where('id', $missingProduct->id)->fetch();
			$amountToAdd = ceil($missingProduct->amount_missing / $product->qu_factor_purchase_to_stock);

			$alreadyExistingEntry = $this->Database->shopping_list()->where('product_id', $missingProduct->id)->fetch();
			if ($alreadyExistingEntry) // Update
			{
				if ($alreadyExistingEntry->amount < $amountToAdd)
				{
					$alreadyExistingEntry->update(array(
						'amount' => $amountToAdd
					));
				}
			}
			else // Insert
			{
				$shoppinglistRow = $this->Database->shopping_list()->createRow(array(
					'product_id' => $missingProduct->id,
					'amount' => $amountToAdd
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

	public function UndoBooking($bookingId)
	{
		$logRow = $this->Database->stock_log()->where('id = :1 AND undone = 0', $bookingId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Booking does not exist or was already undone');
		}

		$hasSubsequentBookings = $this->Database->stock_log()->where('stock_id = :1 AND id != :2 AND id > :2', $logRow->stock_id, $logRow->id)->count() > 0;
		if ($hasSubsequentBookings)
		{
			throw new \Exception('Booking has subsequent dependent bookings, undo not possible');
		}

		if ($logRow->transaction_type === self::TRANSACTION_TYPE_PURCHASE || ($logRow->transaction_type === self::TRANSACTION_TYPE_INVENTORY_CORRECTION && $logRow->amount > 0))
		{
			// Remove corresponding stock entry
			$stockRows = $this->Database->stock()->where('stock_id', $logRow->stock_id);
			$stockRows->delete();

			// Update log entry
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_CONSUME || ($logRow->transaction_type === self::TRANSACTION_TYPE_INVENTORY_CORRECTION && $logRow->amount < 0))
		{
			// Add corresponding amount back to stock
			$stockRow = $this->Database->stock()->createRow(array(
				'product_id' => $logRow->product_id,
				'amount' => $logRow->amount * -1,
				'best_before_date' => $logRow->best_before_date,
				'purchased_date' => $logRow->purchased_date,
				'stock_id' => $logRow->stock_id,
				'price' => $logRow->price,
				'opened_date' => $logRow->opened_date
			));
			$stockRow->save();

			// Update log entry
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_PRODUCT_OPENED)
		{
			// Remove opened flag from corresponding log entry
			$stockRows = $this->Database->stock()->where('stock_id = :1 AND amount = :2 AND purchased_date = :3', $logRow->stock_id, $logRow->amount, $logRow->purchased_date)->limit(1);
			$stockRows->update(array(
				'open' => 0,
				'opened_date' => null
			));

			// Update log entry
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		else
		{
			throw new \Exception('This booking cannot be undone');
		}
	}
}
