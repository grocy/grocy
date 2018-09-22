<?php

use \Grocy\Middleware\JsonMiddleware;
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

	// User routes
	$this->get('/users', '\Grocy\Controllers\UsersController:UsersList');
	$this->get('/user/{userId}', '\Grocy\Controllers\UsersController:UserEditForm');

	// Stock routes
	$this->get('/stockoverview', '\Grocy\Controllers\StockController:Overview');
	$this->get('/purchase', '\Grocy\Controllers\StockController:Purchase');
	$this->get('/consume', '\Grocy\Controllers\StockController:Consume');
	$this->get('/inventory', '\Grocy\Controllers\StockController:Inventory');
	$this->get('/products', '\Grocy\Controllers\StockController:ProductsList');
	$this->get('/product/{productId}', '\Grocy\Controllers\StockController:ProductEditForm');
	$this->get('/locations', '\Grocy\Controllers\StockController:LocationsList');
	$this->get('/location/{locationId}', '\Grocy\Controllers\StockController:LocationEditForm');
	$this->get('/quantityunits', '\Grocy\Controllers\StockController:QuantityUnitsList');
	$this->get('/quantityunit/{quantityunitId}', '\Grocy\Controllers\StockController:QuantityUnitEditForm');
	$this->get('/shoppinglist', '\Grocy\Controllers\StockController:ShoppingList');
	$this->get('/shoppinglistitem/{itemId}', '\Grocy\Controllers\StockController:ShoppingListItemEditForm');

	// Recipe routes
	$this->get('/recipes', '\Grocy\Controllers\RecipesController:Overview');
	$this->get('/recipe/{recipeId}', '\Grocy\Controllers\RecipesController:RecipeEditForm');
	$this->get('/recipe/{recipeId}/pos/{recipePosId}', '\Grocy\Controllers\RecipesController:RecipePosEditForm');

	// Chore routes
	$this->get('/choresoverview', '\Grocy\Controllers\ChoresController:Overview');
	$this->get('/choretracking', '\Grocy\Controllers\ChoresController:TrackChoreExecution');
	$this->get('/choresanalysis', '\Grocy\Controllers\ChoresController:Analysis');

	$this->get('/chores', '\Grocy\Controllers\ChoresController:ChoresList');
	$this->get('/chore/{choreId}', '\Grocy\Controllers\ChoresController:ChoreEditForm');

	// Battery routes
	$this->get('/batteriesoverview', '\Grocy\Controllers\BatteriesController:Overview');
	$this->get('/batterytracking', '\Grocy\Controllers\BatteriesController:TrackChargeCycle');

	$this->get('/batteries', '\Grocy\Controllers\BatteriesController:BatteriesList');
	$this->get('/battery/{batteryId}', '\Grocy\Controllers\BatteriesController:BatteryEditForm');

	// OpenAPI routes
	$this->get('/api', '\Grocy\Controllers\OpenApiController:DocumentationUi');
	$this->get('/manageapikeys', '\Grocy\Controllers\OpenApiController:ApiKeysList');
	$this->get('/manageapikeys/new', '\Grocy\Controllers\OpenApiController:CreateNewApiKey');
})->add(new SessionAuthMiddleware($appContainer, $appContainer->LoginControllerInstance->GetSessionCookieName()));

$app->group('/api', function()
{
	// OpenAPI
	$this->get('/get-openapi-specification', '\Grocy\Controllers\OpenApiController:DocumentationSpec');

	// Generic entity interaction
	$this->get('/get-objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:GetObjects');
	$this->get('/get-object/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetObject');
	$this->post('/add-object/{entity}', '\Grocy\Controllers\GenericEntityApiController:AddObject');
	$this->post('/edit-object/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:EditObject');
	$this->get('/delete-object/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:DeleteObject');

	// Users
	$this->get('/users/get', '\Grocy\Controllers\UsersApiController:GetUsers');
	$this->post('/users/create', '\Grocy\Controllers\UsersApiController:CreateUser');
	$this->post('/users/edit/{userId}', '\Grocy\Controllers\UsersApiController:EditUser');
	$this->get('/users/delete/{userId}', '\Grocy\Controllers\UsersApiController:DeleteUser');

	// Stock
	$this->get('/stock/add-product/{productId}/{amount}', '\Grocy\Controllers\StockApiController:AddProduct');
	$this->get('/stock/consume-product/{productId}/{amount}', '\Grocy\Controllers\StockApiController:ConsumeProduct');
	$this->get('/stock/inventory-product/{productId}/{newAmount}', '\Grocy\Controllers\StockApiController:InventoryProduct');
	$this->get('/stock/get-product-details/{productId}', '\Grocy\Controllers\StockApiController:ProductDetails');
	$this->get('/stock/get-product-price-history/{productId}', '\Grocy\Controllers\StockApiController:ProductPriceHistory');
	$this->get('/stock/get-current-stock', '\Grocy\Controllers\StockApiController:CurrentStock');
	$this->get('/stock/get-current-volatil-stock', '\Grocy\Controllers\StockApiController:CurrentVolatilStock');
	$this->get('/stock/add-missing-products-to-shoppinglist', '\Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');
	$this->get('/stock/clear-shopping-list', '\Grocy\Controllers\StockApiController:ClearShoppingList');
	$this->get('/stock/external-barcode-lookup/{barcode}', '\Grocy\Controllers\StockApiController:ExternalBarcodeLookup');

	// Recipes
	$this->get('/recipes/add-not-fulfilled-products-to-shopping-list/{recipeId}', '\Grocy\Controllers\RecipesApiController:AddNotFulfilledProductsToShoppingList');
	$this->get('/recipes/consume-recipe/{recipeId}', '\Grocy\Controllers\RecipesApiController:ConsumeRecipe');

	// Chores
	$this->get('/chores/track-chore-execution/{choreId}', '\Grocy\Controllers\ChoresApiController:TrackChoreExecution');
	$this->get('/chores/get-chore-details/{choreId}', '\Grocy\Controllers\ChoresApiController:ChoreDetails');
	$this->get('/chores/get-current', '\Grocy\Controllers\ChoresApiController:Current');
	
	// Batteries
	$this->get('/batteries/track-charge-cycle/{batteryId}', '\Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
	$this->get('/batteries/get-battery-details/{batteryId}', '\Grocy\Controllers\BatteriesApiController:BatteryDetails');
	$this->get('/batteries/get-current', '\Grocy\Controllers\BatteriesApiController:Current');
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
