<?php

namespace Grocy\Controllers;

use Grocy\Services\StockService;

class StockController extends BaseController
{

	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function Overview($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'stockoverview', [
			'title' => 'Stock overview',
			'contentPage' => 'stockoverview.php',
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'currentStock' => $this->StockService->GetCurrentStock(),
			'missingProducts' => $this->StockService->GetMissingProducts()
		]);
	}

	public function Purchase($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'purchase', [
			'title' => 'Purchase',
			'contentPage' => 'purchase.php',
			'products' => $this->Database->products()
		]);
	}

	public function Consume($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'consume', [
			'title' => 'Consume',
			'contentPage' => 'consume.php',
			'products' => $this->Database->products()
		]);
	}

	public function Inventory($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'inventory', [
			'title' => 'Inventory',
			'contentPage' => 'inventory.php',
			'products' => $this->Database->products()
		]);
	}

	public function ShoppingList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'shoppinglist', [
			'title' => 'Shopping list',
			'contentPage' => 'shoppinglist.php',
			'listItems' => $this->Database->shopping_list(),
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'missingProducts' => $this->StockService->GetMissingProducts()
		]);
	}

	public function ProductsList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'products', [
			'title' => 'Products',
			'contentPage' => 'products.php',
			'products' => $this->Database->products(),
			'locations' => $this->Database->locations(),
			'quantityunits' => $this->Database->quantity_units()
		]);
	}

	public function LocationsList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'locations', [
			'title' => 'Locations',
			'contentPage' => 'locations.php',
			'locations' => $this->Database->locations()
		]);
	}

	public function QuantityUnitsList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'quantityunits', [
			'title' => 'Quantity units',
			'contentPage' => 'quantityunits.php',
			'quantityunits' => $this->Database->quantity_units()
		]);
	}

	public function ProductEditForm($request, $response, $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'title' => 'Create product',
				'contentPage' => 'productform.php',
				'locations' =>  $this->Database->locations(),
				'quantityunits' =>  $this->Database->quantity_units(),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'title' => 'Edit product',
				'contentPage' => 'productform.php',
				'product' =>  $this->Database->products($args['productId']),
				'locations' =>  $this->Database->locations(),
				'quantityunits' =>  $this->Database->quantity_units(),
				'mode' => 'edit'
			]);
		}
	}

	public function LocationEditForm($request, $response, $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'title' => 'Create location',
				'contentPage' => 'locationform.php',
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'title' => 'Edit location',
				'contentPage' => 'locationform.php',
				'location' =>  $this->Database->locations($args['locationId']),
				'mode' => 'edit'
			]);
		}
	}

	public function QuantityUnitEditForm($request, $response, $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'title' => 'Create quantity unit',
				'contentPage' => 'quantityunitform.php',
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'title' => 'Edit quantity unit',
				'contentPage' => 'quantityunitform.php',
				'quantityunit' =>  $this->Database->quantity_units($args['quantityunitId']),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListItemEditForm($request, $response, $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'title' => 'Add shopping list item',
				'contentPage' => 'shoppinglistform.php',
				'products' =>  $this->Database->products(),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'title' => 'Edit shopping list item',
				'contentPage' => 'shoppinglistform.php',
				'listItem' =>  $this->Database->shopping_list($args['itemId']),
				'products' =>  $this->Database->products(),
				'mode' => 'edit'
			]);
		}
	}
}
