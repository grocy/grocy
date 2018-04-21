<?php

use \Grocy\Middleware\JsonMiddleware;
use \Grocy\Middleware\CliMiddleware;
use \Grocy\Middleware\SessionAuthMiddleware;
use \Grocy\Middleware\ApiKeyAuthMiddleware;
use \Tuupola\Middleware\CorsMiddleware;

$app->group('', function()
{
	// Base route
	$this->get('/', 'LoginControllerInstance:Root')->setName('root');

	// Login routes
	$this->get('/login', 'LoginControllerInstance:LoginPage')->setName('login');
	$this->post('/login', 'LoginControllerInstance:ProcessLogin')->setName('login');
	$this->get('/logout', 'LoginControllerInstance:Logout');

	// Stock routes
	$this->get('/stockoverview', 'Grocy\Controllers\StockController:Overview');
	$this->get('/purchase', 'Grocy\Controllers\StockController:Purchase');
	$this->get('/consume', 'Grocy\Controllers\StockController:Consume');
	$this->get('/inventory', 'Grocy\Controllers\StockController:Inventory');

	$this->get('/products', 'Grocy\Controllers\StockController:ProductsList');
	$this->get('/product/{productId}', 'Grocy\Controllers\StockController:ProductEditForm');

	$this->get('/locations', 'Grocy\Controllers\StockController:LocationsList');
	$this->get('/location/{locationId}', 'Grocy\Controllers\StockController:LocationEditForm');

	$this->get('/quantityunits', 'Grocy\Controllers\StockController:QuantityUnitsList');
	$this->get('/quantityunit/{quantityunitId}', 'Grocy\Controllers\StockController:QuantityUnitEditForm');

	$this->get('/shoppinglist', 'Grocy\Controllers\StockController:ShoppingList');
	$this->get('/shoppinglistitem/{itemId}', 'Grocy\Controllers\StockController:ShoppingListItemEditForm');

	// Habit routes
	$this->get('/habitsoverview', 'Grocy\Controllers\HabitsController:Overview');
	$this->get('/habittracking', 'Grocy\Controllers\HabitsController:TrackHabitExecution');

	$this->get('/habits', 'Grocy\Controllers\HabitsController:HabitsList');
	$this->get('/habit/{habitId}', 'Grocy\Controllers\HabitsController:HabitEditForm');

	// Batterry routes
	$this->get('/batteriesoverview', 'Grocy\Controllers\BatteriesController:Overview');
	$this->get('/batterytracking', 'Grocy\Controllers\BatteriesController:TrackChargeCycle');

	$this->get('/batteries', 'Grocy\Controllers\BatteriesController:BatteriesList');
	$this->get('/battery/{batteryId}', 'Grocy\Controllers\BatteriesController:BatteryEditForm');

	// Other routes
	$this->get('/api', 'Grocy\Controllers\OpenApiController:DocumentationUi');
	$this->get('/manageapikeys', 'Grocy\Controllers\OpenApiController:ApiKeysList');
	$this->get('/manageapikeys/new', 'Grocy\Controllers\OpenApiController:CreateNewApiKey');
})->add(new SessionAuthMiddleware($appContainer, $appContainer->LoginControllerInstance->GetSessionCookieName()));

$app->group('/api', function()
{
	$this->get('/get-openapi-specification', 'Grocy\Controllers\OpenApiController:DocumentationSpec');

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
})->add(new ApiKeyAuthMiddleware($appContainer, $appContainer->LoginControllerInstance->GetSessionCookieName(), $appContainer->ApiKeyHeaderName))
->add(JsonMiddleware::class)
->add(new CorsMiddleware([
	'origin' => ["*"],
	'methods' => ["GET", "POST"],
	'headers.allow' => [ $appContainer->ApiKeyHeaderName ],
	'headers.expose' => [ ],
	'credentials' => false,
	'cache' => 0,
]));

$app->group('/cli', function()
{
	$this->get('/recreatedemo', 'Grocy\Controllers\CliController:RecreateDemo');
})->add(CliMiddleware::class);
