<?php
namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Services\StockService;
use Grocy\Helpers\WebhookRunner;
use Grocy\Helpers\Grocycode;

class StockApiController extends BaseApiController
{
	public function AddMissingProductsToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_ADD);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->getStockService()->AddMissingProductsToShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddOverdueProductsToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_ADD);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->getStockService()->AddOverdueProductsToShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddExpiredProductsToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_ADD);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->getStockService()->AddExpiredProductsToShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddProduct(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_PURCHASE);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('amount', $requestBody))
			{
				throw new \Exception('An amount is required');
			}

			$bestBeforeDate = null;

			if (array_key_exists('best_before_date', $requestBody) && IsIsoDate($requestBody['best_before_date']))
			{
				$bestBeforeDate = $requestBody['best_before_date'];
			}

			$purchasedDate = date('Y-m-d');

			if (array_key_exists('purchased_date', $requestBody) && IsIsoDate($requestBody['purchased_date']))
			{
				$purchasedDate = $requestBody['purchased_date'];
			}

			$price = null;

			if (array_key_exists('price', $requestBody) && is_numeric($requestBody['price']))
			{
				$price = $requestBody['price'];
			}

			$locationId = null;

			if (array_key_exists('location_id', $requestBody) && is_numeric($requestBody['location_id']))
			{
				$locationId = $requestBody['location_id'];
			}

			$shoppingLocationId = null;

			if (array_key_exists('shopping_location_id', $requestBody) && is_numeric($requestBody['shopping_location_id']))
			{
				$shoppingLocationId = $requestBody['shopping_location_id'];
			}

			$transactionType = StockService::TRANSACTION_TYPE_PURCHASE;

			if (array_key_exists('transaction_type', $requestBody) && !empty($requestBody['transactiontype']))
			{
				$transactionType = $requestBody['transactiontype'];
			}
			$runPrinterWebhook = false;
			if (array_key_exists('print_stock_label', $requestBody) && intval($requestBody['print_stock_label']))
			{
				$runPrinterWebhook = intval($requestBody['print_stock_label']);
			}

			$transactionId = $this->getStockService()->AddProduct($args['productId'], $requestBody['amount'], $bestBeforeDate, $transactionType, $purchasedDate, $price, $locationId, $shoppingLocationId, $unusedTransactionId, $runPrinterWebhook);

			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddProductByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$args['productId'] = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->AddProduct($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddProductToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_ADD);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;
			$amount = 1;
			$productId = null;
			$note = null;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			if (array_key_exists('product_amount', $requestBody) && !empty($requestBody['product_amount']) && is_numeric($requestBody['product_amount']))
			{
				$amount = intval($requestBody['product_amount']);
			}

			if (array_key_exists('product_id', $requestBody) && !empty($requestBody['product_id']) && is_numeric($requestBody['product_id']))
			{
				$productId = intval($requestBody['product_id']);
			}

			if (array_key_exists('note', $requestBody) && !empty($requestBody['note']))
			{
				$note = $requestBody['note'];
			}

			if ($productId == null)
			{
				throw new \Exception('No product id was supplied');
			}

			$this->getStockService()->AddProductToShoppingList($productId, $amount, $note, $listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ClearShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_DELETE);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->getStockService()->ClearShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ConsumeProduct(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_CONSUME);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('amount', $requestBody))
			{
				throw new \Exception('An amount is required');
			}

			$spoiled = false;
			if (array_key_exists('spoiled', $requestBody))
			{
				$spoiled = $requestBody['spoiled'];
			}

			$transactionType = StockService::TRANSACTION_TYPE_CONSUME;
			if (array_key_exists('transaction_type', $requestBody) && !empty($requestBody['transactiontype']))
			{
				$transactionType = $requestBody['transactiontype'];
			}

			$specificStockEntryId = 'default';
			if (array_key_exists('stock_entry_id', $requestBody) && !empty($requestBody['stock_entry_id']))
			{
				$specificStockEntryId = $requestBody['stock_entry_id'];
			}

			$locationId = null;
			if (array_key_exists('location_id', $requestBody) && !empty($requestBody['location_id']) && is_numeric($requestBody['location_id']))
			{
				$locationId = $requestBody['location_id'];
			}

			$recipeId = null;
			if (array_key_exists('recipe_id', $requestBody) && is_numeric($requestBody['recipe_id']))
			{
				$recipeId = $requestBody['recipe_id'];
			}

			$consumeExact = false;
			if (array_key_exists('exact_amount', $requestBody))
			{
				$consumeExact = $requestBody['exact_amount'];
			}

			$allowSubproductSubstitution = false;
			if (array_key_exists('allow_subproduct_substitution', $requestBody))
			{
				$allowSubproductSubstitution = $requestBody['allow_subproduct_substitution'];
			}

			$transactionId = null;
			$transactionId = $this->getStockService()->ConsumeProduct($args['productId'], $requestBody['amount'], $spoiled, $transactionType, $specificStockEntryId, $recipeId, $locationId, $transactionId, $allowSubproductSubstitution, $consumeExact);
			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ConsumeProductByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$args['productId'] = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->ConsumeProduct($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CurrentStock(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getStockService()->GetCurrentStock());
	}

	public function CurrentVolatileStock(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$nextXDays = 5;

		if (isset($request->getQueryParams()['due_soon_days']) && !empty($request->getQueryParams()['due_soon_days']) && is_numeric($request->getQueryParams()['due_soon_days']))
		{
			$nextXDays = $request->getQueryParams()['due_soon_days'];
		}

		$dueProducts = $this->getStockService()->GetDueProducts($nextXDays, true);
		$overdueProducts = $this->getStockService()->GetDueProducts(-1);
		$expiredProducts = $this->getStockService()->GetExpiredProducts();
		$missingProducts = $this->getStockService()->GetMissingProducts();
		return $this->ApiResponse($response, [
			'due_products' => $dueProducts,
			'overdue_products' => $overdueProducts,
			'expired_products' => $expiredProducts,
			'missing_products' => $missingProducts
		]);
	}

	public function EditStockEntry(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_EDIT);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('amount', $requestBody))
			{
				throw new \Exception('An amount is required');
			}

			$bestBeforeDate = null;

			if (array_key_exists('best_before_date', $requestBody) && IsIsoDate($requestBody['best_before_date']))
			{
				$bestBeforeDate = $requestBody['best_before_date'];
			}

			$price = null;

			if (array_key_exists('price', $requestBody) && is_numeric($requestBody['price']))
			{
				$price = $requestBody['price'];
			}

			$locationId = null;

			if (array_key_exists('location_id', $requestBody) && is_numeric($requestBody['location_id']))
			{
				$locationId = $requestBody['location_id'];
			}

			$shoppingLocationId = null;

			if (array_key_exists('shopping_location_id', $requestBody) && is_numeric($requestBody['shopping_location_id']))
			{
				$shoppingLocationId = $requestBody['shopping_location_id'];
			}

			$transactionId = $this->getStockService()->EditStockEntry($args['entryId'], $requestBody['amount'], $bestBeforeDate, $locationId, $shoppingLocationId, $price, $requestBody['open'], $requestBody['purchased_date']);
			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ExternalBarcodeLookup(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		try
		{
			$addFoundProduct = false;

			if (isset($request->getQueryParams()['add']) && ($request->getQueryParams()['add'] === 'true' || $request->getQueryParams()['add'] === 1))
			{
				$addFoundProduct = true;
			}

			return $this->ApiResponse($response, $this->getStockService()->ExternalBarcodeLookup($args['barcode'], $addFoundProduct));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function InventoryProduct(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_INVENTORY);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('new_amount', $requestBody))
			{
				throw new \Exception('An new amount is required');
			}

			$bestBeforeDate = null;
			if (array_key_exists('best_before_date', $requestBody) && IsIsoDate($requestBody['best_before_date']))
			{
				$bestBeforeDate = $requestBody['best_before_date'];
			}

			$purchasedDate = null;
			if (array_key_exists('purchased_date', $requestBody) && IsIsoDate($requestBody['purchased_date']))
			{
				$purchasedDate = $requestBody['purchased_date'];
			}

			$locationId = null;
			if (array_key_exists('location_id', $requestBody) && is_numeric($requestBody['location_id']))
			{
				$locationId = $requestBody['location_id'];
			}

			$price = null;
			if (array_key_exists('price', $requestBody) && is_numeric($requestBody['price']))
			{
				$price = $requestBody['price'];
			}

			$shoppingLocationId = null;
			if (array_key_exists('shopping_location_id', $requestBody) && is_numeric($requestBody['shopping_location_id']))
			{
				$shoppingLocationId = $requestBody['shopping_location_id'];
			}

			$transactionId = $this->getStockService()->InventoryProduct($args['productId'], $requestBody['new_amount'], $bestBeforeDate, $locationId, $price, $shoppingLocationId, $purchasedDate);
			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function InventoryProductByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$args['productId'] = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->InventoryProduct($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function OpenProduct(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_OPEN);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('amount', $requestBody))
			{
				throw new \Exception('An amount is required');
			}

			$specificStockEntryId = 'default';
			if (array_key_exists('stock_entry_id', $requestBody) && !empty($requestBody['stock_entry_id']))
			{
				$specificStockEntryId = $requestBody['stock_entry_id'];
			}

			$allowSubproductSubstitution = false;
			if (array_key_exists('allow_subproduct_substitution', $requestBody))
			{
				$allowSubproductSubstitution = $requestBody['allow_subproduct_substitution'];
			}

			$transactionId = null;
			$transactionId = $this->getStockService()->OpenProduct($args['productId'], $requestBody['amount'], $specificStockEntryId, $transactionId, $allowSubproductSubstitution);
			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function OpenProductByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$args['productId'] = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->OpenProduct($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductDetails(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getStockService()->GetProductDetails($args['productId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductDetailsByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$productId = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->ApiResponse($response, $this->getStockService()->GetProductDetails($productId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductPriceHistory(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getStockService()->GetProductPriceHistory($args['productId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductStockEntries(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$allowSubproductSubstitution = false;
		if (isset($request->getQueryParams()['include_sub_products']) && filter_var($request->getQueryParams()['include_sub_products'], FILTER_VALIDATE_BOOLEAN) !== false)
		{
			$allowSubproductSubstitution = true;
		}

		return $this->FilteredApiResponse($response, $this->getStockService()->GetProductStockEntries($args['productId'], false, $allowSubproductSubstitution, true), $request->getQueryParams());
	}

	public function ProductStockLocations(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$allowSubproductSubstitution = false;
		if (isset($request->getQueryParams()['include_sub_products']) && filter_var($request->getQueryParams()['include_sub_products'], FILTER_VALIDATE_BOOLEAN) !== false)
		{
			$allowSubproductSubstitution = true;
		}

		return $this->FilteredApiResponse($response, $this->getStockService()->GetProductStockLocations($args['productId'], $allowSubproductSubstitution), $request->getQueryParams());
	}

	public function ProductPrintLabel(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$product = $this->getDatabase()->products()->where('id', $args['productId'])->fetch();

		$webhookData = array_merge([
			'product' => $product->name,
			'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $product->id)),
		], GROCY_LABEL_PRINTER_PARAMS);

		if (GROCY_LABEL_PRINTER_RUN_SERVER)
		{
			(new WebhookRunner())->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
		}

		return $this->ApiResponse($response, $webhookData);
	}

	public function StockEntryPrintLabel(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$stockEntry = $this->getDatabase()->stock()->where('id', $args['entryId'])->fetch();
		$product = $this->getDatabase()->products()->where('id', $stockEntry->product_id)->fetch();

		$webhookData = array_merge([
			'product' => $product->name,
			'grocycode' => (string)(new Grocycode(Grocycode::PRODUCT, $stockEntry->product_id, [$stockEntry->stock_id])),
		], GROCY_LABEL_PRINTER_PARAMS);

		if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		{
			$webhookData['duedate'] = $this->getLocalizationService()->__t('DD') . ': ' . $stockEntry->best_before_date;
		}

		if (GROCY_LABEL_PRINTER_RUN_SERVER)
		{
			(new WebhookRunner())->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
		}

		return $this->ApiResponse($response, $webhookData);
	}

	public function RemoveProductFromShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_DELETE);

		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$listId = 1;
			$amount = 1;
			$productId = null;

			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			if (array_key_exists('product_amount', $requestBody) && !empty($requestBody['product_amount']) && is_numeric($requestBody['product_amount']))
			{
				$amount = intval($requestBody['product_amount']);
			}

			if (array_key_exists('product_id', $requestBody) && !empty($requestBody['product_id']) && is_numeric($requestBody['product_id']))
			{
				$productId = intval($requestBody['product_id']);
			}

			if ($productId == null)
			{
				throw new \Exception('No product id was supplied');
			}

			$this->getStockService()->RemoveProductFromShoppingList($productId, $amount, $listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function StockBooking(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$stockLogRow = $this->getDatabase()->stock_log($args['bookingId']);

			if ($stockLogRow === null)
			{
				throw new \Exception('Stock booking does not exist');
			}

			return $this->ApiResponse($response, $stockLogRow);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function StockEntry(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getStockService()->GetStockEntry($args['entryId']));
	}

	public function StockTransactions(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$transactionRows = $this->getDatabase()->stock_log()->where('transaction_id = :1', $args['transactionId'])->fetchAll();
			if (count($transactionRows) === 0)
			{
				throw new \Exception('No transaction was found by the given transaction id');
			}

			return $this->ApiResponse($response, $transactionRows);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function TransferProduct(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_TRANSFER);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			if (!array_key_exists('amount', $requestBody))
			{
				throw new \Exception('An amount is required');
			}

			if (!array_key_exists('location_id_from', $requestBody))
			{
				throw new \Exception('A transfer from location is required');
			}

			if (!array_key_exists('location_id_to', $requestBody))
			{
				throw new \Exception('A transfer to location is required');
			}

			$specificStockEntryId = 'default';

			if (array_key_exists('stock_entry_id', $requestBody) && !empty($requestBody['stock_entry_id']))
			{
				$specificStockEntryId = $requestBody['stock_entry_id'];
			}

			$transactionId = $this->getStockService()->TransferProduct($args['productId'], $requestBody['amount'], $requestBody['location_id_from'], $requestBody['location_id_to'], $specificStockEntryId);
			$args['transactionId'] = $transactionId;
			return $this->StockTransactions($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function TransferProductByBarcode(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$args['productId'] = $this->getStockService()->GetProductIdFromBarcode($args['barcode']);
			return $this->TransferProduct($request, $response, $args);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoBooking(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_EDIT);

		try
		{
			$this->ApiResponse($response, $this->getStockService()->UndoBooking($args['bookingId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoTransaction(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_EDIT);

		try
		{
			$this->ApiResponse($response, $this->getStockService()->UndoTransaction($args['transactionId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function MergeProducts(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_STOCK_EDIT);

		try
		{
			if (filter_var($args['productIdToKeep'], FILTER_VALIDATE_INT) === false || filter_var($args['productIdToRemove'], FILTER_VALIDATE_INT) === false)
			{
				throw new \Exception('Provided {productIdToKeep} or {productIdToRemove} is not a valid integer');
			}

			$this->ApiResponse($response, $this->getStockService()->MergeProducts($args['productIdToKeep'], $args['productIdToRemove']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
