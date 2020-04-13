<?php

namespace Grocy\Services;

class StockService extends BaseService
{
	const TRANSACTION_TYPE_PURCHASE = 'purchase';
	const TRANSACTION_TYPE_CONSUME = 'consume';
	const TRANSACTION_TYPE_TRANSFER_FROM = 'transfer_from';
	const TRANSACTION_TYPE_TRANSFER_TO = 'transfer_to';
	const TRANSACTION_TYPE_INVENTORY_CORRECTION = 'inventory-correction';
	const TRANSACTION_TYPE_STOCK_EDIT_NEW = 'stock-edit-new';
	const TRANSACTION_TYPE_STOCK_EDIT_OLD = 'stock-edit-old';
	const TRANSACTION_TYPE_PRODUCT_OPENED = 'product-opened';
	const TRANSACTION_TYPE_SELF_PRODUCTION = 'self-production';

	public function GetCurrentStock($includeNotInStockButMissingProducts = false)
	{
		$sql = 'SELECT * FROM stock_current';
		if ($includeNotInStockButMissingProducts)
		{
			$missingProductsView = 'stock_missing_products_including_opened';
			if (!GROCY_FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT)
			{
				$missingProductsView = 'stock_missing_products';
			}

			$sql = 'SELECT * FROM stock_current WHERE best_before_date IS NOT NULL UNION SELECT id, 0, 0, null, 0, 0, 0 FROM ' . $missingProductsView . ' WHERE id NOT IN (SELECT product_id FROM stock_current)';
		}
		$currentStockMapped = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_OBJ);
		
		$relevantProducts = $this->getDatabase()->products()->where('id IN (SELECT product_id FROM (' . $sql . ') x)');
		foreach ($relevantProducts as $product)
		{
			$currentStockMapped[$product->id][0]->product_id = $product->id;
			$currentStockMapped[$product->id][0]->product = $product;
		}

