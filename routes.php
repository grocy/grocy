<?php

use \Grocy\Middleware\JsonMiddleware;
use \Grocy\Middleware\SessionAuthMiddleware;
use \Grocy\Middleware\ApiKeyAuthMiddleware;
use \Tuupola\Middleware\CorsMiddleware;

$app->group('', function()
{
	// System routes
	$this->get('/', '\Grocy\Controllers\SystemController:Root')->setName('root');
	$this->get('/about', '\Grocy\Controllers\SystemController:About');

	// Login routes
	$this->get('/login', 'LoginControllerInstance:LoginPage')->setName('login');
	$this->post('/login', 'LoginControllerInstance:ProcessLogin')->setName('login');
	$this->get('/logout', 'LoginControllerInstance:Logout');

	// Generic entity interaction
	$this->get('/userfields', '\Grocy\Controllers\GenericEntityController:UserfieldsList');
	$this->get('/userfield/{userfieldId}', '\Grocy\Controllers\GenericEntityController:UserfieldEditForm');

	// User routes
	$this->get('/users', '\Grocy\Controllers\UsersController:UsersList');
	$this->get('/user/{userId}', '\Grocy\Controllers\UsersController:UserEditForm');

	// Stock routes
	if (GROCY_FEATURE_FLAG_STOCK)
	{
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
		$this->get('/stockjournal', '\Grocy\Controllers\StockController:Journal');
	}

	// Shopping list routes
	if (GROCY_FEATURE_FLAG_SHOPPINGLIST)
	{
		$this->get('/shoppinglist', '\Grocy\Controllers\StockController:ShoppingList');
		$this->get('/shoppinglistitem/{itemId}', '\Grocy\Controllers\StockController:ShoppingListItemEditForm');
		$this->get('/shoppinglist/{listId}', '\Grocy\Controllers\StockController:ShoppingListEditForm');
	}

	// Recipe routes
	if (GROCY_FEATURE_FLAG_RECIPES)
	{
		$this->get('/recipes', '\Grocy\Controllers\RecipesController:Overview');
		$this->get('/recipe/{recipeId}', '\Grocy\Controllers\RecipesController:RecipeEditForm');
		$this->get('/recipe/{recipeId}/pos/{recipePosId}', '\Grocy\Controllers\RecipesController:RecipePosEditForm');
		$this->get('/mealplan', '\Grocy\Controllers\RecipesController:MealPlan');
	}

	// Chore routes
	if (GROCY_FEATURE_FLAG_CHORES)
	{
		$this->get('/choresoverview', '\Grocy\Controllers\ChoresController:Overview');
		$this->get('/choretracking', '\Grocy\Controllers\ChoresController:TrackChoreExecution');
		$this->get('/choresjournal', '\Grocy\Controllers\ChoresController:Journal');
		$this->get('/chores', '\Grocy\Controllers\ChoresController:ChoresList');
		$this->get('/chore/{choreId}', '\Grocy\Controllers\ChoresController:ChoreEditForm');
		$this->get('/choressettings', '\Grocy\Controllers\ChoresController:ChoresSettings');
	}

	// Battery routes
	if (GROCY_FEATURE_FLAG_BATTERIES)
	{
		$this->get('/batteriesoverview', '\Grocy\Controllers\BatteriesController:Overview');
		$this->get('/batterytracking', '\Grocy\Controllers\BatteriesController:TrackChargeCycle');
		$this->get('/batteriesjournal', '\Grocy\Controllers\BatteriesController:Journal');
		$this->get('/batteries', '\Grocy\Controllers\BatteriesController:BatteriesList');
		$this->get('/battery/{batteryId}', '\Grocy\Controllers\BatteriesController:BatteryEditForm');
		$this->get('/batteriessettings', '\Grocy\Controllers\BatteriesController:BatteriesSettings');
	}

	// Task routes
	if (GROCY_FEATURE_FLAG_TASKS)
	{
		$this->get('/tasks', '\Grocy\Controllers\TasksController:Overview');
		$this->get('/task/{taskId}', '\Grocy\Controllers\TasksController:TaskEditForm');
		$this->get('/taskcategories', '\Grocy\Controllers\TasksController:TaskCategoriesList');
		$this->get('/taskcategory/{categoryId}', '\Grocy\Controllers\TasksController:TaskCategoryEditForm');
		$this->get('/taskssettings', '\Grocy\Controllers\TasksController:TasksSettings');
	}

	// Equipment routes
	if (GROCY_FEATURE_FLAG_EQUIPMENT)
	{
		$this->get('/equipment', '\Grocy\Controllers\EquipmentController:Overview');
		$this->get('/equipment/{equipmentId}', '\Grocy\Controllers\EquipmentController:EditForm');
	}
	
	// Calendar routes
	if (GROCY_FEATURE_FLAG_CALENDAR)
	{
		$this->get('/calendar', '\Grocy\Controllers\CalendarController:Overview');
	}

	// OpenAPI routes
	$this->get('/api', '\Grocy\Controllers\OpenApiController:DocumentationUi');
	$this->get('/manageapikeys', '\Grocy\Controllers\OpenApiController:ApiKeysList');
	$this->get('/manageapikeys/new', '\Grocy\Controllers\OpenApiController:CreateNewApiKey');
})->add(new SessionAuthMiddleware($appContainer, $appContainer->LoginControllerInstance->GetSessionCookieName()));

