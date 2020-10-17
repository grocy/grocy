<?php

namespace Grocy\Controllers;

class StockController extends BaseController
{
	public function Consume(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$sql = 'select group_concat(barcode) barcodes, product_id from product_barcodes group by product_id';
		$productBarcodes = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);

		return $this->renderPage($response, 'consume', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'barcodes' => $productBarcodes,
			'recipes' => $this->getDatabase()->recipes()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function Inventory(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$sql = 'select group_concat(barcode) barcodes, product_id from product_barcodes group by product_id';
		$productBarcodes = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);

		return $this->renderPage($response, 'inventory', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'barcodes' => $productBarcodes,
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'stockjournal', [
			'stockLog' => $this->getDatabase()->uihelper_stock_journal()->orderBy('row_created_timestamp', 'DESC'),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
		]);
	}

	public function LocationContentSheet(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'locationcontentsheet', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'currentStockLocationContent' => $this->getStockService()->GetCurrentStockLocationContent()
		]);
	}

	public function LocationEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'location' => $this->getDatabase()->locations($args['locationId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('locations')
			]);
		}
	}

	public function LocationsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'locations', [
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('locations'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('locations')
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expiring_soon_days'];

		return $this->renderPage($response, 'stockoverview', [
			'currentStock' => $this->getStockService()->GetCurrentStockOverview(),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'currentStockLocations' => $this->getStockService()->GetCurrentStockLocations(),
			'nextXDays' => $nextXDays,
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function ProductBarcodesEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$product = null;

		if (isset($request->getQueryParams()['product']))
		{
			$product = $this->getDatabase()->products($request->getQueryParams()['product']);
		}

		if ($args['productBarcodeId'] == 'new')
		{
			return $this->renderPage($response, 'productbarcodesform', [
				'mode' => 'create',
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'product' => $product,
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name')
			]);
		}
		else
		{
			return $this->renderPage($response, 'productbarcodesform', [
				'mode' => 'edit',
				'barcode' => $this->getDatabase()->product_barcodes($args['productBarcodeId']),
				'product' => $product,
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name')
			]);
		}
	}

	public function ProductEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->renderPage($response, 'productform', [
				'locations' => $this->getDatabase()->locations()->orderBy('name'),
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('parent_product_id IS NULL and active = 1')->orderBy('name'),
				'isSubProductOfOthers' => false,
				'mode' => 'create'
			]);
		}
		else
		{
			$product = $this->getDatabase()->products($args['productId']);

			return $this->renderPage($response, 'productform', [
				'product' => $product,
				'locations' => $this->getDatabase()->locations()->orderBy('name'),
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('id != :1 AND parent_product_id IS NULL and active = 1', $product->id)->orderBy('name'),
				'isSubProductOfOthers' => $this->getDatabase()->products()->where('parent_product_id = :1', $product->id)->count() !== 0,
				'mode' => 'edit',
				'quConversions' => $this->getDatabase()->quantity_unit_conversions()
			]);
		}
	}

	public function ProductGroupEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'group' => $this->getDatabase()->product_groups($args['productGroupId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('product_groups')
			]);
		}
	}

	public function ProductGroupsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'productgroups', [
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('product_groups'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('product_groups')
		]);
	}

	public function ProductsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

	public function Purchase(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$sql = 'select group_concat(barcode) barcodes, product_id from product_barcodes group by product_id';
		$productBarcodes = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);

		return $this->renderPage($response, 'purchase', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'barcodes' => $productBarcodes,
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function QuantityUnitConversionEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'quConversion' => $this->getDatabase()->quantity_unit_conversions($args['quConversionId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
	}

	public function QuantityUnitEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'quantityUnit' => $quantityUnit,
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
				'pluralCount' => $this->getLocalizationService()->GetPluralCount(),
				'pluralRule' => $this->getLocalizationService()->GetPluralDefinition(),
				'defaultQuConversions' => $this->getDatabase()->quantity_unit_conversions()->where('from_qu_id = :1 AND product_id IS NULL', $quantityUnit->id),
				'quantityUnits' => $this->getDatabase()->quantity_units()
			]);
		}
	}

	public function QuantityUnitPluralFormTesting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'quantityunitpluraltesting', [
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name')
		]);
	}

	public function QuantityUnitsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'quantityunits', [
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('quantity_units')
		]);
	}

	public function ShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$listId = 1;

		if (isset($request->getQueryParams()['list']))
		{
			$listId = $request->getQueryParams()['list'];
		}

		return $this->renderPage($response, 'shoppinglist', [
			'listItems' => $this->getDatabase()->shopping_list()->where('shopping_list_id = :1', $listId),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'missingProducts' => $this->getStockService()->GetMissingProducts(),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name'),
			'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
			'selectedShoppingListId' => $listId,
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function ShoppingListEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'shoppingList' => $this->getDatabase()->shopping_lists($args['listId']),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListItemEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->renderPage($response, 'shoppinglistitemform', [
				'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->renderPage($response, 'shoppinglistitemform', [
				'listItem' => $this->getDatabase()->shopping_list($args['itemId']),
				'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name'),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'shoppinglistsettings');
	}

	public function ShoppingLocationEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['shoppingLocationId'] == 'new')
		{
			return $this->renderPage($response, 'shoppinglocationform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations')
			]);
		}
		else
		{
			return $this->renderPage($response, 'shoppinglocationform', [
				'shoppinglocation' => $this->getDatabase()->shopping_locations($args['shoppingLocationId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations')
			]);
		}
	}

	public function ShoppingLocationsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'shoppinglocations', [
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('shopping_locations')
		]);
	}

	public function StockEntryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'stockentryform', [
			'stockEntry' => $this->getDatabase()->stock()->where('id', $args['entryId'])->fetch(),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function StockSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'stocksettings', [
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name')
		]);
	}

	public function Stockentries(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_expiring_soon_days'];

		return $this->renderPage($response, 'stockentries', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name'),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name'),
			'stockEntries' => $this->getDatabase()->stock()->orderBy('product_id'),
			'currentStockLocations' => $this->getStockService()->GetCurrentStockLocations(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function Transfer(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$sql = 'select group_concat(barcode) barcodes, product_id from product_barcodes group by product_id';
		$productBarcodes = $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);

		return $this->renderPage($response, 'transfer', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'barcodes' => $productBarcodes,
			'recipes' => $this->getDatabase()->recipes()->orderBy('name'),
			'locations' => $this->getDatabase()->locations()->orderBy('name')
		]);
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function JournalSummary(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$entries = $this->getDatabase()->uihelper_stock_journal_summary();
		if (isset($request->getQueryParams()['product_id']))
		{
			$entries = $entries->where('product_id', $request->getQueryParams()['product_id']);
		}
		if (isset($request->getQueryParams()['user_id']))
		{
			$entries = $entries->where('user_id', $request->getQueryParams()['user_id']);
		}
		if (isset($request->getQueryParams()['transaction_type']))
		{
			$entries = $entries->where('transaction_type', $request->getQueryParams()['transaction_type']);
		}
		return $this->renderPage($response, 'stockjournalsummary', [
			'entries' => $entries
		]);
	}
}
