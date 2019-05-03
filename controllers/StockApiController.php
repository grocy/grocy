<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;

class StockApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function ProductDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->StockService->GetProductDetails($args['productId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductDetailsByBarcode(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$productId = $this->StockService->GetProductIdFromBarcode($args['barcode']);
			return $this->ApiResponse($this->StockService->GetProductDetails($productId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductPriceHistory(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->StockService->GetProductPriceHistory($args['productId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function AddProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

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

			$bestBeforeDate = date('Y-m-d');
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

			$transactionType = StockService::TRANSACTION_TYPE_PURCHASE;
			if (array_key_exists('transaction_type', $requestBody)  && !empty($requestBody['transactiontype']))
			{
				$transactionType = $requestBody['transactiontype'];
			}

			$bookingId = $this->StockService->AddProduct($args['productId'], $requestBody['amount'], $bestBeforeDate, $transactionType, date('Y-m-d'), $price, $locationId);
			return $this->ApiResponse($this->Database->stock_log($bookingId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ConsumeProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

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
			if (array_key_exists('transaction_type', $requestBody)  && !empty($requestBody['transactiontype']))
			{
				$transactionType = $requestBody['transactiontype'];
			}

			$specificStockEntryId = 'default';
			if (array_key_exists('stock_entry_id', $requestBody) && !empty($requestBody['stock_entry_id']))
			{
				$specificStockEntryId = $requestBody['stock_entry_id'];
			}

			$recipeId = null;
			if (array_key_exists('recipe_id', $requestBody) && is_numeric($requestBody['recipe_id']))
			{
				$recipeId = $requestBody['recipe_id'];
			}

			$bookingId = $this->StockService->ConsumeProduct($args['productId'], $requestBody['amount'], $spoiled, $transactionType, $specificStockEntryId, $recipeId);
			return $this->ApiResponse($this->Database->stock_log($bookingId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function InventoryProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

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

			$bestBeforeDate = date('Y-m-d');
			if (array_key_exists('best_before_date', $requestBody) && IsIsoDate($requestBody['best_before_date']))
			{
				$bestBeforeDate = $requestBody['best_before_date'];
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

			$bookingId = $this->StockService->InventoryProduct($args['productId'], $requestBody['new_amount'], $bestBeforeDate, $locationId, $price);
			return $this->ApiResponse($this->Database->stock_log($bookingId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function OpenProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

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

			$bookingId = $this->StockService->OpenProduct($args['productId'], $requestBody['amount'], $specificStockEntryId);
			return $this->ApiResponse($this->Database->stock_log($bookingId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CurrentStock(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->StockService->GetCurrentStock());
	}

	public function CurrentVolatilStock(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$nextXDays = 5;
		if (isset($request->getQueryParams()['expiring_days']) && !empty($request->getQueryParams()['expiring_days']) && is_numeric($request->getQueryParams()['expiring_days']))
		{
			$nextXDays = $request->getQueryParams()['expiring_days'];
		}

		$expiringProducts = $this->StockService->GetExpiringProducts($nextXDays, true);
		$expiredProducts = $this->StockService->GetExpiringProducts(-1);
		$missingProducts = $this->StockService->GetMissingProducts();
		return $this->ApiResponse(array(
			 'expiring_products' => $expiringProducts,
			 'expired_products' => $expiredProducts,
			 'missing_products' => $missingProducts
		));
	}

	public function AddMissingProductsToShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$requestBody = $request->getParsedBody();

			$listId = 1;
			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->StockService->AddMissingProductsToShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ClearShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$requestBody = $request->getParsedBody();

			$listId = 1;
			if (array_key_exists('list_id', $requestBody) && !empty($requestBody['list_id']) && is_numeric($requestBody['list_id']))
			{
				$listId = intval($requestBody['list_id']);
			}

			$this->StockService->ClearShoppingList($listId);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ExternalBarcodeLookup(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$addFoundProduct = false;
			if (isset($request->getQueryParams()['add']) && ($request->getQueryParams()['add'] === 'true' || $request->getQueryParams()['add'] === 1))
			{
				$addFoundProduct = true;
			}
			
			return $this->ApiResponse($this->StockService->ExternalBarcodeLookup($args['barcode'], $addFoundProduct));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoBooking(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->ApiResponse($this->StockService->UndoBooking($args['bookingId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ProductStockEntries(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->StockService->GetProductStockEntries($args['productId']));
	}
}
