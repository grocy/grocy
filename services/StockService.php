<?php

namespace Grocy\Services;

use Grocy\Helpers\Grocycode;
use Grocy\Helpers\WebhookRunner;
use GuzzleHttp\Client;

class StockService extends BaseService
{
	const TRANSACTION_TYPE_CONSUME = 'consume';
	const TRANSACTION_TYPE_INVENTORY_CORRECTION = 'inventory-correction';
	const TRANSACTION_TYPE_PRODUCT_OPENED = 'product-opened';
	const TRANSACTION_TYPE_PURCHASE = 'purchase';
	const TRANSACTION_TYPE_SELF_PRODUCTION = 'self-production';
	const TRANSACTION_TYPE_STOCK_EDIT_NEW = 'stock-edit-new';
	const TRANSACTION_TYPE_STOCK_EDIT_OLD = 'stock-edit-old';
	const TRANSACTION_TYPE_TRANSFER_FROM = 'transfer_from';
	const TRANSACTION_TYPE_TRANSFER_TO = 'transfer_to';

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
			$amountToAdd = round($missingProduct->amount_missing, 2);

			$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id', $missingProduct->id)->fetch();
			if ($alreadyExistingEntry)
			{
				// Update
				if ($alreadyExistingEntry->amount < $amountToAdd)
				{
					$alreadyExistingEntry->update([
						'amount' => $amountToAdd,
						'shopping_list_id' => $listId
					]);
				}
			}
			else
			{
				// Insert
				$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow([
					'product_id' => $missingProduct->id,
					'amount' => $amountToAdd,
					'shopping_list_id' => $listId,
					'qu_id' => $product->qu_id_purchase
				]);
				$shoppinglistRow->save();
			}
		}
	}

	public function AddOverdueProductsToShoppingList($listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$overdueProducts = $this->GetDueProducts(-1);
		foreach ($overdueProducts as $overdueProduct)
		{
			$product = $this->getDatabase()->products()->where('id', $overdueProduct->product_id)->fetch();

			$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id', $overdueProduct->product_id)->fetch();
			if (!$alreadyExistingEntry)
			{
				$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow([
					'product_id' => $overdueProduct->product_id,
					'amount' => 1,
					'shopping_list_id' => $listId,
					'qu_id' => $product->qu_id_purchase
				]);
				$shoppinglistRow->save();
			}
		}
	}

	public function AddExpiredProductsToShoppingList($listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$expiredProducts = $this->GetExpiredProducts();
		foreach ($expiredProducts as $expiredProduct)
		{
			$product = $this->getDatabase()->products()->where('id', $expiredProduct->product_id)->fetch();

			$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id', $expiredProduct->product_id)->fetch();
			if (!$alreadyExistingEntry)
			{
				$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow([
					'product_id' => $expiredProduct->product_id,
					'amount' => 1,
					'shopping_list_id' => $listId,
					'qu_id' => $product->qu_id_purchase
				]);
				$shoppinglistRow->save();
			}
		}
	}

	public function AddProduct(int $productId, float $amount, $bestBeforeDate, $transactionType, $purchasedDate, $price, $locationId = null, $shoppingLocationId = null, &$transactionId = null, $stockLabelType = 0, $addExactAmount = false, $note = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}


		if ($amount <= 0)
		{
			throw new \Exception('Amount can\'t be <= 0');
		}

		$productDetails = (object)$this->GetProductDetails($productId);

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// The amount to be posted needs to be the given amount - stock amount - tare weight
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			if ($addExactAmount)
			{
				$amount = $productDetails->stock_amount + $productDetails->product->tare_weight + $amount;
			}

			if ($amount <= $productDetails->product->tare_weight + $productDetails->stock_amount)
			{
				throw new \Exception('The amount cannot be lower or equal than the defined tare weight + current stock amount');
			}

			$amount = $amount - $productDetails->stock_amount - $productDetails->product->tare_weight;
		}

		//Set the default due date, if none is supplied
		if ($bestBeforeDate == null)
		{
			if ($locationId !== null && !$this->LocationExists($locationId))
			{
				throw new \Exception('Location does not exist');
			}
			else
			{
				$location = $this->getDatabase()->locations()->where('id', $locationId)->fetch();
			}

			if (GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING && $locationId !== null && $location->is_freezer == 1 && $productDetails->product->default_best_before_days_after_freezing >= -1)
			{
				if ($productDetails->product->default_best_before_days_after_freezing == -1)
				{
					$bestBeforeDate = date('2999-12-31');
				}
				else
				{
					$bestBeforeDate = date('Y-m-d', strtotime('+' . $productDetails->product->default_best_before_days_after_freezing . ' days'));
				}
			}
			elseif ($productDetails->product->default_best_before_days == -1)
			{
				$bestBeforeDate = date('2999-12-31');
			}
			elseif ($productDetails->product->default_best_before_days > 0)
			{
				$bestBeforeDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $productDetails->product->default_best_before_days . ' days'));
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

			if ($stockLabelType == 2)
			{
				// Label per unit => single stock entry per unit

				for ($i = 1; $i <= $amount; $i++)
				{
					$stockId = uniqid('x');
					$logRow = $this->getDatabase()->stock_log()->createRow([
						'product_id' => $productId,
						'amount' => 1,
						'best_before_date' => $bestBeforeDate,
						'purchased_date' => $purchasedDate,
						'stock_id' => $stockId,
						'transaction_type' => $transactionType,
						'price' => $price,
						'location_id' => $locationId,
						'transaction_id' => $transactionId,
						'shopping_location_id' => $shoppingLocationId,
						'user_id' => GROCY_USER_ID,
						'note' => $note
					]);
					$logRow->save();

					$stockRow = $this->getDatabase()->stock()->createRow([
						'product_id' => $productId,
						'amount' => 1,
						'best_before_date' => $bestBeforeDate,
						'purchased_date' => $purchasedDate,
						'stock_id' => $stockId,
						'price' => $price,
						'location_id' => $locationId,
						'shopping_location_id' => $shoppingLocationId,
						'note' => $note
					]);
					$stockRow->save();

					if (GROCY_FEATURE_FLAG_LABEL_PRINTER && GROCY_LABEL_PRINTER_RUN_SERVER)
					{
						$webhookData = array_merge([
							'product' => $productDetails->product->name,
							'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $productId, [$stockId])),
						], GROCY_LABEL_PRINTER_PARAMS);

						if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
						{
							$webhookData['due_date'] = $this->getLocalizationService()->__t('DD') . ': ' . $bestBeforeDate;
						}

						$runner = new WebhookRunner();
						$runner->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
					}
				}
			}
			else
			{
				// No or single label => one stock entry

				$stockId = uniqid();
				$logRow = $this->getDatabase()->stock_log()->createRow([
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
					'user_id' => GROCY_USER_ID,
					'note' => $note
				]);
				$logRow->save();

				$stockRow = $this->getDatabase()->stock()->createRow([
					'product_id' => $productId,
					'amount' => $amount,
					'best_before_date' => $bestBeforeDate,
					'purchased_date' => $purchasedDate,
					'stock_id' => $stockId,
					'price' => $price,
					'location_id' => $locationId,
					'shopping_location_id' => $shoppingLocationId,
					'note' => $note
				]);
				$stockRow->save();

				if ($stockLabelType == 1 && GROCY_FEATURE_FLAG_LABEL_PRINTER && GROCY_LABEL_PRINTER_RUN_SERVER)
				{
					$webhookData = array_merge([
						'product' => $productDetails->product->name,
						'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $productId, [$stockId])),
					], GROCY_LABEL_PRINTER_PARAMS);

					if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					{
						$webhookData['due_date'] = $this->getLocalizationService()->__t('DD') . ': ' . $bestBeforeDate;
					}

					$runner = new WebhookRunner();
					$runner->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
				}
			}

			$this->CompactStockEntries($productId);

			return $transactionId;
		}
		else
		{
			throw new \Exception("Transaction type $transactionType is not valid (StockService.AddProduct)");
		}
	}

	public function AddProductToShoppingList($productId, $amount = 1, $quId = -1, $note = null, $listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}

		if ($quId == -1)
		{
			$quId = $this->getDatabase()->products($productId)->qu_id_purchase;
		}

		$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id = :1 AND shopping_list_id = :2', $productId, $listId)->fetch();
		if ($alreadyExistingEntry)
		{
			// Update
			$alreadyExistingEntry->update([
				'amount' => ($alreadyExistingEntry->amount + $amount),
				'shopping_list_id' => $listId,
				'note' => $note
			]);
		}
		else
		{
			// Insert
			$shoppinglistRow = $this->getDatabase()->shopping_list()->createRow([
				'product_id' => $productId,
				'amount' => $amount,
				'qu_id' => $quId,
				'shopping_list_id' => $listId,
				'note' => $note
			]);
			$shoppinglistRow->save();
		}
	}

	public function ClearShoppingList($listId = 1, $doneOnly = false)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		if ($doneOnly)
		{
			$this->getDatabase()->shopping_list()->where('shopping_list_id = :1 AND IFNULL(done, 0) = 1', $listId)->delete();
		}
		else
		{
			$this->getDatabase()->shopping_list()->where('shopping_list_id = :1', $listId)->delete();
		}
	}

	public function ConsumeProduct(int $productId, float $amount, bool $spoiled, $transactionType, $specificStockEntryId = 'default', $recipeId = null, $locationId = null, &$transactionId = null, $allowSubproductSubstitution = false, $consumeExactAmount = false)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}

		if ($amount <= 0)
		{
			throw new \Exception('Amount can\'t be <= 0');
		}

		if ($locationId !== null && !$this->LocationExists($locationId))
		{
			throw new \Exception('Location does not exist');
		}

		$productDetails = (object)$this->GetProductDetails($productId);

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// The amount to be posted needs to be the absolute value of the given amount - stock amount - tare weight
		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			if ($consumeExactAmount)
			{
				$amount = $productDetails->stock_amount + $productDetails->product->tare_weight - $amount;
			}
			if ($amount < $productDetails->product->tare_weight)
			{
				throw new \Exception('The amount cannot be lower than the defined tare weight');
			}

			$amount = abs($amount - $productDetails->stock_amount - $productDetails->product->tare_weight);
		}

		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			if ($locationId === null)
			{
				// Consume from any location
				$potentialStockEntries = $this->GetProductStockEntries($productId, false, $allowSubproductSubstitution);
			}
			else
			{
				// Consume only from the supplied location
				$potentialStockEntries = $this->GetProductStockEntriesForLocation($productId, $locationId, false, $allowSubproductSubstitution);
			}

			if ($specificStockEntryId !== 'default')
			{
				$potentialStockEntries = FindAllObjectsInArrayByPropertyValue($potentialStockEntries, 'stock_id', $specificStockEntryId);
			}

			$productStockAmount = $productDetails->stock_amount_aggregated;
			if (round($amount, 2) > round($productStockAmount, 2))
			{
				throw new \Exception('Amount to be consumed cannot be > current stock amount (if supplied, at the desired location)');
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

				if ($allowSubproductSubstitution && $stockEntry->product_id != $productId)
				{
					// A sub product will be used -> use QU conversions
					$subProduct = $this->getDatabase()->products($stockEntry->product_id);
					$conversion = $this->getDatabase()->cache__quantity_unit_conversions_resolved()->where('product_id = :1 AND from_qu_id = :2 AND to_qu_id = :3', $stockEntry->product_id, $productDetails->product->qu_id_stock, $subProduct->qu_id_stock)->fetch();
					if ($conversion != null)
					{
						$amount = $amount * $conversion->factor;
					}
				}

				if ($amount >= $stockEntry->amount)
				{
					// Take the whole stock entry
					$logRow = $this->getDatabase()->stock_log()->createRow([
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
						'transaction_id' => $transactionId,
						'user_id' => GROCY_USER_ID,
						'location_id' => $stockEntry->location_id,
						'note' => $stockEntry->note
					]);
					$logRow->save();

					$stockEntry->delete();

					$amount -= $stockEntry->amount;

					if ($allowSubproductSubstitution && $stockEntry->product_id != $productId && $conversion != null)
					{
						// A sub product with QU conversions was used
						// => Convert the rest amount back to be based on the original (parent) product for the next round
						$amount = $amount / $conversion->factor;
					}
				}
				else
				{
					// Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
					$restStockAmount = $stockEntry->amount - $amount;

					$logRow = $this->getDatabase()->stock_log()->createRow([
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
						'transaction_id' => $transactionId,
						'user_id' => GROCY_USER_ID,
						'location_id' => $stockEntry->location_id,
						'note' => $stockEntry->note
					]);
					$logRow->save();

					$stockEntry->update([
						'amount' => $restStockAmount
					]);

					$amount = 0;
				}
			}

			if (boolval($this->getUsersService()->GetUserSetting(GROCY_USER_ID, 'shopping_list_auto_add_below_min_stock_amount')))
			{
				$this->AddMissingProductsToShoppingList($this->getUsersService()->GetUserSetting(GROCY_USER_ID, 'shopping_list_auto_add_below_min_stock_amount_list_id'));
			}

			return $transactionId;
		}
		else
		{
			throw new \Exception("Transaction type $transactionType is not valid (StockService.ConsumeProduct)");
		}
	}

	public function EditStockEntry(int $stockRowId, float $amount, $bestBeforeDate, $locationId, $shoppingLocationId, $price, $open, $purchasedDate, $note = null)
	{
		$stockRow = $this->getDatabase()->stock()->where('id = :1', $stockRowId)->fetch();
		if ($stockRow === null)
		{
			throw new \Exception('Stock does not exist');
		}

		$correlationId = uniqid();
		$transactionId = uniqid();
		$logOldRowForStockUpdate = $this->getDatabase()->stock_log()->createRow([
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
			'stock_row_id' => $stockRow->id,
			'user_id' => GROCY_USER_ID,
			'note' => $stockRow->note
		]);
		$logOldRowForStockUpdate->save();

		$openedDate = $stockRow->opened_date;
		if (boolval($open) && $openedDate == null)
		{
			$openedDate = date('Y-m-d');
		}
		elseif (!boolval($open))
		{
			$openedDate = null;
		}

		$stockRow->update([
			'amount' => $amount,
			'price' => $price,
			'best_before_date' => $bestBeforeDate,
			'location_id' => $locationId,
			'shopping_location_id' => $shoppingLocationId,
			'opened_date' => $openedDate,
			'open' => BoolToInt($open),
			'purchased_date' => $purchasedDate,
			'note' => $note
		]);

		$logNewRowForStockUpdate = $this->getDatabase()->stock_log()->createRow([
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
			'stock_row_id' => $stockRow->id,
			'user_id' => GROCY_USER_ID,
			'note' => $stockRow->note
		]);
		$logNewRowForStockUpdate->save();

		$this->CompactStockEntries($stockRow->product_id);

		return $transactionId;
	}

	public function GetExternalBarcodeLookupPluginName()
	{
		$plugin = $this->LoadExternalBarcodeLookupPlugin();
		return $plugin::PLUGIN_NAME;
	}

	public function ExternalBarcodeLookup($barcode, $addFoundProduct)
	{
		$plugin = $this->LoadExternalBarcodeLookupPlugin();
		$pluginOutput = $plugin->Lookup($barcode);

		if ($pluginOutput !== null)
		{
			// Lookup was successful
			if ($addFoundProduct === true)
			{
				if ($this->getDatabase()->products()->where('name = :1', $pluginOutput['name'])->fetch() !== null)
				{
					throw new \Exception('Product "' . $pluginOutput['name'] . '" already exists');
				}

				// Add product to database and include new product id in output
				$productData = $pluginOutput;
				unset($productData['__barcode'], $productData['__qu_factor_purchase_to_stock'], $productData['__image_url']); // Virtual lookup plugin properties

				// Download and save image if provided
				if (isset($pluginOutput['__image_url']) && !empty($pluginOutput['__image_url']))
				{
					try
					{
						$webClient = new Client();
						$response = $webClient->request('GET', $pluginOutput['__image_url'], ['headers' => ['User-Agent' => 'Grocy/' . $this->getApplicationService()->GetInstalledVersion()->Version . ' (https://grocy.info)']]);
						$fileName = $pluginOutput['__barcode'] . '.' . pathinfo($pluginOutput['__image_url'], PATHINFO_EXTENSION);
						file_put_contents($this->getFilesService()->GetFilePath('productpictures', $fileName), $response->getBody());
						$productData['picture_file_name'] = $fileName;
					}
					catch (\Exception)
					{
						// Ignore
					}
				}

				$newProductRow = $this->getDatabase()->products()->createRow($productData);
				$newProductRow->save();

				$this->getDatabase()->product_barcodes()->createRow([
					'product_id' => $newProductRow->id,
					'barcode' => $pluginOutput['__barcode']
				])->save();

				if ($pluginOutput['qu_id_stock'] != $pluginOutput['qu_id_purchase'])
				{
					$this->getDatabase()->quantity_unit_conversions()->createRow([
						'product_id' => $newProductRow->id,
						'from_qu_id' => $pluginOutput['qu_id_purchase'],
						'to_qu_id' => $pluginOutput['qu_id_stock'],
						'factor' => $pluginOutput['__qu_factor_purchase_to_stock'],
					])->save();
				}

				$pluginOutput['id'] = $newProductRow->id;
			}
		}

		return $pluginOutput;
	}

	public function GetCurrentStock($customWhere = '')
	{
		$sql = 'SELECT * FROM stock_current ' . $customWhere;
		$currentStockMapped = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_OBJ);
		$relevantProducts = $this->getDatabase()->products()->where('id IN (SELECT product_id FROM (' . $sql . ') x)');

		foreach ($relevantProducts as $product)
		{
			$currentStockMapped[$product->id][0]->product_id = $product->id;
			$currentStockMapped[$product->id][0]->product = $product;
		}

		return array_column($currentStockMapped, 0);
	}

	public function GetCurrentStockLocationContent($includeOutOfStockProductsAtTheDefaultLocation = false)
	{
		$leftJoin = '';
		if ($includeOutOfStockProductsAtTheDefaultLocation)
		{
			$leftJoin = 'LEFT';
		}

		$sql = 'SELECT IFNULL(sclc.location_id, p.location_id) AS location_id, p.id AS product_id, IFNULL(sclc.amount, 0) AS amount, IFNULL(sclc.amount_opened, 0) AS amount_opened FROM products p ' . $leftJoin . ' JOIN stock_current_location_content sclc ON sclc.product_id = p.id WHERE p.active = 1 ORDER BY p.name';
		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetCurrentStockLocations()
	{
		$sql = 'SELECT * FROM stock_current_locations';
		return $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetDueProducts(int $days = 5, bool $excludeOverdue = false)
	{
		if ($excludeOverdue)
		{
			return $this->GetCurrentStock("WHERE best_before_date <= date('now', '$days days') AND best_before_date >= date()");
		}
		else
		{
			return $this->GetCurrentStock("WHERE best_before_date <= date('now', '$days days')");
		}
	}

	public function GetExpiredProducts()
	{
		return $this->GetCurrentStock('WHERE best_before_date < date() AND due_type = 2');
	}

	public function GetMissingProducts()
	{
		$missingProductsResponse = $this->getDatabaseService()->ExecuteDbQuery('SELECT * FROM stock_missing_products')->fetchAll(\PDO::FETCH_OBJ);

		$relevantProducts = $this->getDatabase()->products()->where('id IN (SELECT id FROM stock_missing_products)');
		foreach ($relevantProducts as $product)
		{
			FindObjectInArrayByPropertyValue($missingProductsResponse, 'id', $product->id)->product = $product;
		}

		return $missingProductsResponse;
	}

	public function GetProductDetails(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}

		$stockCurrentRow = FindObjectInArrayByPropertyValue($this->GetCurrentStock(), 'product_id', $productId);
		if ($stockCurrentRow == null)
		{
			$stockCurrentRow = new \stdClass();
			$stockCurrentRow->amount = 0;
			$stockCurrentRow->value = 0;
			$stockCurrentRow->amount_opened = 0;
			$stockCurrentRow->amount_aggregated = 0;
			$stockCurrentRow->amount_opened_aggregated = 0;
			$stockCurrentRow->is_aggregated_amount = 0;
		}

		$detailsRow = $this->getDatabase()->uihelper_product_details()->where('id', $productId)->fetch();
		$product = $this->getDatabase()->products($productId);
		$productBarcodes = $this->getDatabase()->product_barcodes()->where('product_id', $productId)->fetchAll();
		$quPurchase = $this->getDatabase()->quantity_units($product->qu_id_purchase);
		$quStock = $this->getDatabase()->quantity_units($product->qu_id_stock);
		$quConsume = $this->getDatabase()->quantity_units($product->qu_id_consume);
		$quPrice = $this->getDatabase()->quantity_units($product->qu_id_price);
		$location = $this->getDatabase()->locations($product->location_id);

		$defaultConsumeLocation = null;
		if (!empty($product->default_consume_location_id))
		{
			$defaultConsumeLocation = $this->getDatabase()->locations($product->default_consume_location_id);
		}

		return [
			'product' => $product,
			'product_barcodes' => $productBarcodes,
			'last_purchased' => $detailsRow->last_purchased_date,
			'last_used' => $detailsRow->last_used_date,
			'stock_amount' => $stockCurrentRow->amount,
			'stock_value' => $stockCurrentRow->value,
			'stock_amount_opened' => $stockCurrentRow->amount_opened,
			'stock_amount_aggregated' => $stockCurrentRow->amount_aggregated,
			'stock_amount_opened_aggregated' => $stockCurrentRow->amount_opened_aggregated,
			'quantity_unit_stock' => $quStock,
			'default_quantity_unit_purchase' => $quPurchase,
			'default_quantity_unit_consume' => $quConsume,
			'quantity_unit_price' => $quPrice,
			'last_price' => $detailsRow->last_purchased_price,
			'avg_price' => $detailsRow->average_price,
			'oldest_price' => $detailsRow->current_price, // Deprecated
			'current_price' => $detailsRow->current_price,
			'last_shopping_location_id' => $detailsRow->last_purchased_shopping_location_id,
			'default_shopping_location_id' => $product->shopping_location_id,
			'next_due_date' => $detailsRow->next_due_date,
			'location' => $location,
			'average_shelf_life_days' => $detailsRow->average_shelf_life_days,
			'spoil_rate_percent' => $detailsRow->spoil_rate,
			'is_aggregated_amount' => $stockCurrentRow->is_aggregated_amount,
			'has_childs' => boolval($detailsRow->has_childs),
			'default_consume_location' => $defaultConsumeLocation,
			'qu_conversion_factor_purchase_to_stock' => $detailsRow->qu_factor_purchase_to_stock,
			'qu_conversion_factor_price_to_stock' => $detailsRow->qu_factor_price_to_stock
		];
	}

	public function GetProductIdFromBarcode(string $barcode)
	{
		// first, try to parse this as a product Grocycode
		if (Grocycode::Validate($barcode))
		{
			$gc = new Grocycode($barcode);
			if ($gc->GetType() != Grocycode::PRODUCT)
			{
				throw new \Exception('Invalid Grocycode');
			}
			return $gc->GetId();
		}

		$potentialProduct = $this->getDatabase()->product_barcodes()->where('barcode = :1 COLLATE NOCASE', $barcode)->fetch();
		if ($potentialProduct === null)
		{
			throw new \Exception("No product with barcode $barcode found");
		}

		return $potentialProduct->product_id;
	}

	public function GetProductPriceHistory(int $productId)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}

		$returnData = [];
		$shoppingLocations = $this->getDatabase()->shopping_locations();

		$rows = $this->getDatabase()->products_price_history()->where('product_id = :1', $productId)->orderBy('purchased_date', 'DESC');
		foreach ($rows as $row)
		{
			$returnData[] = [
				'date' => $row->purchased_date,
				'price' => $row->price,
				'shopping_location' => FindObjectInArrayByPropertyValue($shoppingLocations, 'id', $row->shopping_location_id)
			];
		}

		return $returnData;
	}

	public function GetProductStockEntries(int $productId, $excludeOpened = false, $allowSubproductSubstitution = false)
	{
		$sqlWhereProductId = 'product_id = ' . $productId;
		if ($allowSubproductSubstitution)
		{
			$sqlWhereProductId = '(product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = ' . $productId . ') OR product_id = ' . $productId . ')';
		}

		$sqlWhereAndOpen = 'AND open IN (0, 1)';
		if ($excludeOpened)
		{
			$sqlWhereAndOpen = 'AND open = 0';
		}

		return $this->getDatabase()->stock_next_use()->where($sqlWhereProductId . ' ' . $sqlWhereAndOpen);
	}

	public function GetLocationStockEntries($locationId)
	{
		if (!$this->LocationExists($locationId))
		{
			throw new \Exception('Location does not exist');
		}

		return $this->getDatabase()->stock()->where('location_id', $locationId);
	}

	public function GetProductStockEntriesForLocation($productId, $locationId, $excludeOpened = false, $allowSubproductSubstitution = false)
	{
		$stockEntries = $this->GetProductStockEntries($productId, $excludeOpened, $allowSubproductSubstitution);
		return FindAllObjectsInArrayByPropertyValue($stockEntries, 'location_id', $locationId);
	}

	public function GetProductStockLocations(int $productId, $allowSubproductSubstitution = false)
	{
		$sqlWhereProductId = 'product_id = ' . $productId;
		if ($allowSubproductSubstitution)
		{
			$sqlWhereProductId = '(product_id IN (SELECT sub_product_id FROM products_resolved WHERE parent_product_id = ' . $productId . ') OR product_id = ' . $productId . ')';
		}

		return $this->getDatabase()->stock_current_locations()->where($sqlWhereProductId);
	}

	public function GetStockEntry($entryId)
	{
		return $this->getDatabase()->stock()->where('id', $entryId)->fetch();
	}

	public function InventoryProduct(int $productId, float $newAmount, $bestBeforeDate, $locationId = null, $price = null, $shoppingLocationId = null, $purchasedDate = null, $stockLabelType = 0, $note = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
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

		if ($purchasedDate == null)
		{
			$purchasedDate = date('Y-m-d');
		}

		// Tare weight handling
		// The given amount is the new total amount including the container weight (gross)
		// So assume that the amount in stock is the amount also including the container weight
		$containerWeight = 0;

		if ($productDetails->product->enable_tare_weight_handling == 1)
		{
			$containerWeight = $productDetails->product->tare_weight;
		}

		if ($newAmount == $productDetails->stock_amount + $containerWeight)
		{
			throw new \Exception('The new amount cannot equal the current stock amount');
		}
		elseif ($newAmount > $productDetails->stock_amount + $containerWeight)
		{
			$bookingAmount = $newAmount - $productDetails->stock_amount;

			if ($productDetails->product->enable_tare_weight_handling == 1)
			{
				$bookingAmount = $newAmount;
			}

			return $this->AddProduct($productId, $bookingAmount, $bestBeforeDate, self::TRANSACTION_TYPE_INVENTORY_CORRECTION, $purchasedDate, $price, $locationId, $shoppingLocationId, $unusedTransactionId, $stockLabelType, false, $note);
		}
		elseif ($newAmount < $productDetails->stock_amount + $containerWeight)
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

	public function OpenProduct(int $productId, float $amount, $specificStockEntryId = 'default', &$transactionId = null, $allowSubproductSubstitution = false)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
		}

		$product = $this->getDatabase()->products($productId);

		if ($product->disable_open == 1)
		{
			throw new \Exception('Product can\'t be opened');
		}

		$productDetails = (object)$this->GetProductDetails($productId);
		$productStockAmountUnopened = $productDetails->stock_amount_aggregated - $productDetails->stock_amount_opened_aggregated;
		$potentialStockEntries = $this->GetProductStockEntries($productId, true, $allowSubproductSubstitution);

		if ($product->enable_tare_weight_handling == 1)
		{
			throw new \Exception('Opening tare weight handling enabled products is not supported');
		}

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
				$newBestBeforeDate = date('Y-m-d', strtotime('+' . $product->default_best_before_days_after_open . ' days'));

				// The new due date should be never > the original due date
				if (strtotime($newBestBeforeDate) > strtotime($stockEntry->best_before_date))
				{
					$newBestBeforeDate = $stockEntry->best_before_date;
				}

				if (GROCY_FEATURE_FLAG_LABEL_PRINTER && GROCY_LABEL_PRINTER_RUN_SERVER && $productDetails->product->auto_reprint_stock_label == 1 && $newBestBeforeDate != $stockEntry->best_before_date)
				{
					$webhookData = array_merge([
						'product' => $productDetails->product->name,
						'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $productId, [$stockEntry->stock_id])),
					], GROCY_LABEL_PRINTER_PARAMS);

					if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					{
						$webhookData['due_date'] = $this->getLocalizationService()->__t('DD') . ': ' . $newBestBeforeDate;
					}

					$runner = new WebhookRunner();
					$runner->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
				}
			}

			if ($allowSubproductSubstitution && $stockEntry->product_id != $productId)
			{
				// A sub product will be used -> use QU conversions
				$subProduct = $this->getDatabase()->products($stockEntry->product_id);
				$conversion = $this->getDatabase()->cache__quantity_unit_conversions_resolved()->where('product_id = :1 AND from_qu_id = :2 AND to_qu_id = :3', $stockEntry->product_id, $product->qu_id_stock, $subProduct->qu_id_stock)->fetch();
				if ($conversion != null)
				{
					$amount = $amount * $conversion->factor;
				}
			}

			if ($amount >= $stockEntry->amount)
			{
				// Mark the whole stock entry as opened
				$logRow = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'location_id' => $stockEntry->location_id,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d'),
					'transaction_id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRow->save();

				$stockEntry->update([
					'open' => 1,
					'opened_date' => date('Y-m-d'),
					'best_before_date' => $newBestBeforeDate
				]);

				$amount -= $stockEntry->amount;
			}
			else
			{
				// Stock entry amount is > than needed amount -> split the stock entry
				$restStockAmount = $stockEntry->amount - $amount;

				$newStockRow = $this->getDatabase()->stock()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $restStockAmount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'location_id' => $stockEntry->location_id,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'stock_id' => uniqid(),
					'price' => $stockEntry->price,
					'note' => $stockEntry->note
				]);
				$newStockRow->save();

				$logRow = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'location_id' => $stockEntry->location_id,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'transaction_type' => self::TRANSACTION_TYPE_PRODUCT_OPENED,
					'price' => $stockEntry->price,
					'opened_date' => date('Y-m-d'),
					'transaction_id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRow->save();

				$stockEntry->update([
					'amount' => $amount,
					'open' => 1,
					'opened_date' => date('Y-m-d'),
					'best_before_date' => $newBestBeforeDate
				]);

				$amount = 0;
			}

			if ($product->move_on_open == 1)
			{
				$locationIdTo = $product->default_consume_location_id;
				if (!empty($locationIdTo) && $locationIdTo != $stockEntry->location_id)
				{
					$this->TransferProduct($stockEntry->product_id, $stockEntry->amount, $stockEntry->location_id, $locationIdTo, $stockEntry->stock_id, $transactionId);
				}
			}
		}

		if (boolval($this->getUsersService()->GetUserSetting(GROCY_USER_ID, 'shopping_list_auto_add_below_min_stock_amount')))
		{
			$this->AddMissingProductsToShoppingList($this->getUsersService()->GetUserSetting(GROCY_USER_ID, 'shopping_list_auto_add_below_min_stock_amount_list_id'));
		}

		return $transactionId;
	}

	public function RemoveProductFromShoppingList($productId, $amount = 1, $listId = 1)
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$productRow = $this->getDatabase()->shopping_list()->where('product_id = :1', $productId)->fetch();

		// If no entry was found with for this product, we return gracefully
		if ($productRow != null && !empty($productRow))
		{
			$decimals = $this->getUsersService()->GetUserSetting(GROCY_USER_ID, 'stock_decimal_places_amounts');
			$newAmount = $productRow->amount - $amount;

			if ($newAmount < floatval('0.' . str_repeat('0', $decimals - ($decimals <= 0 ? 0 : 1)) . '1'))
			{
				$productRow->delete();
			}
			else
			{
				$productRow->update(['amount' => $newAmount]);
			}
		}
	}

	public function GetShoppinglistInPrintableStrings($listId = 1): array
	{
		if (!$this->ShoppingListExists($listId))
		{
			throw new \Exception('Shopping list does not exist');
		}

		$result_product = [];
		$result_quantity = [];
		$rowsShoppingListProducts = $this->getDatabase()->uihelper_shopping_list()->where('shopping_list_id = :1', $listId)->fetchAll();
		foreach ($rowsShoppingListProducts as $row)
		{
			$isValidProduct = ($row->product_id != null && $row->product_id != '');
			if ($isValidProduct)
			{
				$product = $this->getDatabase()->products()->where('id = :1', $row->product_id)->fetch();
				$conversion = $this->getDatabase()->cache__quantity_unit_conversions_resolved()->where('product_id = :1 AND from_qu_id = :2 AND to_qu_id = :3', $product->id, $product->qu_id_stock, $row->qu_id)->fetch();

				$factor = 1.0;
				if ($conversion != null)
				{
					$factor = $conversion->factor;
				}

				$amount = round($row->amount * $factor);
				$note = '';

				if (GROCY_TPRINTER_PRINT_NOTES)
				{
					if ($row->note != '')
					{
						$note = ' (' . $row->note . ')';
					}
				}
			}

			if (GROCY_TPRINTER_PRINT_QUANTITY_NAME && $isValidProduct)
			{
				$quantityname = $row->qu_name;
				if ($amount > 1)
				{
					$quantityname = $row->qu_name_plural;
				}

				array_push($result_quantity, $amount . ' ' . $quantityname);
				array_push($result_product, $row->product_name . $note);
			}
			else
			{
				if ($isValidProduct)
				{
					array_push($result_quantity, $amount);
					array_push($result_product, $row->product_name . $note);
				}
				else
				{
					array_push($result_quantity, round($row->amount));
					array_push($result_product, $row->note);
				}
			}
		}

		//Add padding to look nicer
		$maxlength = 1;
		foreach ($result_quantity as $quantity)
		{
			if (strlen($quantity) > $maxlength)
			{
				$maxlength = strlen($quantity);
			}
		}

		$result = [];
		$length = count($result_quantity);
		for ($i = 0; $i < $length; $i++)
		{
			$quantity = str_pad($result_quantity[$i], $maxlength);
			array_push($result, $quantity . '  ' . $result_product[$i]);
		}

		return $result;
	}

	public function TransferProduct(int $productId, float $amount, int $locationIdFrom, int $locationIdTo, $specificStockEntryId = 'default', &$transactionId = null)
	{
		if (!$this->ProductExists($productId))
		{
			throw new \Exception('Product does not exist or is inactive');
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
			// Hard fail for now, as we not yet support transferring tare weight enabled products
			throw new \Exception('Transferring tare weight enabled products is not yet possible');
			if ($amount < $productDetails->product->tare_weight)
			{
				throw new \Exception('The amount cannot be lower than the defined tare weight');
			}

			$amount = abs($amount - $productDetails->stock_amount - $productDetails->product->tare_weight);
		}

		$productStockAmountAtFromLocation = $this->getDatabase()->stock()->where('product_id = :1 AND location_id = :2', $productId, $locationIdFrom)->sum('amount');
		$potentialStockEntriesAtFromLocation = $this->GetProductStockEntriesForLocation($productId, $locationIdFrom);

		if ($amount > $productStockAmountAtFromLocation)
		{
			throw new \Exception('Amount to be transferred cannot be > current stock amount at the source location');
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
				if ($locationFrom->is_freezer == 0 && $locationTo->is_freezer == 1 && ($productDetails->product->default_best_before_days_after_freezing > 0 || $productDetails->product->default_best_before_days_after_freezing == -1))
				{
					if ($productDetails->product->default_best_before_days_after_freezing == -1)
					{
						$newBestBeforeDate = date('2999-12-31');
					}
					else
					{
						$newBestBeforeDate = date('Y-m-d', strtotime('+' . $productDetails->product->default_best_before_days_after_freezing . ' days'));
					}
				}

				// Product was moved from a freezer to non-freezer location -> thaw
				if ($locationFrom->is_freezer == 1 && $locationTo->is_freezer == 0 && $productDetails->product->default_best_before_days_after_thawing > 0)
				{
					$newBestBeforeDate = date('Y-m-d', strtotime('+' . $productDetails->product->default_best_before_days_after_thawing . ' days'));
				}

				if (GROCY_FEATURE_FLAG_LABEL_PRINTER && GROCY_LABEL_PRINTER_RUN_SERVER && $productDetails->product->auto_reprint_stock_label == 1 && $stockEntry->best_before_date != $newBestBeforeDate)
				{
					$webhookData = array_merge([
						'product' => $productDetails->product->name,
						'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $productId, [$stockEntry->stock_id])),
					], GROCY_LABEL_PRINTER_PARAMS);

					if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					{
						$webhookData['due_date'] = $this->getLocalizationService()->__t('DD') . ': ' . $newBestBeforeDate;
					}

					$runner = new WebhookRunner();
					$runner->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
				}
			}

			$correlationId = uniqid();
			if ($amount >= $stockEntry->amount)
			{
				// Take the whole stock entry
				$logRowForLocationFrom = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount * -1,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_FROM,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $stockEntry->location_id,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRowForLocationFrom->save();

				$logRowForLocationTo = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_TO,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $locationIdTo,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRowForLocationTo->save();

				$stockEntry->update([
					'location_id' => $locationIdTo,
					'best_before_date' => $newBestBeforeDate
				]);

				$amount -= $stockEntry->amount;
			}
			else
			{
				// Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
				$restStockAmount = $stockEntry->amount - $amount;

				$logRowForLocationFrom = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $amount * -1,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_FROM,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $stockEntry->location_id,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRowForLocationFrom->save();

				$logRowForLocationTo = $this->getDatabase()->stock_log()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'transaction_type' => self::TRANSACTION_TYPE_TRANSFER_TO,
					'price' => $stockEntry->price,
					'opened_date' => $stockEntry->opened_date,
					'location_id' => $locationIdTo,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'correlation_id' => $correlationId,
					'transaction_Id' => $transactionId,
					'user_id' => GROCY_USER_ID,
					'note' => $stockEntry->note
				]);
				$logRowForLocationTo->save();

				// This is the existing stock entry -> remains at the source location with the rest amount
				$stockEntry->update([
					'amount' => $restStockAmount
				]);

				// The transferred amount gets into a new stock entry
				$stockEntryNew = $this->getDatabase()->stock()->createRow([
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $newBestBeforeDate,
					'purchased_date' => $stockEntry->purchased_date,
					'stock_id' => $stockEntry->stock_id,
					'price' => $stockEntry->price,
					'location_id' => $locationIdTo,
					'shopping_location_id' => $stockEntry->shopping_location_id,
					'open' => $stockEntry->open,
					'opened_date' => $stockEntry->opened_date,
					'note' => $stockEntry->note
				]);
				$stockEntryNew->save();

				$amount = 0;
			}
		}

		return $transactionId;
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

		$hasSubsequentBookings = $this->getDatabase()->stock_log()->where('stock_id = :1 AND id != :2 AND (correlation_id IS NOT NULL OR correlation_id != :3) AND id > :2 AND undone = 0', $logRow->stock_id, $logRow->id, $logRow->correlation_id)->count() > 0;
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
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_CONSUME || ($logRow->transaction_type === self::TRANSACTION_TYPE_INVENTORY_CORRECTION && $logRow->amount < 0))
		{
			// Add corresponding amount back to stock
			$stockRow = $this->getDatabase()->stock()->createRow([
				'product_id' => $logRow->product_id,
				'amount' => $logRow->amount * -1,
				'best_before_date' => $logRow->best_before_date,
				'purchased_date' => $logRow->purchased_date,
				'stock_id' => $logRow->stock_id,
				'price' => $logRow->price,
				'opened_date' => $logRow->opened_date,
				'open' => $logRow->opened_date !== null,
				'location_id' => $logRow->location_id,
				'note' => $logRow->note
			]);
			$stockRow->save();

			// Update log entry
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
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
			}
			else
			{
				// Remove corresponding amount back to stock
				$stockRow->update([
					'amount' => $newAmount
				]);
			}

			// Update log entry
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_TRANSFER_FROM)
		{
			// Add corresponding amount back to stock
			$stockRow = $this->getDatabase()->stock()->where('stock_id = :1 AND location_id = :2', $logRow->stock_id, $logRow->location_id)->fetch();
			if ($stockRow === null)
			{
				$stockRow = $this->getDatabase()->stock()->createRow([
					'product_id' => $logRow->product_id,
					'amount' => $logRow->amount * -1,
					'best_before_date' => $logRow->best_before_date,
					'purchased_date' => $logRow->purchased_date,
					'stock_id' => $logRow->stock_id,
					'price' => $logRow->price,
					'opened_date' => $logRow->opened_date,
					'note' => $logRow->note
				]);
				$stockRow->save();
			}
			else
			{
				$stockRow->update([
					'amount' => $stockRow->amount - $logRow->amount
				]);
			}

			// Update log entry
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_PRODUCT_OPENED)
		{
			// Remove opened flag from corresponding stock entry
			$stockRows = $this->getDatabase()->stock()->where('stock_id = :1 AND amount = :2 AND purchased_date = :3', $logRow->stock_id, $logRow->amount, $logRow->purchased_date)->limit(1);
			$stockRows->update([
				'open' => 0,
				'opened_date' => null,
				'best_before_date' => $logRow->best_before_date // Is only relevant when the product has "Default due days after opened", but also doesn't hurt for other products
			]);

			// Update log entry
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
		}
		elseif ($logRow->transaction_type === self::TRANSACTION_TYPE_STOCK_EDIT_NEW)
		{
			// Update log entry, no action needed
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
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

			$stockRow->update([
				'amount' => $logRow->amount,
				'best_before_date' => $logRow->best_before_date,
				'purchased_date' => $logRow->purchased_date,
				'price' => $logRow->price,
				'location_id' => $logRow->location_id,
				'open' => $open,
				'opened_date' => $openedDate,
				'note' => $logRow->note
			]);

			// Update log entry
			$logRow->update([
				'undone' => 1,
				'undone_timestamp' => date('Y-m-d H:i:s')
			]);
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

	public function MergeProducts(int $productIdToKeep, int $productIdToRemove)
	{
		if (!$this->ProductExists($productIdToKeep))
		{
			throw new \Exception('$productIdToKeep does not exist or is inactive');
		}

		if (!$this->ProductExists($productIdToRemove))
		{
			throw new \Exception('$productIdToRemove does not exist or is inactive');
		}

		if ($productIdToKeep == $productIdToRemove)
		{
			throw new \Exception('$productIdToKeep cannot equal $productIdToRemove');
		}

		$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();
		try
		{
			$productToKeep = $this->getDatabase()->products($productIdToKeep);
			$productToRemove = $this->getDatabase()->products($productIdToRemove);
			$conversion = $this->getDatabase()->cache__quantity_unit_conversions_resolved()->where('product_id = :1 AND from_qu_id = :2 AND to_qu_id = :3', $productToRemove->id, $productToRemove->qu_id_stock, $productToKeep->qu_id_stock)->fetch();
			$factor = 1.0;
			if ($conversion != null)
			{
				$factor = $conversion->factor;
			}

			$this->getDatabaseService()->ExecuteDbStatement('UPDATE stock SET product_id = ' . $productIdToKeep . ', amount = amount * ' . $factor . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE stock_log SET product_id = ' . $productIdToKeep . ', amount = amount * ' . $factor . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE product_barcodes SET product_id = ' . $productIdToKeep . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE quantity_unit_conversions SET product_id = ' . $productIdToKeep . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE recipes_pos SET product_id = ' . $productIdToKeep . ', amount = amount * ' . $factor . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE recipes SET product_id = ' . $productIdToKeep . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE meal_plan SET product_id = ' . $productIdToKeep . ', product_amount = product_amount * ' . $factor . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('UPDATE shopping_list SET product_id = ' . $productIdToKeep . ', amount = amount * ' . $factor . ' WHERE product_id = ' . $productIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('DELETE FROM products WHERE id = ' . $productIdToRemove);
		}
		catch (\Exception $ex)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
			throw $ex;
		}
		$this->getDatabaseService()->GetDbConnectionRaw()->commit();
	}

	public function CompactStockEntries($productId = null)
	{
		if ($productId == null)
		{
			$splittedStockEntries = $this->getDatabase()->stock_splits();
		}
		else
		{
			$splittedStockEntries = $this->getDatabase()->stock_splits()->where('product_id = :1', $productId);
		}

		foreach ($splittedStockEntries as $splittedStockEntry)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();
			try
			{
				$stockIds = explode(',', $splittedStockEntry->stock_id_group);
				foreach ($stockIds as $stockId)
				{
					if ($stockId != $splittedStockEntry->stock_id_to_keep)
					{
						$this->getDatabaseService()->ExecuteDbStatement('UPDATE stock SET stock_id = \'' . $splittedStockEntry->stock_id_to_keep . '\' WHERE stock_id = \'' . $stockId . '\'');
						$this->getDatabaseService()->ExecuteDbStatement('UPDATE stock_log SET stock_id = \'' . $splittedStockEntry->stock_id_to_keep . '\' WHERE stock_id = \'' . $stockId . '\'');
					}
				}

				$stockEntryIds = explode(',', $splittedStockEntry->id_group);
				foreach ($stockEntryIds as $stockEntryId)
				{
					if ($stockEntryId != $splittedStockEntry->id_to_keep)
					{
						$this->getDatabaseService()->ExecuteDbStatement('DELETE FROM stock WHERE id = ' . $stockEntryId);
					}
					else
					{
						$this->getDatabaseService()->ExecuteDbStatement('UPDATE stock SET amount = ' . $splittedStockEntry->total_amount . ' WHERE id = ' . $splittedStockEntry->id_to_keep);
					}
				}
			}
			catch (\Exception $ex)
			{
				$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
				throw $ex;
			}
			$this->getDatabaseService()->GetDbConnectionRaw()->commit();
		}
	}

	private function LoadExternalBarcodeLookupPlugin()
	{
		$pluginName = defined('GROCY_STOCK_BARCODE_LOOKUP_PLUGIN') ? GROCY_STOCK_BARCODE_LOOKUP_PLUGIN : '';
		if (empty($pluginName))
		{
			throw new \Exception('No barcode lookup plugin defined');
		}

		// User plugins take precedence
		$standardPluginPath = __DIR__ . "/../plugins/$pluginName.php";
		$userPluginPath = GROCY_DATAPATH . "/plugins/$pluginName.php";
		if (file_exists($userPluginPath))
		{
			require_once $userPluginPath;
			return new $pluginName($this->getDatabase()->locations()->where('active = 1')->fetchAll(), $this->getDatabase()->quantity_units()->where('active = 1')->fetchAll(), $this->getUsersService()->GetUserSettings(GROCY_USER_ID));
		}
		elseif (file_exists($standardPluginPath))
		{
			require_once $standardPluginPath;
			return new $pluginName($this->getDatabase()->locations()->where('active = 1')->fetchAll(), $this->getDatabase()->quantity_units()->where('active = 1')->fetchAll(), $this->getUsersService()->GetUserSettings(GROCY_USER_ID));
		}
		else
		{
			throw new \Exception("Plugin $pluginName was not found");
		}
	}

	private function LocationExists($locationId)
	{
		$locationRow = $this->getDatabase()->locations()->where('id = :1', $locationId)->where('active = 1')->fetch();
		return $locationRow !== null;
	}

	private function ProductExists($productId)
	{
		$productRow = $this->getDatabase()->products()->where('id = :1 and active = 1', $productId)->fetch();
		return $productRow !== null;
	}

	private function ShoppingListExists($listId)
	{
		$shoppingListRow = $this->getDatabase()->shopping_lists()->where('id = :1', $listId)->fetch();
		return $shoppingListRow !== null;
	}
}
