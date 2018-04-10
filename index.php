<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/data/config.php';
require_once __DIR__ . '/services/ApplicationService.php';
require_once __DIR__ . '/services/DatabaseService.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/GrocyDbMigrator.php';
require_once __DIR__ . '/GrocyDemoDataGenerator.php';
require_once __DIR__ . '/services/StockService.php';
require_once __DIR__ . '/services/HabitsService.php';
require_once __DIR__ . '/services/BatteriesService.php';
require_once __DIR__ . '/GrocyPhpHelper.php';

$app = new \Slim\App;

if (PHP_SAPI !== 'cli')
{
	$app = new \Slim\App(new \Slim\Container([
		'settings' => [
			'displayErrorDetails' => true,
			'determineRouteBeforeAppMiddleware' => true
		],
	]));
	$container = $app->getContainer();
	$container['renderer'] = new PhpRenderer('./views');
}

if (PHP_SAPI === 'cli')
{
	$app->add(new \pavlakis\cli\CliRequest());
}

if (!ApplicationService::IsDemoInstallation())
{
	$sessionMiddleware = function(Request $request, Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		if ((!isset($_COOKIE['grocy_session']) || !SessionService::IsValidSession($_COOKIE['grocy_session'])) && $routeName !== 'login')
		{
			$response = $response->withRedirect('/login');
		}
		else
		{
			$response = $next($request, $response);
		}

		return $response;
	};

	$app->add($sessionMiddleware);
}

$db = DatabaseService::GetDbConnection();

$app->get('/login', function(Request $request, Response $response)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Login',
		'contentPage' => 'login.php'
	]);
})->setName('login');

$app->post('/login', function(Request $request, Response $response)
{
	$postParams = $request->getParsedBody();
	if (isset($postParams['username']) && isset($postParams['password']))
	{
		if ($postParams['username'] === HTTP_USER && $postParams['password'] === HTTP_PASSWORD)
		{
			$sessionKey = SessionService::CreateSession();
			setcookie('grocy_session', $sessionKey, time()+2592000); //30 days

			return $response->withRedirect('/');
		}
		else
		{
			return $response->withRedirect('/login?invalid=true');
		}
	}
	else
	{
		return $response->withRedirect('/login?invalid=true');
	}
})->setName('login');

$app->get('/logout', function(Request $request, Response $response)
{
	SessionService::RemoveSession($_COOKIE['grocy_session']);
	return $response->withRedirect('/');
});

$app->get('/', function(Request $request, Response $response) use($db)
{
	$db = DatabaseService::GetDbConnection(true); //For database schema migration

	return $response->withRedirect('/stockoverview');
});

$app->get('/stockoverview', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Stock overview',
		'contentPage' => 'stockoverview.php',
		'products' => $db->products(),
		'quantityunits' => $db->quantity_units(),
		'currentStock' => StockService::GetCurrentStock(),
		'missingProducts' => StockService::GetMissingProducts()
	]);
});

$app->get('/habitsoverview', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Habits overview',
		'contentPage' => 'habitsoverview.php',
		'habits' => $db->habits(),
		'currentHabits' => HabitsService::GetCurrentHabits(),
	]);
});

$app->get('/batteriesoverview', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Batteries overview',
		'contentPage' => 'batteriesoverview.php',
		'batteries' => $db->batteries(),
		'current' => BatteriesService::GetCurrent(),
	]);
});

$app->get('/purchase', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Purchase',
		'contentPage' => 'purchase.php',
		'products' => $db->products()
	]);
});

$app->get('/consume', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Consume',
		'contentPage' => 'consume.php',
		'products' => $db->products()
	]);
});

$app->get('/inventory', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Inventory',
		'contentPage' => 'inventory.php',
		'products' => $db->products()
	]);
});

$app->get('/shoppinglist', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Shopping list',
		'contentPage' => 'shoppinglist.php',
		'listItems' => $db->shopping_list(),
		'products' => $db->products(),
		'quantityunits' => $db->quantity_units(),
		'missingProducts' => StockService::GetMissingProducts()
	]);
});

$app->get('/habittracking', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Habit tracking',
		'contentPage' => 'habittracking.php',
		'habits' => $db->habits()
	]);
});

