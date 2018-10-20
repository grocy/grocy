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
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'currentStock' => $this->StockService->GetCurrentStock(),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'nextXDays' => 5,
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function Purchase(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'purchase', [
			'products' => $this->Database->products()->orderBy('name')
		]);
	}

	public function Consume(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'consume', [
			'products' => $this->Database->products()->orderBy('name')
		]);
	}

	public function Inventory(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'inventory', [
			'products' => $this->Database->products()->orderBy('name')
		]);
	}

	public function ShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'shoppinglist', [
			'listItems' => $this->Database->shopping_list(),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'missingProducts' => $this->StockService->GetMissingProducts(),
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function ProductsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'products', [
			'products' => $this->Database->products()->orderBy('name'),
			'locations' => $this->Database->locations()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function LocationsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'locations', [
			'locations' => $this->Database->locations()->orderBy('name')
		]);
	}

	public function ProductGroupsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'productgroups', [
			'productGroups' => $this->Database->product_groups()->orderBy('name')
		]);
	}

	public function QuantityUnitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'quantityunits', [
			'quantityunits' => $this->Database->quantity_units()->orderBy('name')
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

	public function ProductGroupEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['productGroupId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'productgroupform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'productgroupform', [
				'group' =>  $this->Database->product_groups($args['productGroupId']),
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
				'products' =>  $this->Database->products()->orderBy('name'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'shoppinglistform', [
				'listItem' =>  $this->Database->shopping_list($args['itemId']),
				'products' =>  $this->Database->products()->orderBy('name'),
				'mode' => 'edit'
			]);
		}
	}
}