$app->group('/api', function()
{
	// OpenAPI
	$this->get('/openapi/specification', '\Grocy\Controllers\OpenApiController:DocumentationSpec');

	// System
	$this->get('/system/info', '\Grocy\Controllers\SystemApiController:GetSystemInfo');
	$this->get('/system/db-changed-time', '\Grocy\Controllers\SystemApiController:GetDbChangedTime');	
	$this->post('/system/log-missing-localization', '\Grocy\Controllers\SystemApiController:LogMissingLocalization');
	
	// Generic entity interaction
	$this->get('/objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:GetObjects');
	$this->get('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetObject');
	$this->post('/objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:AddObject');
	$this->put('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:EditObject');
	$this->delete('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:DeleteObject');
	$this->get('/userfields/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetUserfields');
	$this->put('/userfields/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:SetUserfields');

	// Files
	$this->put('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:UploadFile');
	$this->get('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:ServeFile');
	$this->delete('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:DeleteFile');

	// Users
	$this->get('/users', '\Grocy\Controllers\UsersApiController:GetUsers');
	$this->post('/users', '\Grocy\Controllers\UsersApiController:CreateUser');
	$this->put('/users/{userId}', '\Grocy\Controllers\UsersApiController:EditUser');
	$this->delete('/users/{userId}', '\Grocy\Controllers\UsersApiController:DeleteUser');

	// User
	$this->get('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:GetUserSetting');
	$this->put('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:SetUserSetting');

	// Stock
	if (GROCY_FEATURE_FLAG_STOCK)
	{
		$this->get('/stock', '\Grocy\Controllers\StockApiController:CurrentStock');
		$this->get('/stock/volatile', '\Grocy\Controllers\StockApiController:CurrentVolatilStock');
		$this->get('/stock/products/{productId}', '\Grocy\Controllers\StockApiController:ProductDetails');
		$this->get('/stock/products/by-barcode/{barcode}', '\Grocy\Controllers\StockApiController:ProductDetailsByBarcode');
		$this->get('/stock/products/{productId}/entries', '\Grocy\Controllers\StockApiController:ProductStockEntries');
		$this->get('/stock/products/{productId}/price-history', '\Grocy\Controllers\StockApiController:ProductPriceHistory');
		$this->post('/stock/products/{productId}/add', '\Grocy\Controllers\StockApiController:AddProduct');
		$this->post('/stock/products/{productId}/consume', '\Grocy\Controllers\StockApiController:ConsumeProduct');
		$this->post('/stock/products/{productId}/inventory', '\Grocy\Controllers\StockApiController:InventoryProduct');
		$this->post('/stock/products/{productId}/open', '\Grocy\Controllers\StockApiController:OpenProduct');
		$this->post('/stock/bookings/{bookingId}/undo', '\Grocy\Controllers\StockApiController:UndoBooking');
		$this->get('/stock/barcodes/external-lookup', '\Grocy\Controllers\StockApiController:ExternalBarcodeLookup');
	}

	// Shopping list
	if (GROCY_FEATURE_FLAG_SHOPPINGLIST)
	{
		$this->post('/stock/shoppinglist/add-missing-products', '\Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');
		$this->post('/stock/shoppinglist/clear', '\Grocy\Controllers\StockApiController:ClearShoppingList');
	}

	// Recipes
	if (GROCY_FEATURE_FLAG_RECIPES)
	{
		$this->post('/recipes/{recipeId}/add-not-fulfilled-products-to-shoppinglist', '\Grocy\Controllers\RecipesApiController:AddNotFulfilledProductsToShoppingList');
		$this->get('/recipes/{recipeId}/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
		$this->post('/recipes/{recipeId}/consume', '\Grocy\Controllers\RecipesApiController:ConsumeRecipe');
		$this->get('/recipes/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
	}

	// Chores
	if (GROCY_FEATURE_FLAG_CHORES)
	{
		$this->get('/chores', '\Grocy\Controllers\ChoresApiController:Current');
		$this->get('/chores/{choreId}', '\Grocy\Controllers\ChoresApiController:ChoreDetails');
		$this->post('/chores/{choreId}/execute', '\Grocy\Controllers\ChoresApiController:TrackChoreExecution');
		$this->post('/chores/executions/{executionId}/undo', '\Grocy\Controllers\ChoresApiController:UndoChoreExecution');
	}

	// Batteries
	if (GROCY_FEATURE_FLAG_BATTERIES)
	{
		$this->get('/batteries', '\Grocy\Controllers\BatteriesApiController:Current');
		$this->get('/batteries/{batteryId}', '\Grocy\Controllers\BatteriesApiController:BatteryDetails');
		$this->post('/batteries/{batteryId}/charge', '\Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
		$this->post('/batteries/charge-cycles/{chargeCycleId}/undo', '\Grocy\Controllers\BatteriesApiController:UndoChargeCycle');
	}

	// Tasks
	if (GROCY_FEATURE_FLAG_TASKS)
	{
		$this->get('/tasks', '\Grocy\Controllers\TasksApiController:Current');
		$this->post('/tasks/{taskId}/complete', '\Grocy\Controllers\TasksApiController:MarkTaskAsCompleted');
		$this->post('/tasks/{taskId}/undo', '\Grocy\Controllers\TasksApiController:UndoTask');
	}

	// Calendar
	if (GROCY_FEATURE_FLAG_CALENDAR)
	{
		$this->get('/calendar/ical', '\Grocy\Controllers\CalendarApiController:Ical')->setName('calendar-ical');
		$this->get('/calendar/ical/sharing-link', '\Grocy\Controllers\CalendarApiController:IcalSharingLink');
	}
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