$app->get('/batterytracking', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Battery tracking',
		'contentPage' => 'batterytracking.php',
		'batteries' => $db->batteries()
	]);
});

$app->get('/products', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Products',
		'contentPage' => 'products.php',
		'products' => $db->products(),
		'locations' => $db->locations(),
		'quantityunits' => $db->quantity_units()
	]);
});

$app->get('/locations', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Locations',
		'contentPage' => 'locations.php',
		'locations' => $db->locations()
	]);
});

$app->get('/quantityunits', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Quantity units',
		'contentPage' => 'quantityunits.php',
		'quantityunits' => $db->quantity_units()
	]);
});

$app->get('/habits', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Habits',
		'contentPage' => 'habits.php',
		'habits' => $db->habits()
	]);
});

$app->get('/batteries', function(Request $request, Response $response) use($db)
{
	return $this->renderer->render($response, 'layout/default.php', [
		'title' => 'Batteries',
		'contentPage' => 'batteries.php',
		'batteries' => $db->batteries()
	]);
});


$app->get('/product/{productId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['productId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Create product',
			'contentPage' => 'productform.php',
			'locations' => $db->locations(),
			'quantityunits' => $db->quantity_units(),
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit product',
			'contentPage' => 'productform.php',
			'product' => $db->products($args['productId']),
			'locations' => $db->locations(),
			'quantityunits' => $db->quantity_units(),
			'mode' => 'edit'
		]);
	}
});

$app->get('/location/{locationId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['locationId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Create location',
			'contentPage' => 'locationform.php',
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit location',
			'contentPage' => 'locationform.php',
			'location' => $db->locations($args['locationId']),
			'mode' => 'edit'
		]);
	}
});

$app->get('/quantityunit/{quantityunitId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['quantityunitId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Create quantity unit',
			'contentPage' => 'quantityunitform.php',
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit quantity unit',
			'contentPage' => 'quantityunitform.php',
			'quantityunit' => $db->quantity_units($args['quantityunitId']),
			'mode' => 'edit'
		]);
	}
});

$app->get('/habit/{habitId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['habitId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Create habit',
			'contentPage' => 'habitform.php',
			'periodTypes' => GrocyPhpHelper::GetClassConstants('HabitsService'),
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit habit',
			'contentPage' => 'habitform.php',
			'habit' => $db->habits($args['habitId']),
			'periodTypes' => GrocyPhpHelper::GetClassConstants('HabitsService'),
			'mode' => 'edit'
		]);
	}
});

$app->get('/battery/{batteryId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['batteryId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Create battery',
			'contentPage' => 'batteryform.php',
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit battery',
			'contentPage' => 'batteryform.php',
			'battery' => $db->batteries($args['batteryId']),
			'mode' => 'edit'
		]);
	}
});

$app->get('/shoppinglistitem/{itemId}', function(Request $request, Response $response, $args) use($db)
{
	if ($args['itemId'] == 'new')
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Add shopping list item',
			'contentPage' => 'shoppinglistform.php',
			'products' => $db->products(),
			'mode' => 'create'
		]);
	}
	else
	{
		return $this->renderer->render($response, 'layout/default.php', [
			'title' => 'Edit shopping list item',
			'contentPage' => 'shoppinglistform.php',
			'listItem' => $db->shopping_list($args['itemId']),
			'products' => $db->products(),
			'mode' => 'edit'
		]);
	}
});

