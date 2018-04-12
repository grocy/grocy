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
		return $this->ApiResponse($this->StockService->GetProductDetails($args['productId']));
	}

	public function AddProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		$transactionType = StockService::TRANSACTION_TYPE_PURCHASE;
		if (isset($request->getQueryParams()['transactiontype']) && !empty($request->getQueryParams()['transactiontype']))
		{
			$transactionType = $request->getQueryParams()['transactiontype'];
		}

		return $this->ApiResponse(array('success' => $this->StockService->AddProduct($args['productId'], $args['amount'], $bestBeforeDate, $transactionType)));
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

		return $this->ApiResponse(array('success' => $this->StockService->ConsumeProduct($args['productId'], $args['amount'], $spoiled, $transactionType)));
	}

	public function InventoryProduct(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		return $this->ApiResponse(array('success' => $this->StockService->InventoryProduct($args['productId'], $args['newAmount'], $bestBeforeDate)));
	}

	public function CurrentStock(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->StockService->GetCurrentStock());
	}

	public function AddmissingProductsToShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$this->StockService->AddMissingProductsToShoppingList();
		return $this->ApiResponse(array('success' => true));
	}
}
