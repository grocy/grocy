<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'grocy.php';

$app = new \Slim\App;
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
		'currentStock' => Grocy::GetCurrentStock()
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

	$this->get('/get-product-statistics/{productId}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		$product = $db->products($args['productId']);
		$productStockAmount = $db->stock()->where('product_id', $args['productId'])->sum('amount');
		$productLastPurchased = $db->stock()->where('product_id', $args['productId'])->max('purchased_date');
		$productLastUsed = $db->consumptions()->where('product_id', $args['productId'])->max('used_date');
		$quPurchase = $db->quantity_units($product->qu_id_purchase);
		$quStock = $db->quantity_units($product->qu_id_stock);

		echo json_encode(array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $productStockAmount,
			'quantity_unit_purchase' => $quPurchase,
			'quantity_unit_stock' => $quStock
		));

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/get-current-stock', function(Request $request, Response $response)
	{
		echo json_encode(Grocy::GetCurrentStock());

		return $response->withHeader('Content-Type', 'application/json');
	});

	$this->get('/consume-product/{productId}/{amount}', function(Request $request, Response $response, $args)
	{
		$db = Grocy::GetDbConnection();
		$productStockAmount = $db->stock()->where('product_id', $args['productId'])->sum('amount');
		$potentialStockEntries = $db->stock()->where('product_id', $args['productId'])->orderBy('purchased_date', 'ASC')->fetchAll(); //FIFO
		$amount = $args['amount'];

		if ($amount > $productStockAmount)
		{
			echo json_encode(array('success' => false));
			return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
		}

		$spoiled = 0;
		if (isset($request->getQueryParams()['spoiled']) && !empty($request->getQueryParams()['spoiled']) && $request->getQueryParams()['spoiled'] == '1')
		{
			$spoiled = 1;
		}

		foreach ($potentialStockEntries as $stockEntry)
		{
			if ($amount == 0)
			{
				break;
			}

			if ($amount >= $stockEntry->amount) //Take the whole stock entry
			{
				$newRow = $db->consumptions()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'spoiled' => $spoiled
				));
				$newRow->save();

				$stockEntry->delete();
			}
			else //Split the stock entry resp. update the amount
			{
				$newRow = $db->consumptions()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'spoiled' => $spoiled
				));
				$newRow->save();

				$restStockAmount = $stockEntry->amount - $amount;
				$stockEntry->update(array(
					'amount' => $restStockAmount
				));
			}

			$amount -= $stockEntry->amount;
		}

		echo json_encode(array('success' => true));
		return $response->withHeader('Content-Type', 'application/json');
	});
});

$app->run();
