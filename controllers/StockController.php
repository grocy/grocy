<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class StockController extends BaseController
{

	public function __construct(\Slim\Container $container)
	{
		#$fp = fopen('/www/data/sql.log', 'a');
		#fwrite($fp, "!!!constructing StockController\n");
		#fclose($fp);
		parent::__construct($container);
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expring_soon_days'];

		return $this->renderPage($response, 'stockoverview', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'currentStock' => $this->StockService->GetCurrentStock(true),
			'currentStockLocations' => $this->StockService->GetCurrentStockLocations(),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'nextXDays' => $nextXDays,
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function Purchase(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'purchase', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function Consume(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		#$fp = fopen('/www/data/sql.log', 'a');
        #fwrite($fp, "???executing consume stock");
		#$time_start = microtime(true);
		$result = $this->renderPage($response, 'consume', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'recipes' => $this->getDatabase()->recipes()->orderBy('name')
		]);
		#fwrite($fp, "???Total execution time in seconds: " . round((microtime(true) - $time_start),6) . "\n");
		#fclose($fp);
        return $result;
	}

	public function Inventory(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'inventory', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function ShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$listId = 1;
		if (isset($request->getQueryParams()['list']))
		{
			$listId = $request->getQueryParams()['list'];
		}

		return $this->renderPage($response, 'shoppinglist', [
			'listItems' => $this->getDatabase()->shopping_list()->where('shopping_list_id = :1', $listId),
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
			'selectedShoppingListId' => $listId,
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function ProductsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'products', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function StockSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'stocksettings', [
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name')
		]);
	}

	public function LocationsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'locations', [
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('locations'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('locations')
		]);
	}

	public function ProductGroupsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'productgroups', [
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('product_groups'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('product_groups')
		]);
	}

	public function QuantityUnitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'quantityunits', [
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('quantity_units')
		]);
	}

	public function ProductEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->renderPage($response, 'productform', [
				'locations' =>  $this->getDatabase()->locations()->orderBy('name'),
				'quantityunits' =>  $this->getDatabase()->quantity_units()->orderBy('name'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('parent_product_id IS NULL')->orderBy('name'),
				'isSubProductOfOthers' => false,
				'mode' => 'create'
			]);
		}
		else
		{
			$product = $this->getDatabase()->products($args['productId']);

			return $this->renderPage($response, 'productform', [
				'product' =>  $product,
				'locations' =>  $this->getDatabase()->locations()->orderBy('name'),
				'quantityunits' =>  $this->getDatabase()->quantity_units()->orderBy('name'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('id != :1 AND parent_product_id IS NULL', $product->id)->orderBy('name'),
				'isSubProductOfOthers' => $this->getDatabase()->products()->where('parent_product_id = :1', $product->id)->count() !== 0,
				'mode' => 'edit',
				'quConversions' => $this->getDatabase()->quantity_unit_conversions()
			]);
		}
	}

	public function LocationEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->renderPage($response, 'locationform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('locations')
			]);
		}
		else
		{
			return $this->renderPage($response, 'locationform', [
				'location' =>  $this->getDatabase()->locations($args['locationId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('locations')
			]);
		}
	}

	public function ProductGroupEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productGroupId'] == 'new')
		{
			return $this->renderPage($response, 'productgroupform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('product_groups')
			]);
		}
		else
		{
			return $this->renderPage($response, 'productgroupform', [
				'group' =>  $this->getDatabase()->product_groups($args['productGroupId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('product_groups')
			]);
		}
	}

	public function QuantityUnitEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->renderPage($response, 'quantityunitform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
				'pluralCount' => $this->getLocalizationService()->GetPluralCount(),
				'pluralRule' => $this->getLocalizationService()->GetPluralDefinition()
			]);
		}
		else
		{
			$quantityUnit = $this->getDatabase()->quantity_units($args['quantityunitId']);

			return $this->renderPage($response, 'quantityunitform', [
				'quantityUnit' =>  $quantityUnit,
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
				'pluralCount' => $this->getLocalizationService()->GetPluralCount(),
				'pluralRule' => $this->getLocalizationService()->GetPluralDefinition(),
				'defaultQuConversions' => $this->getDatabase()->quantity_unit_conversions()->where('from_qu_id = :1 AND product_id IS NULL', $quantityUnit->id),
				'quantityUnits' => $this->getDatabase()->quantity_units()
			]);
		}
	}

	public function ShoppingListItemEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->renderPage($response, 'shoppinglistitemform', [
				'products' =>  $this->getDatabase()->products()->orderBy('name'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->renderPage($response, 'shoppinglistitemform', [
				'listItem' =>  $this->getDatabase()->shopping_list($args['itemId']),
				'products' =>  $this->getDatabase()->products()->orderBy('name'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['listId'] == 'new')
		{
			return $this->renderPage($response, 'shoppinglistform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->renderPage($response, 'shoppinglistform', [
				'shoppingList' =>  $this->getDatabase()->shopping_lists($args['listId']),
				'mode' => 'edit'
			]);
		}
	}

	public function Journal(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'stockjournal', [
			'stockLog' => $this->getDatabase()->stock_log()->orderBy('row_created_timestamp', 'DESC'),
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name')
		]);
	}

	public function LocationContentSheet(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'locationcontentsheet', [
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'currentStockLocationContent' => $this->StockService->GetCurrentStockLocationContent()
		]);
	}

	public function QuantityUnitConversionEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$product = null;
		if (isset($request->getQueryParams()['product']))
		{
			$product = $this->getDatabase()->products($request->getQueryParams()['product']);
		}

		$defaultQuUnit = null;
		if (isset($request->getQueryParams()['qu-unit']))
		{
			$defaultQuUnit = $this->getDatabase()->quantity_units($request->getQueryParams()['qu-unit']);
		}

		if ($args['quConversionId'] == 'new')
		{
			return $this->renderPage($response, 'quantityunitconversionform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
		else
		{
			return $this->renderPage($response, 'quantityunitconversionform', [
				'quConversion' =>  $this->getDatabase()->quantity_unit_conversions($args['quConversionId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
	}

	public function QuantityUnitPluralFormTesting(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'quantityunitpluraltesting', [
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name')
		]);
	}
}
