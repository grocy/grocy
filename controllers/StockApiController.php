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
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
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
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function AddProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']) && IsIsoDate($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		$price = null;
		if (isset($request->getQueryParams()['price']) && !empty($request->getQueryParams()['price']) && is_numeric($request->getQueryParams()['price']))
		{
			$price = $request->getQueryParams()['price'];
		}

		$transactionType = StockService::TRANSACTION_TYPE_PURCHASE;
		if (isset($request->getQueryParams()['transactiontype']) && !empty($request->getQueryParams()['transactiontype']))
		{
			$transactionType = $request->getQueryParams()['transactiontype'];
		}

		try
		{
			$this->StockService->AddProduct($args['productId'], $args['amount'], $bestBeforeDate, $transactionType, date('Y-m-d'), $price);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function ConsumeProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$spoiled = false;
		if (isset($request->getQueryParams()['spoiled']) && !empty($request->getQueryParams()['spoiled']) && $request->getQueryParams()['spoiled'] == '1')
		{
			$spoiled = true;
		}

		$transactionType = StockService::TRANSACTION_TYPE_CONSUME;
		if (isset($request->getQueryParams()['transactiontype']) && !empty($request->getQueryParams()['transactiontype']))
		{
			$transactionType = $request->getQueryParams()['transactiontype'];
		}

		try
		{
			$this->StockService->ConsumeProduct($args['productId'], $args['amount'], $spoiled, $transactionType);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function InventoryProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']) && IsIsoDate($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		try
		{
			$this->StockService->InventoryProduct($args['productId'], $args['newAmount'], $bestBeforeDate);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
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
		$this->StockService->AddMissingProductsToShoppingList();
		return $this->VoidApiActionResponse($response);
	}

	public function ClearShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$this->StockService->ClearShoppingList();
		return $this->VoidApiActionResponse($response);
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
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
