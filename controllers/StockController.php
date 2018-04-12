<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;

class StockController extends BaseController
{

	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'stockoverview', [
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'currentStock' => $this->StockService->GetCurrentStock(),
			'missingProducts' => $this->StockService->GetMissingProducts()
		]);
	}

	public function Purchase(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'purchase', [
			'products' => $this->Database->products()
		]);
	}

	public function Consume(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'consume', [
			'products' => $this->Database->products()
		]);
	}

	public function Inventory(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'inventory', [
			'products' => $this->Database->products()
		]);
	}

	public function ShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'shoppinglist', [
			'listItems' => $this->Database->shopping_list(),
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'missingProducts' => $this->StockService->GetMissingProducts()
		]);
	}

	public function ProductsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'products', [
			'products' => $this->Database->products(),
			'locations' => $this->Database->locations(),
			'quantityunits' => $this->Database->quantity_units()
		]);
	}

	public function LocationsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'locations', [
			'locations' => $this->Database->locations()
		]);
	}

	public function QuantityUnitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'quantityunits', [
			'quantityunits' => $this->Database->quantity_units()
		]);
	}

	public function ProductEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'locations' =>  $this->Database->locations(),
				'quantityunits' =>  $this->Database->quantity_units(),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'productform', [
				'product' =>  $this->Database->products($args['productId']),
				'locations' =>  $this->Database->locations(),
				'quantityunits' =>  $this->Database->quantity_units(),
				'mode' => 'edit'
			]);
		}
	}

	public function LocationEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['locationId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'locationform', [
				'location' =>  $this->Database->locations($args['locationId']),
				'mode' => 'edit'
			]);
		}
	}

	public function QuantityUnitEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['quantityunitId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'quantityunitform', [
				'quantityunit' =>  $this->Database->quantity_units($args['quantityunitId']),
				'mode' => 'edit'
			]);
		}
	}

	public function ShoppingListItemEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['itemId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'products' =>  $this->Database->products(),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'listItem' =>  $this->Database->shopping_list($args['itemId']),
				'products' =>  $this->Database->products(),
				'mode' => 'edit'
			]);
		}
	}
}
