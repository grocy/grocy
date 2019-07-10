<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class StockController extends BaseController
{

	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $StockService;
	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expring_soon_days'];

		return $this->AppContainer->view->render($response, 'stockoverview', [
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'currentStock' => $this->StockService->GetCurrentStock(),
			'currentStockLocations' => $this->StockService->GetCurrentStockLocations(),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'nextXDays' => $nextXDays,
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function Purchase(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'purchase', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function Consume(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'consume', [
			'products' => $this->Database->products()->orderBy('name'),
			'recipes' => $this->Database->recipes()->orderBy('name')
		]);
	}

	public function Inventory(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'inventory', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function ShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$listId = 1;
		if (isset($request->getQueryParams()['list']))
		{
			$listId = $request->getQueryParams()['list'];
		}

		return $this->AppContainer->view->render($response, 'shoppinglist', [
			'listItems' => $this->Database->shopping_list()->where('shopping_list_id = :1', $listId),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
			'selectedShoppingListId' => $listId
		]);
	}

	public function ProductsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'products', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function StockSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'stocksettings', [
			'locations' => $this->Database->locations()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function LocationsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'locations', [
			'locations' => $this->Database->locations()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('locations'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('locations')
		]);
	}

	public function ProductGroupsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'productgroups', [
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'products' => $this->Database->products()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('product_groups'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('product_groups')
		]);
	}

	public function QuantityUnitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'quantityunits', [
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('quantity_units')
		]);
	}

	public function ProductEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'locations' =>  $this->Database->locations()->orderBy('name'),
				'quantityunits' =>  $this->Database->quantity_units()->orderBy('name'),
				'productgroups' => $this->Database->product_groups()->orderBy('name'),
				'userfields' => $this->UserfieldsService->GetFields('products'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'product' =>  $this->Database->products($args['productId']),
				'locations' =>  $this->Database->locations()->orderBy('name'),
				'quantityunits' =>  $this->Database->quantity_units()->orderBy('name'),
				'productgroups' => $this->Database->product_groups()->orderBy('name'),
				'userfields' => $this->UserfieldsService->GetFields('products'),
				'mode' => 'edit'
			]);
		}
	}

	public function LocationEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('locations')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'location' =>  $this->Database->locations($args['locationId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('locations')
			]);
		}
	}

	public function ProductGroupEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productGroupId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'productgroupform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('product_groups')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'productgroupform', [
				'group' =>  $this->Database->product_groups($args['productGroupId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('product_groups')
			]);
		}
	}

	public function QuantityUnitEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
				'pluralCount' => $this->LocalizationService->GetPluralCount(),
				'pluralRule' => $this->LocalizationService->GetPluralDefinition()
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'quantityunit' =>  $this->Database->quantity_units($args['quantityunitId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
				'pluralCount' => $this->LocalizationService->GetPluralCount(),
				'pluralRule' => $this->LocalizationService->GetPluralDefinition()
			]);
		}
	}

	public function ShoppingListItemEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'shoppinglistitemform', [
				'products' =>  $this->Database->products()->orderBy('name'),
				'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'shoppinglistitemform', [
				'listItem' =>  $this->Database->shopping_list($args['itemId']),
				'products' =>  $this->Database->products()->orderBy('name'),
				'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['listId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'shoppingList' =>  $this->Database->shopping_lists($args['listId']),
				'mode' => 'edit'
			]);
		}
	}

	public function Journal(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'stockjournal', [
			'stockLog' => $this->Database->stock_log()->orderBy('row_created_timestamp', 'DESC'),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name')
		]);
	}
}