		return array_column($currentStockMapped, 0);
	}

	public function GetCurrentStockLocationContent()
	{
		$sql = 'SELECT sclc.* FROM stock_current_location_content sclc JOIN products p ON sclc.product_id = p.id ORDER BY p.name';
		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetCurrentStockLocations()
	{
		$sql = 'SELECT * FROM stock_current_locations';
		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetCurrentProductPrices()
	{
		$sql = 'SELECT * FROM products_current_price';
		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetMissingProducts()
	{
		$sql = 'SELECT * FROM stock_missing_products_including_opened';
		if (!GROCY_FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT)
		{
			$sql = 'SELECT * FROM stock_missing_products';
		}

		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetProductStockLocations($productId)
	{
		return $this->getDatabase()->stock_current_locations()->where('product_id', $productId)->fetchAll();
	}

	public function GetProductIdFromBarcode(string $barcode)
	{
		$potentialProduct = $this->getDatabase()->products()->where("','  || barcode || ',' LIKE '%,' || :1 || ',%' AND IFNULL(barcode, '') != ''", $barcode)->limit(1)->fetch();

		if ($potentialProduct === null)
		{
			throw new \Exception("No product with barcode $barcode found");
		}

		return intval($potentialProduct->id);
	}

	public function GetExpiringProducts(int $days = 5, bool $excludeExpired = false)
	{
		$currentStock = $this->GetCurrentStock(false);
		$currentStock = FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d 23:59:59', strtotime("+$days days")), '<');

		if ($excludeExpired)
		{
			$currentStock = FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d 23:59:59', strtotime('now')), '>');
		}

		return $currentStock;
	}

	public function GetProductDetails(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$stockCurrentRow = FindObjectinArrayByPropertyValue($this->GetCurrentStock(), 'product_id', $productId);

		if ($stockCurrentRow == null)
		{
			$stockCurrentRow = new \stdClass();
			$stockCurrentRow->amount = 0;
			$stockCurrentRow->amount_opened = 0;
			$stockCurrentRow->amount_aggregated = 0;
			$stockCurrentRow->amount_opened_aggregated = 0;
			$stockCurrentRow->is_aggregated_amount = 0;
		}

		$product = $this->getDatabase()->products($productId);
		$productLastPurchased = $this->getDatabase()->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_PURCHASE)->where('undone', 0)->max('purchased_date');
		$productLastUsed = $this->getDatabase()->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->where('undone', 0)->max('used_date');
		$nextBestBeforeDate = $this->getDatabase()->stock()->where('product_id', $productId)->min('best_before_date');
		$quPurchase = $this->getDatabase()->quantity_units($product->qu_id_purchase);
		$quStock = $this->getDatabase()->quantity_units($product->qu_id_stock);
		$location = $this->getDatabase()->locations($product->location_id);
		$averageShelfLifeDays = intval($this->getDatabase()->stock_average_product_shelf_life()->where('id', $productId)->fetch()->average_shelf_life_days);

		$lastPrice = null;
		$defaultShoppingLocation = null;
		$lastShoppingLocation = null;
		$lastLogRow = $this->getDatabase()->stock_log()->where('product_id = :1 AND transaction_type IN (:2, :3) AND undone = 0', $productId, self::TRANSACTION_TYPE_PURCHASE, self::TRANSACTION_TYPE_INVENTORY_CORRECTION)->orderBy('row_created_timestamp', 'DESC')->limit(1)->fetch();
		if ($lastLogRow !== null && !empty($lastLogRow))
		{
			$lastPrice = $lastLogRow->price;
			$lastShoppingLocation = $lastLogRow->shopping_location_id;
		}

		$consumeCount = $this->getDatabase()->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->where('undone = 0 AND spoiled = 0')->sum('amount') * -1;
		$consumeCountSpoiled = $this->getDatabase()->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->where('undone = 0 AND spoiled = 1')->sum('amount') * -1;
		if ($consumeCount == 0)
		{
			$consumeCount = 1;
		}
		$spoilRate = ($consumeCountSpoiled * 100) / $consumeCount;

		return array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $stockCurrentRow->amount,
			'stock_amount_opened' => $stockCurrentRow->amount_opened,
			'stock_amount_aggregated' => $stockCurrentRow->amount_aggregated,
			'stock_amount_opened_aggregated' => $stockCurrentRow->amount_opened_aggregated,
			'quantity_unit_purchase' => $quPurchase,
			'quantity_unit_stock' => $quStock,
			'last_price' => $lastPrice,
			'last_shopping_location_id' => $lastShoppingLocation,
			'default_shopping_location_id' => $product->shopping_location_id,
			'next_best_before_date' => $nextBestBeforeDate,
			'location' => $location,
			'average_shelf_life_days' => $averageShelfLifeDays,
			'spoil_rate_percent' => $spoilRate,
			'is_aggregated_amount' => $stockCurrentRow->is_aggregated_amount
		);
	}

	public function GetProductPriceHistory(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$returnData = array();
		$shoppingLocations = $this->getDatabase()->shopping_locations();
		$rows = $this->getDatabase()->stock_log()->where('product_id = :1 AND transaction_type IN (:2, :3) AND undone = 0', $productId, self::TRANSACTION_TYPE_PURCHASE, self::TRANSACTION_TYPE_INVENTORY_CORRECTION)->whereNOT('price', null)->orderBy('purchased_date', 'DESC');
		foreach ($rows as $row)
		{
			$returnData[] = array(
				'date' => $row->purchased_date,
				'price' => $row->price,
				'shopping_location' => FindObjectInArrayByPropertyValue($shoppingLocations, 'id', $row->shopping_location_id),
			);
		}
		return $returnData;
	}

	public function GetStockEntry($entryId)
	{
		return $this->getDatabase()->stock()->where('id', $entryId)->fetch();
	}

	public function GetProductStockEntries($productId, $excludeOpened = false, $allowSubproductSubstitution = false)
	{
		// In order of next use:
		// First expiring first, then first in first out

		$sqlWhereProductId = 'product_id = :1';
		if ($allowSubproductSubstitution)
		{
			$sqlWhereProductId = '(product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = :1) OR product_id = :1)';
		}

		$sqlWhereAndOpen = 'AND open IN (0, 1)';
		if ($excludeOpened)
		{
			$sqlWhereAndOpen = 'AND open = 0';
		}

		return $this->getDatabase()->stock()->where($sqlWhereProductId . ' ' . $sqlWhereAndOpen, $productId)->orderBy('best_before_date', 'ASC')->orderBy('purchased_date', 'ASC')->fetchAll();
	}

	public function GetProductStockEntriesForLocation($productId, $locationId, $excludeOpened = false, $allowSubproductSubstitution = false)
	{
		$stockEntries = $this->GetProductStockEntries($productId, $excludeOpened, $allowSubproductSubstitution);
		return FindAllObjectsInArrayByPropertyValue($stockEntries, 'location_id', $locationId);
	}

	public function AddProduct(int $productId, float $amount, $bestBeforeDate, $transactionType, $purchasedDate, $price, $locationId = null, $shoppingLocationId = null, &$transactionId = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// The amount to be posted needs to be the given amount - stock amount - tare weight
		$productDetails = (object)$this->GetProductDetails($productId);
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			if ($amount <= floatval($productDetails->product->tare_weight) + floatval($productDetails->stock_amount))
			{
				throw new \Exception('The amount cannot be lower or equal than the defined tare weight + current stock amount');
			}

			$amount = $amount - floatval($productDetails->stock_amount) - floatval($productDetails->product->tare_weight);
		}

		//Sets the default best before date, if none is supplied
		if ($bestBeforeDate == null)
		{
			if (intval($productDetails->product->default_best_before_days) == -1)
			{
				$bestBeforeDate = date('2999-12-31');
			}
			else if (intval($productDetails->product->default_best_before_days) > 0)
			{
				$bestBeforeDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + '.$productDetails->product->default_best_before_days.' days'));
			}
			else
			{
				$bestBeforeDate = date('Y-m-d');
			}
		}

		if ($transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION || $transactionType == self::TRANSACTION_TYPE_SELF_PRODUCTION)
		{
			if ($transactionId === null)
			{
				$transactionId = uniqid();
			}

			$stockId = uniqid();

			$logRow = $this->getDatabase()->stock_log()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => $purchasedDate,
				'stock_id' => $stockId,
				'transaction_type' => $transactionType,
				'price' => $price,
				'location_id' => $locationId,
				'transaction_id' => $transactionId,
				'shopping_location_id' => $shoppingLocationId,
			));
			$logRow->save();

			$returnValue = $this->getDatabase()->lastInsertId();

			$stockRow = $this->getDatabase()->stock()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => $purchasedDate,
				'stock_id' => $stockId,
				'price' => $price,
				'location_id' => $locationId,
				'shopping_location_id' => $shoppingLocationId,
			));
			$stockRow->save();

			return $returnValue;
		}
		else
		{
			throw new \Exception("Transaction type $transactionType is not valid (StockService.AddProduct)");
		}
	}

	public function ConsumeProduct(int $productId, float $amount, bool $spoiled, $transactionType, $specificStockEntryId = 'default', $recipeId = null, $locationId = null, &$transactionId = null, $allowSubproductSubstitution = false)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if ($locationId !== null && !$this->LocationExists($locationId))
		{
			throw new \Exception('Location does not exist');
		}

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// The amount to be posted needs to be the absolute value of the given amount - stock amount - tare weight
		$productDetails = (object)$this->GetProductDetails($productId);
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			if ($amount < floatval($productDetails->product->tare_weight))
			{
				throw new \Exception('The amount cannot be lower than the defined tare weight');
			}

			$amount = abs($amount - floatval($productDetails->stock_amount) - floatval($productDetails->product->tare_weight));
		}

		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{

			if ($locationId === null) // Consume from any location
			{
				$potentialStockEntries = $this->GetProductStockEntries($productId, false, $allowSubproductSubstitution);
			}
			else // Consume only from the supplied location
			{
				$potentialStockEntries = $this->GetProductStockEntriesForLocation($productId, $locationId, false, $allowSubproductSubstitution);
			}

			$productStockAmount = SumArrayValue($potentialStockEntries, 'amount');
			if ($amount > $productStockAmount)
			{
				throw new \Exception('Amount to be consumed cannot be > current stock amount (if supplied, at the desired location)');
			}

			if ($specificStockEntryId !== 'default')
			{
				$potentialStockEntries = FindAllObjectsInArrayByPropertyValue($potentialStockEntries, 'stock_id', $specificStockEntryId);
			}

			if ($transactionId === null)
			{
				$transactionId = uniqid();
			}

			foreach ($potentialStockEntries as $stockEntry)
			{
				if ($amount == 0)
				{
					break;
				}

				if ($amount >= $stockEntry->amount) // Take the whole stock entry
				{
					$logRow = $this->getDatabase()->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $stockEntry->amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType,
						'price' => $stockEntry->price,
						'opened_date' => $stockEntry->opened_date,
						'recipe_id' => $recipeId,
						'transaction_id' => $transactionId
					));
					$logRow->save();

					$stockEntry->delete();

					$amount -= $stockEntry->amount;
				}
				else // Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
				{
					$restStockAmount = $stockEntry->amount - $amount;

					$logRow = $this->getDatabase()->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType,
						'price' => $stockEntry->price,
						'opened_date' => $stockEntry->opened_date,
						'recipe_id' => $recipeId,
						'transaction_id' => $transactionId
					));
					$logRow->save();

					$stockEntry->update(array(
						'amount' => $restStockAmount
					));

					$amount = 0;
				}
			}

			return $this->getDatabase()->lastInsertId();
		}
		else
		{
			throw new Exception("Transaction type $transactionType is not valid (StockService.ConsumeProduct)");
		}
	}

	public function TransferProduct(int $productId, float $amount, int $locationIdFrom, int $locationIdTo, $specificStockEntryId = 'default', &$transactionId = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		if (!$this->LocationExists($locationIdFrom))
		{
			throw new \Exception('Source location does not exist');
		}

		if (!$this->LocationExists($locationIdTo))
		{
			throw new \Exception('Destination location does not exist');
		}

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// The amount to be posted needs to be the absolute value of the given amount - stock amount - tare weight
		$productDetails = (object)$this->GetProductDetails($productId);
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			// Hard fail for now, as we not yet support transfering tare weight enabled products
			throw new \Exception('Transfering tare weight enabled products is not yet possible');

			if ($amount < floatval($productDetails->product->tare_weight))
			{
				throw new \Exception('The amount cannot be lower than the defined tare weight');
			}

			$amount = abs($amount - floatval($productDetails->stock_amount) - floatval($productDetails->product->tare_weight));
		}

		$productStockAmountAtFromLocation = $this->getDatabase()->stock()->where('product_id = :1 AND location_id = :2', $productId, $locationIdFrom)->sum('amount');
		$potentialStockEntriesAtFromLocation = $this->GetProductStockEntriesForLocation($productId, $locationIdFrom);

		if ($amount > $productStockAmountAtFromLocation)
		{
			throw new \Exception('Amount to be transfered cannot be > current stock amount at the source location');
		}

		if ($specificStockEntryId !== 'default')
		{
			$potentialStockEntriesAtFromLocation = FindAllObjectsInArrayByPropertyValue($potentialStockEntriesAtFromLocation, 'stock_id', $specificStockEntryId);
		}

		if ($transactionId === null)
		{
			$transactionId = uniqid();
		}

		foreach ($potentialStockEntriesAtFromLocation as $stockEntry)
		{
			if ($amount == 0)
			{
				break;
			}

			$newBestBeforeDate = $stockEntry->best_before_date;

			if (GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			{
				$locationFrom = $this->getDatabase()->locations()->where('id', $locationIdFrom)->fetch();
				$locationTo = $this->getDatabase()->locations()->where('id', $locationIdTo)->fetch();

				// Product was moved from a non-freezer to freezer location -> freeze
				if (intval($locationFrom->is_freezer) === 0 && intval($locationTo->is_freezer) === 1 && $productDetails->product->default_best_before_days_after_freezing > 0)
				{
					$newBestBeforeDate = date("Y-m-d", strtotime('+' . $productDetails->product->default_best_before_days_after_freezing . ' days'));
				}

				// Product was moved from a freezer to non-freezer location -> thaw
				if (intval($locationFrom->is_freezer) === 1 && intval($locationTo->is_freezer) === 0 && $productDetails->product->default_best_before_days_after_thawing > 0)
				{
					$newBestBeforeDate = date("Y-m-d", strtotime('+' . $productDetails->product->default_best_before_days_after_thawing . ' days'));
				}
			}

			$correlationId = uniqid();
			if ($amount >= $stockEntry->amount) // Take the whole stock entry
			{
				$logRowForLocationFrom = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount * -1,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_FROM,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $stockEntry->location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId
				));
				$logRowForLocationFrom->save();

				$logRowForLocationTo = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_TO,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $locationIdTo,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId
				));
				$logRowForLocationTo->save();

				$stockEntry->update(array(
					'location_id' => $locationIdTo,
					'best_before_date' => $newBestBeforeDate
				));

				$amount -= $stockEntry->amount;
			}
			else // Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
			{
				$restStockAmount = $stockEntry->amount - $amount;

				$logRowForLocationFrom = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount * -1,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_FROM,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $stockEntry->location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId
				));
				$logRowForLocationFrom->save();

				$logRowForLocationTo = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_TO,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $locationIdTo,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId
				));
				$logRowForLocationTo->save();

				// This is the existing stock entry -> remains at the source location with the rest amount
				$stockEntry->update(array(
					'amount' => $restStockAmount
				));

				// The transfered amount gets into a new stock entry
				$stockEntryNew = $this->getDatabase()->stock()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'price' => $stockEntry->price,
					'location_id' => $locationIdTo,
					'open' => $stockEntry->open,
					'opened_date' => $stockEntry->opened_date
				));
				$stockEntryNew->save();

				$amount = 0;
			}
		}

		return $this->getDatabase()->lastInsertId();
	}

	public function EditStockEntry(int $stockRowId, float $amount, $bestBeforeDate, $locationId, $shoppingLocationId, $price, $open, $purchasedDate)
	{

		$stockRow = $this->getDatabase()->stock()->where('id = :1', $stockRowId)->fetch();

		if ($stockRow === null)
		{
			throw new \Exception('Stock does not exist');
		}

		$correlationId = uniqid();
		$transactionId = uniqid();
		$logOldRowForStockUpdate = $this->getDatabase()->stock_log()->createRow(array(
			'product_id' => $stockRow->product_id,
			'amount' => $stockRow->amount,
			'best_before_date' => $stockRow->best_before_date,
			'purchased_date' => $stockRow->purchased_date,
			'stock_id' => $stockRow->stock_id,
			'transaction_type' => self::TRANSACTION_TYPE_STOCK_EDIT_OLD,
			'price' => $stockRow->price,
			'opened_date' => $stockRow->opened_date,
			'location_id' => $stockRow->location_id,
			'shopping_location_id' => $stockRow->shopping_location_id,
			'correlation_id' => $correlationId,
			'transaction_id' => $transactionId,
			'stock_row_id' => $stockRow->id
		));
		$logOldRowForStockUpdate->save();

		$openedDate = $stockRow->opened_date;
		if ($open && $openedDate == null)
		{
			$openedDate = date('Y-m-d');
		}
		else if (!$open)
		{
			$openedDate = null;
		}

		$stockRow->update(array(
			'amount' => $amount,
			'price' => $price,
			'best_before_date' => $bestBeforeDate,
			'location_id' => $locationId,
			'shopping_location_id' => $shoppingLocationId,
			'opened_date' => $openedDate,
			'open' => $open,
			'purchased_date' => $purchasedDate
		));

		$logNewRowForStockUpdate = $this->getDatabase()->stock_log()->createRow(array(
			'product_id' => $stockRow->product_id,
			'amount' => $amount,
			'best_before_date' => $bestBeforeDate,
			'purchased_date' => $stockRow->purchased_date,
			'stock_id' => $stockRow->stock_id,
			'transaction_type' => self::TRANSACTION_TYPE_STOCK_EDIT_NEW,
			'price' => $price,
			'opened_date' => $stockRow->opened_date,
			'location_id' => $locationId,
			'shopping_location_id' => $shoppingLocationId,
			'correlation_id' => $correlationId,
			'transaction_id' => $transactionId,
			'stock_row_id' => $stockRow->id
		));
		$logNewRowForStockUpdate->save();

		return $this->getDatabase()->lastInsertId();
	}

	public function InventoryProduct(int $productId, float $newAmount, $bestBeforeDate, $locationId = null, $price = null, $shoppingLocationId = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$productDetails = (object)$this->GetProductDetails($productId);

		if ($price === null)
		{
			$price = $productDetails->last_price;
		}

		if ($shoppingLocationId === null)
		{
			$shoppingLocationId = $productDetails->last_shopping_location_id;
		}

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// So assume that the amount in stock is the amount also including the container weight
		$containerWeight = 0;
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			$containerWeight = floatval($productDetails->product->tare_weight);
		}

		if ($newAmount == floatval($productDetails->stock_amount) + $containerWeight)
		{
			throw new \Exception('The new amount cannot equal the current stock amount');
		}
		else if ($newAmount > floatval($productDetails->stock_amount) + $containerWeight)
		{
			$bookingAmount = $newAmount - floatval($productDetails->stock_amount);
			if ($productDetails->product->enable_tare_weight_handling == 1)
			{
				$bookingAmount = $newAmount;
			}

			return $this->AddProduct($productId, $bookingAmount, $bestBeforeDate, self::TRANSACTION_TYPE_INVENTORY_CORRECTION, date('Y-m-d'), $price, $locationId, $shoppingLocationId);
		}
		else if ($newAmount < $productDetails->stock_amount + $containerWeight)
		{
			$bookingAmount = $productDetails->stock_amount - $newAmount;
			if ($productDetails->product->enable_tare_weight_handling == 1)
			{
				$bookingAmount = $newAmount;
			}

			return $this->ConsumeProduct($productId, $bookingAmount, false, self::TRANSACTION_TYPE_INVENTORY_CORRECTION);
		}

		return null;
	}

	public function OpenProduct(int $productId, float $amount, $specificStockEntryId = 'default', &$transactionId = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$productStockAmountUnopened = $this->getDatabase()->stock()->where('product_id = :1 AND open = 0', $productId)->sum('amount');
		$potentialStockEntries = $this->GetProductStockEntries($productId, true);
		$product = $this->getDatabase()->products($productId);

		if ($amount > $productStockAmountUnopened)
		{
			throw new \Exception('Amount to be opened cannot be > current unopened stock amount');
		}

		if ($specificStockEntryId !== 'default')
		{
			$potentialStockEntries = FindAllObjectsInArrayByPropertyValue($potentialStockEntries, 'stock_id', $specificStockEntryId);
		}

		if ($transactionId === null)
		{
			$transactionId = uniqid();
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
				$logRow = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d'),
					'transaction_id' => $transactionId
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

				$newStockRow = $this->getDatabase()->stock()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $restStockAmount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'price' => $stockEntry->price
				));
				$newStockRow->save();

				$logRow = $this->getDatabase()->stock_log()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d'),
					'transaction_id' => $transactionId
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

		return $this->getDatabase()->lastInsertId();
	}

	public function AddMissingProductsToShoppingList($listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$missingProducts = $this->GetMissingProducts();
		foreach ($missingProducts as $missingProduct)
		{
			$product = $this->getDatabase()->products()->where('id', $missingProduct->id)->fetch();
			$amountToAdd = ceil($missingProduct->amount_missing / $product->qu_factor_purchase_to_stock);

			$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id', $missingProduct->id)->fetch();
			if ($alreadyExistingEntry) // Update
			{
				if ($alreadyExistingEntry->amount < $amountToAdd)
				{
					$alreadyExistingEntry->update(array(
						'amount' => $amountToAdd,
						'shopping_list_id' => $listId
					));
				}
			}
			else // Insert
			{
				$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow(array(
					'product_id' => $missingProduct->id,
					'amount' => $amountToAdd,
					'shopping_list_id' => $listId
				));
				$shoppinglistRow->save();
			}
		}
	}

	public function ClearShoppingList($listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$this->getDatabase()->shopping_list()->where('shopping_list_id = :1', $listId)->delete();
	}


	public function RemoveProductFromShoppingList($productId, $amount = 1, $listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$productRow = $this->getDatabase()->shopping_list()->where('product_id = :1', $productId)->fetch();

		//If no entry was found with for this product, we return gracefully
		if ($productRow != null && !empty($productRow))
		{
			$newAmount = $productRow->amount - $amount;
			if ($newAmount < 1)
			{
				$productRow->delete();
			}
			else
			{
				$productRow->update(array('amount' => $newAmount));
			}

		}
	}

	public function AddProductToShoppingList($productId, $amount = 1, $note = null, $listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist');
		}

		$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id = :1 AND shopping_list_id = :2', $productId, $listId)->fetch();
		if ($alreadyExistingEntry) // Update
		{
			$alreadyExistingEntry->update(array(
				'amount' => ($alreadyExistingEntry->amount + $amount),
				'shopping_list_id' => $listId,
				'note' => $note
			));
		}
		else // Insert
		{
			$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'shopping_list_id' => $listId,
				'note' => $note
			));
			$shoppinglistRow->save();
		}
	}

	private function ProductExists($productId)
	{
		$productRow = $this->getDatabase()->products()->where('id = :1', $productId)->fetch();
		return $productRow !== null;
	}

	private function LocationExists($locationId)
	{
		$locationRow = $this->getDatabase()->locations()->where('id = :1', $locationId)->fetch();
		return $locationRow !== null;
	}

	private function ShoppingListExists($listId)
	{
		$shoppingListRow = $this->getDatabase()->shopping_lists()->where('id = :1', $listId)->fetch();
		return $shoppingListRow !== null;
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
			return new $pluginName($this->getDatabase()->locations()->fetchAll(), $this->getDatabase()->quantity_units()->fetchAll());
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
				$newRow = $this->getDatabase()->products()->createRow($pluginOutput);
				$newRow->save();

				$pluginOutput['id'] = $newRow->id;
			}
		}

		return $pluginOutput;
	}

	public function UndoBooking($bookingId, $skipCorrelatedBookings = false)
	{
		$logRow = $this->getDatabase()->stock_log()->where('id = :1 AND undone = 0', $bookingId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Booking does not exist or was already undone');
		}

		// Undo all correlated bookings first, in order from newest first to the oldest
		if (!$skipCorrelatedBookings && !empty($logRow->correlation_id))
		{
			$correlatedBookings = $this->getDatabase()->stock_log()->where('undone = 0 AND correlation_id = :1', $logRow->correlation_id)->orderBy('id', 'DESC')->fetchAll();
			foreach ($correlatedBookings as $correlatedBooking)
			{
				$this->UndoBooking($correlatedBooking->id, true);
			}
			return;
		}

		$hasSubsequentBookings = $this->getDatabase()->stock_log()->where('stock_id = :1 AND id != :2 AND (correlation_id is not null OR correlation_id != :3) AND id > :2 AND undone = 0', $logRow->stock_id, $logRow->id, $logRow->correlation_id)->count() > 0;
		if ($hasSubsequentBookings)
		{
			throw new \Exception('Booking has subsequent dependent bookings, undo not possible');
		}

		if ($logRow->transaction_type === self::TRANSACTION_TYPE_PURCHASE || ($logRow->transaction_type === self::TRANSACTION_TYPE_INVENTORY_CORRECTION && $logRow->amount > 0))
		{
			// Remove corresponding stock entry
			$stockRows = $this->getDatabase()->stock()->where('stock_id', $logRow->stock_id);
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
			$stockRow = $this->getDatabase()->stock()->createRow(array(
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
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_TRANSFER_TO)
		{
			$stockRow = $this->getDatabase()->stock()->where('stock_id = :1 AND location_id = :2', $logRow->stock_id, $logRow->location_id)->fetch();
			if ($stockRow === null)
			{
				throw new \Exception('Booking does not exist or was already undone');
			}
			$newAmount = $stockRow->amount - $logRow->amount;

			if ($newAmount == 0)
			{
				$stockRow->delete();
			} else {
			// Remove corresponding amount back to stock
				$stockRow->update(array(
					'amount' => $newAmount
				));
			}

			// Update log entry
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_TRANSFER_FROM)
		{
			// Add corresponding amount back to stock or
			// create a row if missing
			$stockRow = $this->getDatabase()->stock()->where('stock_id = :1 AND location_id = :2', $logRow->stock_id, $logRow->location_id)->fetch();
			if ($stockRow === null)
			{
				$stockRow = $this->getDatabase()->stock()->createRow(array(
					'product_id' => $logRow->product_id,
					'amount' => $logRow->amount * -1,
					'best_before_date' => $logRow->best_before_date,
					'purchased_date' => $logRow->purchased_date,
					'stock_id' => $logRow->stock_id,
					'price' => $logRow->price,
					'opened_date' => $logRow->opened_date
				));
				$stockRow->save();
			} else {
				$stockRow->update(array(
					'amount' => $stockRow->amount -	$logRow->amount
				));
			}

			// Update log entry
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_PRODUCT_OPENED)
		{
			// Remove opened flag from corresponding log entry
			$stockRows = $this->getDatabase()->stock()->where('stock_id = :1 AND amount = :2 AND purchased_date = :3', $logRow->stock_id, $logRow->amount, $logRow->purchased_date)->limit(1);
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
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_STOCK_EDIT_NEW)
		{
			// Update log entry, no action needed
			$logRow->update(array(
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			));
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_STOCK_EDIT_OLD)
		{
			// Make sure there is a stock row still
			$stockRow = $this->getDatabase()->stock()->where('id = :1', $logRow->stock_row_id)->fetch();
			if ($stockRow == null)
			{
				throw new \Exception('Booking does not exist or was already undone');
			}

			$openedDate = $logRow->opened_date;
			$open = true;
			if ($openedDate == null)
			{
				$open = false;
			}

			$stockRow->update(array(
				'amount' => $logRow->amount,
				'best_before_date' => $logRow->best_before_date,
				'purchased_date' => $logRow->purchased_date,
				'price' => $logRow->price,
				'location_id' => $logRow->location_id,
				'open' => $open,
				'opened_date' => $openedDate
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

	public function UndoTransaction($transactionId)
	{
		$transactionBookings = $this->getDatabase()->stock_log()->where('undone = 0 AND transaction_id = :1', $transactionId)->orderBy('id', 'DESC')->fetchAll();

		if (count($transactionBookings) === 0)
		{
			throw new \Exception('This transaction was not found or already undone');
		}

		foreach ($transactionBookings as $transactionBooking)
		{
			$this->UndoBooking($transactionBooking->id, true);
		}
	}
}
