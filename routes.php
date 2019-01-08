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
	$this->get('/stocksettings', '\Grocy\Controllers\StockController:StockSettings');
	$this->get('/locations', '\Grocy\Controllers\StockController:LocationsList');
	$this->get('/location/{locationId}', '\Grocy\Controllers\StockController:LocationEditForm');
	$this->get('/quantityunits', '\Grocy\Controllers\StockController:QuantityUnitsList');
	$this->get('/quantityunit/{quantityunitId}', '\Grocy\Controllers\StockController:QuantityUnitEditForm');
	$this->get('/productgroups', '\Grocy\Controllers\StockController:ProductGroupsList');
	$this->get('/productgroup/{productGroupId}', '\Grocy\Controllers\StockController:ProductGroupEditForm');
	$this->get('/shoppinglist', '\Grocy\Controllers\StockController:ShoppingList');
	$this->get('/shoppinglistitem/{itemId}', '\Grocy\Controllers\StockController:ShoppingListItemEditForm');
	$this->get('/stockjournal', '\Grocy\Controllers\StockController:Journal');

	// Recipe routes
	$this->get('/recipes', '\Grocy\Controllers\RecipesController:Overview');
	$this->get('/recipe/{recipeId}', '\Grocy\Controllers\RecipesController:RecipeEditForm');
	$this->get('/recipe/{recipeId}/pos/{recipePosId}', '\Grocy\Controllers\RecipesController:RecipePosEditForm');

	// Chore routes
	$this->get('/choresoverview', '\Grocy\Controllers\ChoresController:Overview');
	$this->get('/choretracking', '\Grocy\Controllers\ChoresController:TrackChoreExecution');
	$this->get('/choresjournal', '\Grocy\Controllers\ChoresController:Journal');

	$this->get('/chores', '\Grocy\Controllers\ChoresController:ChoresList');
	$this->get('/chore/{choreId}', '\Grocy\Controllers\ChoresController:ChoreEditForm');

	// Battery routes
	$this->get('/batteriesoverview', '\Grocy\Controllers\BatteriesController:Overview');
	$this->get('/batterytracking', '\Grocy\Controllers\BatteriesController:TrackChargeCycle');
	$this->get('/batteriesjournal', '\Grocy\Controllers\BatteriesController:Journal');

	$this->get('/batteries', '\Grocy\Controllers\BatteriesController:BatteriesList');
	$this->get('/battery/{batteryId}', '\Grocy\Controllers\BatteriesController:BatteryEditForm');

	// Task routes
	$this->get('/tasks', '\Grocy\Controllers\TasksController:Overview');
	$this->get('/task/{taskId}', '\Grocy\Controllers\TasksController:TaskEditForm');
	$this->get('/taskcategories', '\Grocy\Controllers\TasksController:TaskCategoriesList');
	$this->get('/taskcategory/{categoryId}', '\Grocy\Controllers\TasksController:TaskCategoryEditForm');

	// Equipment routes
	$this->get('/equipment', '\Grocy\Controllers\EquipmentController:Overview');
	$this->get('/equipment/{equipmentId}', '\Grocy\Controllers\EquipmentController:EditForm');

	// Other routes
	$this->get('/calendar', '\Grocy\Controllers\CalendarController:Overview');

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
	$this->put('/edit-object/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:EditObject');
	$this->delete('/delete-object/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:DeleteObject');

	// System
	$this->get('/system/get-db-changed-time', '\Grocy\Controllers\SystemApiController:GetDbChangedTime');
	$this->post('/system/log-missing-localization', '\Grocy\Controllers\SystemApiController:LogMissingLocalization');

	// Files
	$this->put('/file/{group}', '\Grocy\Controllers\FilesApiController:UploadFile');
	$this->get('/file/{group}', '\Grocy\Controllers\FilesApiController:ServeFile');
	$this->delete('/file/{group}', '\Grocy\Controllers\FilesApiController:DeleteFile');

	// Users
	$this->get('/users/get', '\Grocy\Controllers\UsersApiController:GetUsers');
	$this->post('/users/create', '\Grocy\Controllers\UsersApiController:CreateUser');
	$this->put('/users/edit/{userId}', '\Grocy\Controllers\UsersApiController:EditUser');
	$this->delete('/users/delete/{userId}', '\Grocy\Controllers\UsersApiController:DeleteUser');

	// User
	$this->get('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:GetUserSetting');
	$this->post('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:SetUserSetting');

	// Stock
	$this->get('/stock/{productId}', '\Grocy\Controllers\StockApiController:ProductDetails');
	$this->get('/stock/{productId}/pricehistory', '\Grocy\Controllers\StockApiController:ProductPriceHistory');
	$this->get('/stock/{productId}/entries', '\Grocy\Controllers\StockApiController:ProductStockEntries');
	$this->get('/stock', '\Grocy\Controllers\StockApiController:CurrentStock');
	$this->get('/stock/volatile', '\Grocy\Controllers\StockApiController:CurrentVolatilStock');
	$this->post('/stock/{productId}/add/{amount}', '\Grocy\Controllers\StockApiController:AddProduct');
	$this->post('/stock/{productId}/consume/{amount}', '\Grocy\Controllers\StockApiController:ConsumeProduct');
	$this->post('/stock/{productId}/open/{amount}', '\Grocy\Controllers\StockApiController:OpenProduct');
	$this->post('/stock/{productId}/inventory/{newAmount}', '\Grocy\Controllers\StockApiController:InventoryProduct');
  $this->post('/stock/shoppinglist', '\Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');
	$this->post('/stock/clearshoppinglist', '\Grocy\Controllers\StockApiController:ClearShoppingList');
  $this->get('/barcode/{barcode}', '\Grocy\Controllers\StockApiController:ExternalBarcodeLookup');
  $this->post('/booking/{bookingId}/undo', '\Grocy\Controllers\StockApiController:UndoBooking');

	// Recipes
	$this->post('/recipes/{recipeId}/shoppinglist', '\Grocy\Controllers\RecipesApiController:AddNotFulfilledProductsToShoppingList');
	$this->post('/recipes/{recipeId}/consume', '\Grocy\Controllers\RecipesApiController:ConsumeRecipe');

	// Chores
	$this->get('/chores/{choreId}', '\Grocy\Controllers\ChoresApiController:ChoreDetails');
	$this->get('/chores', '\Grocy\Controllers\ChoresApiController:Current');
  $this->post('/chores/{executionId}/undo', '\Grocy\Controllers\ChoresApiController:UndoChoreExecution');
  $this->post('/chores/{choreId}/execute', '\Grocy\Controllers\ChoresApiController:TrackChoreExecution');

	// Batteries
	$this->get('/batteries/{batteryId}', '\Grocy\Controllers\BatteriesApiController:BatteryDetails');
  $this->get('/batteries', '\Grocy\Controllers\BatteriesApiController:Current');
	$this->post('/batteries/{batteryId}/charge', '\Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
	$this->post('/batteries/{chargeCycleId}/undo', '\Grocy\Controllers\BatteriesApiController:UndoChargeCycle');

	// Tasks
	$this->get('/tasks', '\Grocy\Controllers\TasksApiController:Current');
	$this->post('/tasks/{taskId}/complete', '\Grocy\Controllers\TasksApiController:MarkTaskAsCompleted');
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
