<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Services\StockService;

class PrintApiController extends BaseApiController
{

	public function PrintShoppingListThermal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args) {

		try
		{
			User::checkPermission($request, User::PERMISSION_SHOPPINGLIST);

			$params = $request->getQueryParams();

			$listId = 1;
			if (isset($params['list'])) {
				$listId = $params['list'];
			}

			$printHeader = true;
			if (isset($params['printHeader'])) {
				$printHeader = ($params['printHeader'] === "true");
			}
			$items = $this->getStockService()->GetShoppinglistInPrintableStrings($listId);
			return $this->ApiResponse($response, $this->getPrintService()->printShoppingList($printHeader, $items));
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