$app->group('/api', function() use($db)
{
	$this->get('/get-objects/{entity}', function(Request $request, Response $response, $args) use($db)
	{
		echo json_encode($db->{$args['entity']}());
	});

	$this->get('/get-object/{entity}/{objectId}', function(Request $request, Response $response, $args) use($db)
	{
		echo json_encode($db->{$args['entity']}($args['objectId']));
	});

	$this->post('/add-object/{entity}', function(Request $request, Response $response, $args) use($db)
	{
		$newRow = $db->{$args['entity']}()->createRow($request->getParsedBody());
		$newRow->save();
		$success = $newRow->isClean();
		echo json_encode(array('success' => $success));
	});

	$this->post('/edit-object/{entity}/{objectId}', function(Request $request, Response $response, $args) use($db)
	{
		$row = $db->{$args['entity']}($args['objectId']);
		$row->update($request->getParsedBody());
		$success = $row->isClean();
		echo json_encode(array('success' => $success));
	});

	$this->get('/delete-object/{entity}/{objectId}', function(Request $request, Response $response, $args) use($db)
	{
		$row = $db->{$args['entity']}($args['objectId']);
		$row->delete();
		$success = $row->isClean();
		echo json_encode(array('success' => $success));
	});

	$this->get('/stock/add-product/{productId}/{amount}', function(Request $request, Response $response, $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		$transactionType = StockService::TRANSACTION_TYPE_PURCHASE;
		if (isset($request->getQueryParams()['transactiontype']) && !empty($request->getQueryParams()['transactiontype']))
		{
			$transactionType = $request->getQueryParams()['transactiontype'];
		}

		echo json_encode(array('success' => StockService::AddProduct($args['productId'], $args['amount'], $bestBeforeDate, $transactionType)));
	});

	$this->get('/stock/consume-product/{productId}/{amount}', function(Request $request, Response $response, $args)
	{
		$spoiled = false;
		if (isset($request->getQueryParams()['spoiled']) && !empty($request->getQueryParams()['spoiled']) && $request->getQueryParams()['spoiled'] == '1')
		{
			$spoiled = true;
		}

		$transactionType = StockService::TRANSACTION_TYPE_CONSUME;
		if (isset($request->getQueryParams()['transactiontype']) && !empty($request->getQueryParams()['transactiontype']))
		{
			$transactionType = $request->getQueryParams()['transactiontype'];
		}

		echo json_encode(array('success' => StockService::ConsumeProduct($args['productId'], $args['amount'], $spoiled, $transactionType)));
	});

	$this->get('/stock/inventory-product/{productId}/{newAmount}', function(Request $request, Response $response, $args)
	{
		$bestBeforeDate = date('Y-m-d');
		if (isset($request->getQueryParams()['bestbeforedate']) && !empty($request->getQueryParams()['bestbeforedate']))
		{
			$bestBeforeDate = $request->getQueryParams()['bestbeforedate'];
		}

		echo json_encode(array('success' => StockService::InventoryProduct($args['productId'], $args['newAmount'], $bestBeforeDate)));
	});

	$this->get('/stock/get-product-details/{productId}', function(Request $request, Response $response, $args)
	{
		echo json_encode(StockService::GetProductDetails($args['productId']));
	});

	$this->get('/stock/get-current-stock', function(Request $request, Response $response)
	{
		echo json_encode(StockService::GetCurrentStock());
	});

	$this->get('/stock/add-missing-products-to-shoppinglist', function(Request $request, Response $response)
	{
		StockService::AddMissingProductsToShoppingList();
		echo json_encode(array('success' => true));
	});

	$this->get('/habits/track-habit/{habitId}', function(Request $request, Response $response, $args)
	{
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		echo json_encode(array('success' => HabitsService::TrackHabit($args['habitId'], $trackedTime)));
	});

	$this->get('/habits/get-habit-details/{habitId}', function(Request $request, Response $response, $args)
	{
		echo json_encode(HabitsService::GetHabitDetails($args['habitId']));
	});

	$this->get('/batteries/track-charge-cycle/{batteryId}', function(Request $request, Response $response, $args)
	{
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		echo json_encode(array('success' => BatteriesService::TrackChargeCycle($args['batteryId'], $trackedTime)));
	});

	$this->get('/batteries/get-battery-details/{batteryId}', function(Request $request, Response $response, $args)
	{
		echo json_encode(BatteriesService::GetBatteryDetails($args['batteryId']));
	});
})->add(function($request, $response, $next)
{
	$response = $next($request, $response);
	return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/cli', function()
{
	$this->get('/recreatedemo', function(Request $request, Response $response)
	{
		if (ApplicationService::IsDemoInstallation())
		{
			GrocyDemoDataGenerator::RecreateDemo();
		}
	});
})->add(function($request, $response, $next)
{
	$response = $next($request, $response);

	if (PHP_SAPI !== 'cli')
	{
		echo 'Please call this only from CLI';
		return $response->withHeader('Content-Type', 'text/plain')->withStatus(400);
	}

	return $response->withHeader('Content-Type', 'text/plain');
});

$app->run();
