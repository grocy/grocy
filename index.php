<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'Grocy.php';
require_once 'GrocyDbMigrator.php';
require_once 'GrocyDemoDataGenerator.php';
require_once 'GrocyLogicStock.php';
require_once 'GrocyPhpHelper.php';

$app = new \Slim\App(new \Slim\Container([
	'settings' => [
		'displayErrorDetails' => true,
	],
]));
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer('./views');

if (!Grocy::IsDemoInstallation())
{
	$isHttpsReverseProxied = !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
	$app->add(new \Slim\Middleware\HttpBasicAuthentication([
		'realm' => 'grocy',
		'secure' => !$isHttpsReverseProxied,
		'users' => [
			HTTP_USER => HTTP_PASSWORD
		]
	]));
}

$app->get('/', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Dashboard',
		'contentPage' => 'dashboard.php',
		'products' => $db->products(),
		'currentStock' => GrocyLogicStock::GetCurrentStock()
	]);
});

$app->get('/purchase', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Purchase',
		'contentPage' => 'purchase.php',
		'products' => $db->products()
	]);
});

$app->get('/consumption', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Consumption',
		'contentPage' => 'consumption.php',
		'products' => $db->products()
	]);
});

$app->get('/products', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Products',
		'contentPage' => 'products.php',
		'products' => $db->products(),
		'locations' => $db->locations(),
		'quantityunits' => $db->quantity_units()
	]);
});

$app->get('/locations', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Locations',
		'contentPage' => 'locations.php',
		'locations' => $db->locations()
	]);
});

$app->get('/quantityunits', function(Request $request, Response $response)
{
	$db = Grocy::GetDbConnection();

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Quantity units',
		'contentPage' => 'quantityunits.php',
		'quantityunits' => $db->quantity_units()
	]);
});

$app->get('/product/{productId}', function(Request $request, Response $response, $args)
{
	$db = Grocy::GetDbConnection();

	if ($args['productId'] == 'new')
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Create product',
			'contentPage' => 'productform.php',
			'locations' => $db->locations(),
			'quantityunits' => $db->quantity_units(),
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Edit product',
			'contentPage' => 'productform.php',
			'product' => $db->products($args['productId']),
			'locations' => $db->locations(),
			'quantityunits' => $db->quantity_units(),
			'mode' => 'edit'
		]);
	}
});

$app->get('/location/{locationId}', function(Request $request, Response $response, $args)
{
	$db = Grocy::GetDbConnection();

	if ($args['locationId'] == 'new')
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Create location',
			'contentPage' => 'locationform.php',
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Edit location',
			'contentPage' => 'locationform.php',
			'location' => $db->locations($args['locationId']),
			'mode' => 'edit'
		]);
	}
});

$app->get('/quantityunit/{quantityunitId}', function(Request $request, Response $response, $args)
{
	$db = Grocy::GetDbConnection();

	if ($args['quantityunitId'] == 'new')
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Create quantity unit',
			'contentPage' => 'quantityunitform.php',
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, '/layout.php', [
			'title' => 'Edit quantity unit',
			'contentPage' => 'quantityunitform.php',
			'quantityunit' => $db->quantity_units($args['quantityunitId']),
			'mode' => 'edit'
		]);
	}
});

$app->group('/api', function()
{
	$this->get('/get-objects/{entity}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		echo json_encode($db->{$args['entity']}());

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/get-object/{entity}/{objectId}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		echo json_encode($db->{$args['entity']}($args['objectId']));

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->post('/add-object/{entity}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		$newRow = $db->{$args['entity']}()->createRow($request->getParsedBody());
		$newRow->save();
		$success = $newRow->isClean();
		echo json_encode(array('success' => $success));

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->post('/edit-object/{entity}/{objectId}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		$row = $db->{$args['entity']}($args['objectId']);
		$row->update($request->getParsedBody());
		$success = $row->isClean();
		echo json_encode(array('success' => $success));

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/delete-object/{entity}/{objectId}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		$row = $db->{$args['entity']}($args['objectId']);
		$row->delete();
		$success = $row->isClean();
		echo json_encode(array('success' => $success));

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/stock/get-product-details/{productId}', function(Request $request, Response $response, $args)
	{
		echo json_encode(GrocyLogicStock::GetProductDetails($args['productId']));
		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/stock/get-current-stock', function(Request $request, Response $response)
	{
		echo json_encode(GrocyLogicStock::GetCurrentStock());
		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/stock/consume-product/{productId}/{amount}', function(Request $request, Response $response, $args)
	{
		$spoiled = false;
		if (isset($request->getQueryParams()['spoiled']) && !empty($request->getQueryParams()['spoiled']) && $request->getQueryParams()['spoiled'] == '1')
		{
			$spoiled = true;
		}

		echo json_encode(array('success' => GrocyLogicStock::ConsumeProduct($args['productId'], $args['amount'], $spoiled)));
		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/helper/uniqid', function(Request $request, Response $response)
	{
		echo json_encode(array('uniqid' => uniqid()));
		return $response->withHeader('Content-Type', 'application/json');
	});
});

$app->run();
