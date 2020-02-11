<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class StockController extends BaseController
{

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $StockService;
	protected $UserfieldsService;

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expring_soon_days'];

		return $this->View->render($response, 'stockoverview', [
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'currentStock' => $this->StockService->GetCurrentStock(true),
			'currentStockLocations' => $this->StockService->GetCurrentStockLocations(),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'nextXDays' => $nextXDays,
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function Stockentries(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expring_soon_days'];

		return $this->View->render($response, 'stockentries', [
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'stockEntries' => $this->Database->stock()->orderBy('product_id'),
			'currentStockLocations' => $this->StockService->GetCurrentStockLocations(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function Purchase(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'purchase', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function Consume(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'consume', [
			'products' => $this->Database->products()->orderBy('name'),
			'recipes' => $this->Database->recipes()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function Transfer(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'transfer', [
			'products' => $this->Database->products()->orderBy('name'),
			'recipes' => $this->Database->recipes()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function Inventory(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'inventory', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function StockEntryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'stockentryform', [
			'stockEntry' => $this->Database->stock()->where('id', $args['entryId'])->fetch(),
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function ShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$listId = 1;
		if (isset($request->getQueryParams()['list']))
		{
			$listId = $request->getQueryParams()['list'];
		}

		return $this->View->render($response, 'shoppinglist', [
			'listItems' => $this->Database->shopping_list()->where('shopping_list_id = :1', $listId),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
			'selectedShoppingListId' => $listId,
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function ProductsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'products', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('products'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('products')
		]);
	}

	public function StockSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'stocksettings', [
			'locations' => $this->Database->locations()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function LocationsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'locations', [
			'locations' => $this->Database->locations()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('locations'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('locations')
		]);
	}

	public function ProductGroupsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'productgroups', [
			'productGroups' => $this->Database->product_groups()->orderBy('name'),
			'products' => $this->Database->products()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('product_groups'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('product_groups')
		]);
	}

	public function QuantityUnitsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'quantityunits', [
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('quantity_units')
		]);
	}

	public function ProductEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->View->render($response, 'productform', [
				'locations' =>  $this->Database->locations()->orderBy('name'),
				'quantityunits' =>  $this->Database->quantity_units()->orderBy('name'),
				'productgroups' => $this->Database->product_groups()->orderBy('name'),
				'userfields' => $this->UserfieldsService->GetFields('products'),
				'products' => $this->Database->products()->where('parent_product_id IS NULL')->orderBy('name'),
				'isSubProductOfOthers' => false,
				'mode' => 'create'
			]);
		}
		else
		{
			$product = $this->Database->products($args['productId']);

			return $this->View->render($response, 'productform', [
				'product' =>  $product,
				'locations' =>  $this->Database->locations()->orderBy('name'),
				'quantityunits' =>  $this->Database->quantity_units()->orderBy('name'),
				'productgroups' => $this->Database->product_groups()->orderBy('name'),
				'userfields' => $this->UserfieldsService->GetFields('products'),
				'products' => $this->Database->products()->where('id != :1 AND parent_product_id IS NULL', $product->id)->orderBy('name'),
				'isSubProductOfOthers' => $this->Database->products()->where('parent_product_id = :1', $product->id)->count() !== 0,
				'mode' => 'edit',
				'quConversions' => $this->Database->quantity_unit_conversions()
			]);
		}
	}

	public function LocationEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->View->render($response, 'locationform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('locations')
			]);
		}
		else
		{
			return $this->View->render($response, 'locationform', [
				'location' =>  $this->Database->locations($args['locationId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('locations')
			]);
		}
	}

	public function ProductGroupEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['productGroupId'] == 'new')
		{
			return $this->View->render($response, 'productgroupform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('product_groups')
			]);
		}
		else
		{
			return $this->View->render($response, 'productgroupform', [
				'group' =>  $this->Database->product_groups($args['productGroupId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('product_groups')
			]);
		}
	}

	public function QuantityUnitEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->View->render($response, 'quantityunitform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
				'pluralCount' => $this->LocalizationService->GetPluralCount(),
				'pluralRule' => $this->LocalizationService->GetPluralDefinition()
			]);
		}
		else
		{
			$quantityUnit = $this->Database->quantity_units($args['quantityunitId']);

			return $this->View->render($response, 'quantityunitform', [
				'quantityUnit' =>  $quantityUnit,
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('quantity_units'),
				'pluralCount' => $this->LocalizationService->GetPluralCount(),
				'pluralRule' => $this->LocalizationService->GetPluralDefinition(),
				'defaultQuConversions' => $this->Database->quantity_unit_conversions()->where('from_qu_id = :1 AND product_id IS NULL', $quantityUnit->id),
				'quantityUnits' => $this->Database->quantity_units()
			]);
		}
	}

	public function ShoppingListItemEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->View->render($response, 'shoppinglistitemform', [
				'products' =>  $this->Database->products()->orderBy('name'),
				'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->View->render($response, 'shoppinglistitemform', [
				'listItem' =>  $this->Database->shopping_list($args['itemId']),
				'products' =>  $this->Database->products()->orderBy('name'),
				'shoppingLists' => $this->Database->shopping_lists()->orderBy('name'),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['listId'] == 'new')
		{
			return $this->View->render($response, 'shoppinglistform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->View->render($response, 'shoppinglistform', [
				'shoppingList' =>  $this->Database->shopping_lists($args['listId']),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'shoppinglistsettings');
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'stockjournal', [
			'stockLog' => $this->Database->stock_log()->orderBy('row_created_timestamp', 'DESC'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name')
		]);
	}

	public function LocationContentSheet(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'locationcontentsheet', [
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'currentStockLocationContent' => $this->StockService->GetCurrentStockLocationContent()
		]);
	}

	public function QuantityUnitConversionEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$product = null;
		if (isset($request->getQueryParams()['product']))
		{
			$product = $this->Database->products($request->getQueryParams()['product']);
		}

		$defaultQuUnit = null;
		if (isset($request->getQueryParams()['qu-unit']))
		{
			$defaultQuUnit = $this->Database->quantity_units($request->getQueryParams()['qu-unit']);
		}

		if ($args['quConversionId'] == 'new')
		{
			return $this->View->render($response, 'quantityunitconversionform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
		else
		{
			return $this->View->render($response, 'quantityunitconversionform', [
				'quConversion' =>  $this->Database->quantity_unit_conversions($args['quConversionId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
	}

	public function QuantityUnitPluralFormTesting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'quantityunitpluraltesting', [
			'quantityUnits' => $this->Database->quantity_units()->orderBy('name')
		]);
	}
}
