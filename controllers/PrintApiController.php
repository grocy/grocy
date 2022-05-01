<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Services\StockService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PrintApiController extends BaseApiController
{
	public function PrintShoppingListThermal(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		try
		{
			User::checkPermission($request, User::PERMISSION_SHOPPINGLIST);

			$params = $request->getQueryParams();

			$listId = 1;
			if (isset($params['list']))
			{
				$listId = $params['list'];
			}

			$printHeader = true;
			if (isset($params['printHeader']))
			{
				$printHeader = ($params['printHeader'] === 'true');
			}
			$items = $this->getStockService()->GetShoppinglistInPrintableStrings($listId);
			return $this->ApiResponse($response, $this->getPrintService()->printShoppingList($printHeader, $items));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
