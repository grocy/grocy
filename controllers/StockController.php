<?php

namespace Grocy\Controllers;

use Grocy\Helpers\Grocycode;
use Grocy\Services\RecipesService;
use jucksearm\barcode\lib\DatamatrixFactory;

class StockController extends BaseController
{
	public function Consume(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'consume', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name'),
			'barcodes' => $this->getDatabase()->product_barcodes_comma_separated(),
			'recipes' => $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function Inventory(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'inventory', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'barcodes' => $this->getDatabase()->product_barcodes_comma_separated(),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();

		return $this->renderPage($request, $response, 'stockjournal', [
			'stockLog' => $this->getDatabase()->uihelper_stock_journal()->orderBy('row_created_timestamp', 'DESC'),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'users' => $usersService->GetUsersAsDto(),
			'transactionTypes' => GetClassConstants('\Grocy\Services\StockService', 'TRANSACTION_TYPE_')
		]);
	}

	public function LocationContentSheet(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'locationcontentsheet', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'currentStockLocationContent' => $this->getStockService()->GetCurrentStockLocationContent()
		]);
	}

	public function LocationEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->renderPage($request, $response, 'locationform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('locations')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'locationform', [
				'location' => $this->getDatabase()->locations($args['locationId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('locations')
			]);
		}
	}

	public function LocationsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'locations', [
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'userfields' => $this->getUserfieldsService()->GetFields('locations'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('locations')
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_due_soon_days'];

		return $this->renderPage($request, $response, 'stockoverview', [
			'currentStock' => $this->getStockService()->GetCurrentStockOverview(),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'currentStockLocations' => $this->getStockService()->GetCurrentStockLocations(),
			'nextXDays' => $nextXDays,
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
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
			return $this->renderPage($request, $response, 'productbarcodeform', [
				'mode' => 'create',
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'product' => $product,
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
				'userfields' => $this->getUserfieldsService()->GetFields('product_barcodes')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'productbarcodeform', [
				'mode' => 'edit',
				'barcode' => $this->getDatabase()->product_barcodes($args['productBarcodeId']),
				'product' => $product,
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
				'userfields' => $this->getUserfieldsService()->GetFields('product_barcodes')
			]);
		}
	}

	public function ProductEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->renderPage($request, $response, 'productform', [
				'locations' => $this->getDatabase()->locations()->orderBy('name'),
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('parent_product_id IS NULL and active = 1')->orderBy('name', 'COLLATE NOCASE'),
				'isSubProductOfOthers' => false,
				'mode' => 'create'
			]);
		}
		else
		{
			$product = $this->getDatabase()->products($args['productId']);

			return $this->renderPage($request, $response, 'productform', [
				'product' => $product,
				'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
				'barcodes' => $this->getDatabase()->product_barcodes()->orderBy('barcode'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
				'productgroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
				'userfields' => $this->getUserfieldsService()->GetFields('products'),
				'products' => $this->getDatabase()->products()->where('id != :1 AND parent_product_id IS NULL and active = 1', $product->id)->orderBy('name', 'COLLATE NOCASE'),
				'isSubProductOfOthers' => $this->getDatabase()->products()->where('parent_product_id = :1', $product->id)->count() !== 0,
				'mode' => 'edit',
				'quConversions' => $this->getDatabase()->quantity_unit_conversions(),
				'productBarcodeUserfields' => $this->getUserfieldsService()->GetFields('product_barcodes'),
				'productBarcodeUserfieldValues' => $this->getUserfieldsService()->GetAllValues('product_barcodes')
			]);
		}
	}

	public function ProductGrocycodeImage(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$size = $request->getQueryParam('size', null);
		$product = $this->getDatabase()->products($args['productId']);

		$gc = new Grocycode(Grocycode::PRODUCT, $product->id);

		// Explicitly suppress errors, otherwise deprecations warnings would cause invalid PNG data
		// See also https://github.com/jucksearm/php-barcode/issues/3
		$png = @(new DatamatrixFactory())->setCode((string) $gc)->setSize($size)->getDatamatrixPngData();

		$isDownload = $request->getQueryParam('download', false);

		if ($isDownload)
		{
			$response = $response->withHeader('Content-Type', 'application/octet-stream')
			->withHeader('Content-Disposition', 'attachment; filename=grocycode.png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		else
		{
			$response = $response->withHeader('Content-Type', 'image/png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		$response->getBody()->write($png);
		return $response;
	}

	public function ProductGroupEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['productGroupId'] == 'new')
		{
			return $this->renderPage($request, $response, 'productgroupform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('product_groups')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'productgroupform', [
				'group' => $this->getDatabase()->product_groups($args['productGroupId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('product_groups')
			]);
		}
	}

	public function ProductGroupsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'productgroups', [
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'userfields' => $this->getUserfieldsService()->GetFields('product_groups'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('product_groups')
		]);
	}

	public function ProductsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['include_disabled']))
		{
			$products = $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$products = $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		}

		return $this->renderPage($request, $response, 'products', [
			'products' => $products,
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
			'shoppingLocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function Purchase(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'purchase', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'barcodes' => $this->getDatabase()->product_barcodes_comma_separated(),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
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
			return $this->renderPage($request, $response, 'quantityunitconversionform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'quantityunitconversionform', [
				'quConversion' => $this->getDatabase()->quantity_unit_conversions($args['quConversionId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_unit_conversions'),
				'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'product' => $product,
				'defaultQuUnit' => $defaultQuUnit
			]);
		}
	}

	public function QuantityUnitEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->renderPage($request, $response, 'quantityunitform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('quantity_units'),
				'pluralCount' => $this->getLocalizationService()->GetPluralCount(),
				'pluralRule' => $this->getLocalizationService()->GetPluralDefinition()
			]);
		}
		else
		{
			$quantityUnit = $this->getDatabase()->quantity_units($args['quantityunitId']);

			return $this->renderPage($request, $response, 'quantityunitform', [
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
		return $this->renderPage($request, $response, 'quantityunitpluraltesting', [
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function QuantityUnitsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'quantityunits', [
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
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

		return $this->renderPage($request, $response, 'shoppinglist', [
			'listItems' => $this->getDatabase()->uihelper_shopping_list()->where('shopping_list_id = :1', $listId),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'missingProducts' => $this->getStockService()->GetMissingProducts(),
			'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name', 'COLLATE NOCASE'),
			'selectedShoppingListId' => $listId,
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
			'productUserfields' => $this->getUserfieldsService()->GetFields('products'),
			'productUserfieldValues' => $this->getUserfieldsService()->GetAllValues('products'),
			'userfields' => $this->getUserfieldsService()->GetFields('shopping_list'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('shopping_list')
		]);
	}

	public function ShoppingListEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['listId'] == 'new')
		{
			return $this->renderPage($request, $response, 'shoppinglistform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_lists')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'shoppinglistform', [
				'shoppingList' => $this->getDatabase()->shopping_lists($args['listId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_lists')
			]);
		}
	}

	public function ShoppingListItemEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->renderPage($request, $response, 'shoppinglistitemform', [
				'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name', 'COLLATE NOCASE'),
				'mode' => 'create',
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_list')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'shoppinglistitemform', [
				'listItem' => $this->getDatabase()->shopping_list($args['itemId']),
				'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
				'shoppingLists' => $this->getDatabase()->shopping_lists()->orderBy('name', 'COLLATE NOCASE'),
				'mode' => 'edit',
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_list')
			]);
		}
	}

	public function ShoppingListSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'shoppinglistsettings');
	}

	public function ShoppingLocationEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['shoppingLocationId'] == 'new')
		{
			return $this->renderPage($request, $response, 'shoppinglocationform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'shoppinglocationform', [
				'shoppinglocation' => $this->getDatabase()->shopping_locations($args['shoppingLocationId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations')
			]);
		}
	}

	public function ShoppingLocationsList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'shoppinglocations', [
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'userfields' => $this->getUserfieldsService()->GetFields('shopping_locations'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('shopping_locations')
		]);
	}

	public function StockEntryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'stockentryform', [
			'stockEntry' => $this->getDatabase()->stock()->where('id', $args['entryId'])->fetch(),
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function StockEntryGrocycodeImage(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$size = $request->getQueryParam('size', null);

		$stockEntry = $this->getDatabase()->stock()->where('id', $args['entryId'])->fetch();
		$gc = new Grocycode(Grocycode::PRODUCT, $stockEntry->product_id, [$stockEntry->stock_id]);

		// Explicitly suppress errors, otherwise deprecations warnings would cause invalid PNG data
		// See also https://github.com/jucksearm/php-barcode/issues/3
		$png = @(new DatamatrixFactory())->setCode((string) $gc)->setSize($size)->getDatamatrixPngData();

		$isDownload = $request->getQueryParam('download', false);

		if ($isDownload)
		{
			$response = $response->withHeader('Content-Type', 'application/octet-stream')
			->withHeader('Content-Disposition', 'attachment; filename=grocycode.png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		else
		{
			$response = $response->withHeader('Content-Type', 'image/png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		$response->getBody()->write($png);
		return $response;
	}

	public function StockEntryGrocycodeLabel(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$stockEntry = $this->getDatabase()->stock()->where('id', $args['entryId'])->fetch();
		return $this->renderPage($request, $response, 'stockentrylabel', [
			'stockEntry' => $stockEntry,
			'product' => $this->getDatabase()->products($stockEntry->product_id),
		]);
	}

	public function StockSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'stocksettings', [
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE')
		]);
	}


	public function Stockentries(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['stock_due_soon_days'];

		return $this->renderPage($request, $response, 'stockentries', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'shoppinglocations' => $this->getDatabase()->shopping_locations()->orderBy('name', 'COLLATE NOCASE'),
			'stockEntries' => $this->getDatabase()->stock()->orderBy('product_id'),
			'currentStockLocations' => $this->getStockService()->GetCurrentStockLocations(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('products'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('products')
		]);
	}

	public function Transfer(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'transfer', [
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'barcodes' => $this->getDatabase()->product_barcodes_comma_separated(),
			'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
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

		$usersService = $this->getUsersService();
		return $this->renderPage($request, $response, 'stockjournalsummary', [
			'entries' => $entries,
			'products' => $this->getDatabase()->products()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'users' => $usersService->GetUsersAsDto(),
			'transactionTypes' => GetClassConstants('\Grocy\Services\StockService', 'TRANSACTION_TYPE_')
		]);
	}
}
