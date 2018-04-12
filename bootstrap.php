<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Grocy\Middleware\SessionAuthMiddleware;
use \Grocy\Middleware\JsonMiddleware;
use \Grocy\Middleware\CliMiddleware;

use \Grocy\Services\ApplicationService;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/data/config.php';
require_once __DIR__ . '/extensions.php';

// Setup base application
if (PHP_SAPI !== 'cli')
{
	$appContainer = new \Slim\Container([
		'settings' => [
			'displayErrorDetails' => true,
			'determineRouteBeforeAppMiddleware' => true
		],
		'view' => function($container)
		{
			return new \Slim\Views\Blade(__DIR__ . '/views', __DIR__ . '/data/viewcache');
		}
	]);

	$app = new \Slim\App($appContainer);
}
else
{
	$app = new \Slim\App();
	$app->add(\pavlakis\cli\CliRequest::class);
}

// Add session handling if this is not a demo installation
$applicationService = new ApplicationService();
if (!$applicationService->IsDemoInstallation())
{
	$app->add(SessionAuthMiddleware::class);
}

// Base route
$app->get('/', 'Grocy\Controllers\LoginController:Root');

// Login routes
$app->get('/login', 'Grocy\Controllers\LoginController:LoginPage')->setName('login');
$app->post('/login', 'Grocy\Controllers\LoginController:ProcessLogin')->setName('login');
$app->get('/logout', 'Grocy\Controllers\LoginController:Logout');

// Stock routes
$app->get('/stockoverview', 'Grocy\Controllers\StockController:Overview');
$app->get('/purchase', 'Grocy\Controllers\StockController:Purchase');
$app->get('/consume', 'Grocy\Controllers\StockController:Consume');
$app->get('/inventory', 'Grocy\Controllers\StockController:Inventory');

$app->get('/products', 'Grocy\Controllers\StockController:ProductsList');
$app->get('/product/{productId}', 'Grocy\Controllers\StockController:ProductEditForm');

$app->get('/locations', 'Grocy\Controllers\StockController:LocationsList');
$app->get('/location/{locationId}', 'Grocy\Controllers\StockController:LocationEditForm');

$app->get('/quantityunits', 'Grocy\Controllers\StockController:QuantityUnitsList');
$app->get('/quantityunit/{quantityunitId}', 'Grocy\Controllers\StockController:QuantityUnitEditForm');

$app->get('/shoppinglist', 'Grocy\Controllers\StockController:ShoppingList');
$app->get('/shoppinglistitem/{itemId}', 'Grocy\Controllers\StockController:ShoppingListItemEditForm');


// Habit routes
$app->get('/habitsoverview', 'Grocy\Controllers\HabitsController:Overview');
$app->get('/habittracking', 'Grocy\Controllers\HabitsController:TrackHabitExecution');

$app->get('/habits', 'Grocy\Controllers\HabitsController:HabitsList');
$app->get('/habit/{habitId}', 'Grocy\Controllers\HabitsController:HabitEditForm');

// Batterry routes
$app->get('/batteriesoverview', 'Grocy\Controllers\BatteriesController:Overview');
$app->get('/batterytracking', 'Grocy\Controllers\BatteriesController:TrackChargeCycle');

$app->get('/batteries', 'Grocy\Controllers\BatteriesController:BatteriesList');
$app->get('/battery/{batteryId}', 'Grocy\Controllers\BatteriesController:BatteryEditForm');


$app->group('/api', function()
{
	$this->get('/get-objects/{entity}', 'Grocy\Controllers\GenericEntityApiController:GetObjects');
	$this->get('/get-object/{entity}/{objectId}', 'Grocy\Controllers\GenericEntityApiController:GetObject');
	$this->post('/add-object/{entity}', 'Grocy\Controllers\GenericEntityApiController:AddObject');
	$this->post('/edit-object/{entity}/{objectId}', 'Grocy\Controllers\GenericEntityApiController:EditObject');
	$this->get('/delete-object/{entity}/{objectId}', 'Grocy\Controllers\GenericEntityApiController:DeleteObject');

	$this->get('/stock/add-product/{productId}/{amount}', 'Grocy\Controllers\StockApiController:AddProduct');
	$this->get('/stock/consume-product/{productId}/{amount}', 'Grocy\Controllers\StockApiController:ConsumeProduct');
	$this->get('/stock/inventory-product/{productId}/{newAmount}', 'Grocy\Controllers\StockApiController:InventoryProduct');
	$this->get('/stock/get-product-details/{productId}', 'Grocy\Controllers\StockApiController:ProductDetails');
	$this->get('/stock/get-current-stock', 'Grocy\Controllers\StockApiController:CurrentStock');
	$this->get('/stock/add-missing-products-to-shoppinglist', 'Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');

	$this->get('/habits/track-habit-execution/{habitId}', 'Grocy\Controllers\HabitsApiController:TrackHabitExecution');
	$this->get('/habits/get-habit-details/{habitId}', 'Grocy\Controllers\HabitsApiController:HabitDetails');
	
	$this->get('/batteries/track-charge-cycle/{batteryId}', 'Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
	$this->get('/batteries/get-battery-details/{batteryId}', 'Grocy\Controllers\BatteriesApiController:BatteryDetails');
})->add(JsonMiddleware::class);

$app->group('/cli', function()
{
	$this->get('/recreatedemo', 'Grocy\Controllers\CliController:RecreateDemo');
})->add(CliMiddleware::class);

$app->run();
