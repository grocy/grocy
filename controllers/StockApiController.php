<?php

namespace Grocy\Controllers;

use Grocy\Services\StockService;

class StockApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function ProductDetails($request, $response, $args)
	{
		return $this->ApiEncode($this->StockService->GetProductDetails($args['productId']));
	}

	public function AddProduct($request, $response, $args)
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

		return $this->ApiEncode(array('success' => $this->StockService->AddProduct($args['productId'], $args['amount'], $bestBeforeDate, $transactionType)));
	}

	public function ConsumeProduct($request, $response, $args)
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

		return $this->ApiEncode(array('success' => $this->StockService->ConsumeProduct($args['productId'], $args['amount'], $spoiled, $transactionType)));
	}

	public function InventoryProduct($request, $response, $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		return $this->ApiEncode(array('success' => $this->StockService->InventoryProduct($args['productId'], $args['newAmount'], $bestBeforeDate)));
	}

	public function CurrentStock($request, $response, $args)
	{
		return $this->ApiEncode($this->StockService->GetCurrentStock());
	}

	public function AddmissingProductsToShoppingList($request, $response, $args)
	{
		$this->StockService->AddMissingProductsToShoppingList();
		return $this->ApiEncode(array('success' => true));
	}
}
