<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;

use Grocy\Middleware\JsonMiddleware;
use Grocy\Middleware\CorsMiddleware;
use Grocy\Middleware\SessionAuthMiddleware;
use Grocy\Middleware\ApiKeyAuthMiddleware;

$app->group('', function(RouteCollectorProxy $group)
{
	// System routes
	$group->get('/', '\Grocy\Controllers\SystemController:Root')->setName('root');
	$group->get('/about', '\Grocy\Controllers\SystemController:About');
	$group->get('/barcodescannertesting', '\Grocy\Controllers\SystemController:BarcodeScannerTesting');

	// Login routes
	$group->get('/login', 'LoginControllerInstance:LoginPage')->setName('login');
	$group->post('/login', 'LoginControllerInstance:ProcessLogin')->setName('login');
	$group->get('/logout', 'LoginControllerInstance:Logout');

	// Generic entity interaction
	$group->get('/userfields', '\Grocy\Controllers\GenericEntityController:UserfieldsList');
	$group->get('/userfield/{userfieldId}', '\Grocy\Controllers\GenericEntityController:UserfieldEditForm');
	$group->get('/userentities', '\Grocy\Controllers\GenericEntityController:UserentitiesList');
	$group->get('/userentity/{userentityId}', '\Grocy\Controllers\GenericEntityController:UserentityEditForm');
	$group->get('/userobjects/{userentityName}', '\Grocy\Controllers\GenericEntityController:UserobjectsList');
	$group->get('/userobject/{userentityName}/{userobjectId}', '\Grocy\Controllers\GenericEntityController:UserobjectEditForm');

	// User routes
	$group->get('/users', '\Grocy\Controllers\UsersController:UsersList');
	$group->get('/user/{userId}', '\Grocy\Controllers\UsersController:UserEditForm');

	// Stock routes
	if (GROCY_FEATURE_FLAG_STOCK)
	{
		$group->get('/stockoverview', '\Grocy\Controllers\StockController:Overview');
		$group->get('/stockentries', '\Grocy\Controllers\StockController:Stockentries');
		$group->get('/purchase', '\Grocy\Controllers\StockController:Purchase');
		$group->get('/consume', '\Grocy\Controllers\StockController:Consume');
		$group->get('/transfer', '\Grocy\Controllers\StockController:Transfer');
		$group->get('/inventory', '\Grocy\Controllers\StockController:Inventory');
		$group->get('/stockentry/{entryId}', '\Grocy\Controllers\StockController:StockEntryEditForm');
		$group->get('/products', '\Grocy\Controllers\StockController:ProductsList');
		$group->get('/product/{productId}', '\Grocy\Controllers\StockController:ProductEditForm');
		$group->get('/stocksettings', '\Grocy\Controllers\StockController:StockSettings');
		$group->get('/locations', '\Grocy\Controllers\StockController:LocationsList');
		$group->get('/location/{locationId}', '\Grocy\Controllers\StockController:LocationEditForm');
		$group->get('/quantityunits', '\Grocy\Controllers\StockController:QuantityUnitsList');
		$group->get('/quantityunit/{quantityunitId}', '\Grocy\Controllers\StockController:QuantityUnitEditForm');
		$group->get('/quantityunitconversion/{quConversionId}', '\Grocy\Controllers\StockController:QuantityUnitConversionEditForm');
		$group->get('/productgroups', '\Grocy\Controllers\StockController:ProductGroupsList');
		$group->get('/productgroup/{productGroupId}', '\Grocy\Controllers\StockController:ProductGroupEditForm');
		$group->get('/stockjournal', '\Grocy\Controllers\StockController:Journal');
		$group->get('/locationcontentsheet', '\Grocy\Controllers\StockController:LocationContentSheet');
		$group->get('/quantityunitpluraltesting', '\Grocy\Controllers\StockController:QuantityUnitPluralFormTesting');
	}

	// Stock price tracking
	if (GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
	{
		$group->get('/shoppinglocations', '\Grocy\Controllers\StockController:ShoppingLocationsList');
		$group->get('/shoppinglocation/{shoppingLocationId}', '\Grocy\Controllers\StockController:ShoppingLocationEditForm');
	}

	// Shopping list routes
	if (GROCY_FEATURE_FLAG_SHOPPINGLIST)
	{
		$group->get('/shoppinglist', '\Grocy\Controllers\StockController:ShoppingList');
		$group->get('/shoppinglistitem/{itemId}', '\Grocy\Controllers\StockController:ShoppingListItemEditForm');
		$group->get('/shoppinglist/{listId}', '\Grocy\Controllers\StockController:ShoppingListEditForm');
		$group->get('/shoppinglistsettings', '\Grocy\Controllers\StockController:ShoppingListSettings');
	}

	// Recipe routes
	if (GROCY_FEATURE_FLAG_RECIPES)
	{
		$group->get('/recipes', '\Grocy\Controllers\RecipesController:Overview');
		$group->get('/recipe/{recipeId}', '\Grocy\Controllers\RecipesController:RecipeEditForm');
		$group->get('/recipe/{recipeId}/pos/{recipePosId}', '\Grocy\Controllers\RecipesController:RecipePosEditForm');
		$group->get('/mealplan', '\Grocy\Controllers\RecipesController:MealPlan');
		$group->get('/recipessettings', '\Grocy\Controllers\RecipesController:RecipesSettings');
	}

	// Chore routes
	if (GROCY_FEATURE_FLAG_CHORES)
	{
		$group->get('/choresoverview', '\Grocy\Controllers\ChoresController:Overview');
		$group->get('/choretracking', '\Grocy\Controllers\ChoresController:TrackChoreExecution');
		$group->get('/choresjournal', '\Grocy\Controllers\ChoresController:Journal');
		$group->get('/chores', '\Grocy\Controllers\ChoresController:ChoresList');
		$group->get('/chore/{choreId}', '\Grocy\Controllers\ChoresController:ChoreEditForm');
		$group->get('/choressettings', '\Grocy\Controllers\ChoresController:ChoresSettings');
	}

	// Battery routes
	if (GROCY_FEATURE_FLAG_BATTERIES)
	{
		$group->get('/batteriesoverview', '\Grocy\Controllers\BatteriesController:Overview');
		$group->get('/batterytracking', '\Grocy\Controllers\BatteriesController:TrackChargeCycle');
		$group->get('/batteriesjournal', '\Grocy\Controllers\BatteriesController:Journal');
		$group->get('/batteries', '\Grocy\Controllers\BatteriesController:BatteriesList');
		$group->get('/battery/{batteryId}', '\Grocy\Controllers\BatteriesController:BatteryEditForm');
		$group->get('/batteriessettings', '\Grocy\Controllers\BatteriesController:BatteriesSettings');
	}

	// Task routes
	if (GROCY_FEATURE_FLAG_TASKS)
	{
		$group->get('/tasks', '\Grocy\Controllers\TasksController:Overview');
		$group->get('/task/{taskId}', '\Grocy\Controllers\TasksController:TaskEditForm');
		$group->get('/taskcategories', '\Grocy\Controllers\TasksController:TaskCategoriesList');
		$group->get('/taskcategory/{categoryId}', '\Grocy\Controllers\TasksController:TaskCategoryEditForm');
		$group->get('/taskssettings', '\Grocy\Controllers\TasksController:TasksSettings');
	}

	// Equipment routes
	if (GROCY_FEATURE_FLAG_EQUIPMENT)
	{
		$group->get('/equipment', '\Grocy\Controllers\EquipmentController:Overview');
		$group->get('/equipment/{equipmentId}', '\Grocy\Controllers\EquipmentController:EditForm');
	}

	// Calendar routes
	if (GROCY_FEATURE_FLAG_CALENDAR)
	{
		$group->get('/calendar', '\Grocy\Controllers\CalendarController:Overview');
	}

	// OpenAPI routes
	$group->get('/api', '\Grocy\Controllers\OpenApiController:DocumentationUi');
	$group->get('/manageapikeys', '\Grocy\Controllers\OpenApiController:ApiKeysList');
	$group->get('/manageapikeys/new', '\Grocy\Controllers\OpenApiController:CreateNewApiKey');
})->add(new SessionAuthMiddleware($container, $container->get('LoginControllerInstance')->GetSessionCookieName(), $app->getResponseFactory()));

$app->group('/api', function(RouteCollectorProxy $group)
{
	// OpenAPI
	$group->get('/openapi/specification', '\Grocy\Controllers\OpenApiController:DocumentationSpec');

	// System
	$group->get('/system/info', '\Grocy\Controllers\SystemApiController:GetSystemInfo');
	$group->get('/system/db-changed-time', '\Grocy\Controllers\SystemApiController:GetDbChangedTime');	
	$group->get('/system/config', '\Grocy\Controllers\SystemApiController:GetConfig');
	$group->post('/system/log-missing-localization', '\Grocy\Controllers\SystemApiController:LogMissingLocalization');
	
	// Generic entity interaction
	$group->get('/objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:GetObjects');
	$group->get('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetObject');
	$group->get('/objects/{entity}/search/{searchString}', '\Grocy\Controllers\GenericEntityApiController:SearchObjects');
	$group->post('/objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:AddObject');
	$group->put('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:EditObject');
	$group->delete('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:DeleteObject');
	$group->get('/userfields/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetUserfields');
	$group->put('/userfields/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:SetUserfields');

	// Files
	$group->put('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:UploadFile');
	$group->get('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:ServeFile');
	$group->delete('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:DeleteFile');

	// Users
	$group->get('/users', '\Grocy\Controllers\UsersApiController:GetUsers');
	$group->post('/users', '\Grocy\Controllers\UsersApiController:CreateUser');
	$group->put('/users/{userId}', '\Grocy\Controllers\UsersApiController:EditUser');
	$group->delete('/users/{userId}', '\Grocy\Controllers\UsersApiController:DeleteUser');

	// User
	$group->get('/user/settings', '\Grocy\Controllers\UsersApiController:GetUserSettings');
	$group->get('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:GetUserSetting');
	$group->put('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:SetUserSetting');

	// Stock
	if (GROCY_FEATURE_FLAG_STOCK)
	{
		$group->get('/stock', '\Grocy\Controllers\StockApiController:CurrentStock');
		$group->get('/stock/entry/{entryId}', '\Grocy\Controllers\StockApiController:StockEntry');
		$group->put('/stock/entry/{entryId}', '\Grocy\Controllers\StockApiController:EditStockEntry');
		$group->get('/stock/volatile', '\Grocy\Controllers\StockApiController:CurrentVolatileStock');
		$group->get('/stock/products/{productId}', '\Grocy\Controllers\StockApiController:ProductDetails');
		$group->get('/stock/products/{productId}/entries', '\Grocy\Controllers\StockApiController:ProductStockEntries');
		$group->get('/stock/products/{productId}/locations', '\Grocy\Controllers\StockApiController:ProductStockLocations');
		$group->get('/stock/products/{productId}/price-history', '\Grocy\Controllers\StockApiController:ProductPriceHistory');
		$group->post('/stock/products/{productId}/add', '\Grocy\Controllers\StockApiController:AddProduct');
		$group->post('/stock/products/{productId}/consume', '\Grocy\Controllers\StockApiController:ConsumeProduct');
		$group->post('/stock/products/{productId}/transfer', '\Grocy\Controllers\StockApiController:TransferProduct');
		$group->post('/stock/products/{productId}/inventory', '\Grocy\Controllers\StockApiController:InventoryProduct');
		$group->post('/stock/products/{productId}/open', '\Grocy\Controllers\StockApiController:OpenProduct');
		$group->get('/stock/products/by-barcode/{barcode}', '\Grocy\Controllers\StockApiController:ProductDetailsByBarcode');
		$group->post('/stock/products/by-barcode/{barcode}/add', '\Grocy\Controllers\StockApiController:AddProductByBarcode');
		$group->post('/stock/products/by-barcode/{barcode}/consume', '\Grocy\Controllers\StockApiController:ConsumeProductByBarcode');
		$group->post('/stock/products/by-barcode/{barcode}/transfer', '\Grocy\Controllers\StockApiController:TransferProductByBarcode');
		$group->post('/stock/products/by-barcode/{barcode}/inventory', '\Grocy\Controllers\StockApiController:InventoryProductByBarcode');
		$group->post('/stock/products/by-barcode/{barcode}/open', '\Grocy\Controllers\StockApiController:OpenProductByBarcode');
		$group->get('/stock/bookings/{bookingId}', '\Grocy\Controllers\StockApiController:StockBooking');
		$group->post('/stock/bookings/{bookingId}/undo', '\Grocy\Controllers\StockApiController:UndoBooking');
		$group->get('/stock/transactions/{transactionId}', '\Grocy\Controllers\StockApiController:StockTransactions');
		$group->post('/stock/transactions/{transactionId}/undo', '\Grocy\Controllers\StockApiController:UndoTransaction');
		$group->get('/stock/barcodes/external-lookup/{barcode}', '\Grocy\Controllers\StockApiController:ExternalBarcodeLookup');
	}

	// Shopping list
	if (GROCY_FEATURE_FLAG_SHOPPINGLIST)
	{
		$group->post('/stock/shoppinglist/add-missing-products', '\Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');
		$group->post('/stock/shoppinglist/clear', '\Grocy\Controllers\StockApiController:ClearShoppingList');
		$group->post('/stock/shoppinglist/add-product', '\Grocy\Controllers\StockApiController:AddProductToShoppingList');
		$group->post('/stock/shoppinglist/remove-product', '\Grocy\Controllers\StockApiController:RemoveProductFromShoppingList');
	}

	// Recipes
	if (GROCY_FEATURE_FLAG_RECIPES)
	{
		$group->post('/recipes/{recipeId}/add-not-fulfilled-products-to-shoppinglist', '\Grocy\Controllers\RecipesApiController:AddNotFulfilledProductsToShoppingList');
		$group->get('/recipes/{recipeId}/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
		$group->post('/recipes/{recipeId}/consume', '\Grocy\Controllers\RecipesApiController:ConsumeRecipe');
		$group->get('/recipes/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
	}

	// Chores
	if (GROCY_FEATURE_FLAG_CHORES)
	{
		$group->get('/chores', '\Grocy\Controllers\ChoresApiController:Current');
		$group->get('/chores/{choreId}', '\Grocy\Controllers\ChoresApiController:ChoreDetails');
		$group->post('/chores/{choreId}/execute', '\Grocy\Controllers\ChoresApiController:TrackChoreExecution');
		$group->post('/chores/executions/{executionId}/undo', '\Grocy\Controllers\ChoresApiController:UndoChoreExecution');
		$group->post('/chores/executions/calculate-next-assignments', '\Grocy\Controllers\ChoresApiController:CalculateNextExecutionAssignments');
	}

	// Batteries
	if (GROCY_FEATURE_FLAG_BATTERIES)
	{
		$group->get('/batteries', '\Grocy\Controllers\BatteriesApiController:Current');
		$group->get('/batteries/{batteryId}', '\Grocy\Controllers\BatteriesApiController:BatteryDetails');
		$group->post('/batteries/{batteryId}/charge', '\Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
		$group->post('/batteries/charge-cycles/{chargeCycleId}/undo', '\Grocy\Controllers\BatteriesApiController:UndoChargeCycle');
	}

	// Tasks
	if (GROCY_FEATURE_FLAG_TASKS)
	{
		$group->get('/tasks', '\Grocy\Controllers\TasksApiController:Current');
		$group->post('/tasks/{taskId}/complete', '\Grocy\Controllers\TasksApiController:MarkTaskAsCompleted');
		$group->post('/tasks/{taskId}/undo', '\Grocy\Controllers\TasksApiController:UndoTask');
	}

	// Calendar
	if (GROCY_FEATURE_FLAG_CALENDAR)
	{
		$group->get('/calendar/ical', '\Grocy\Controllers\CalendarApiController:Ical')->setName('calendar-ical');
		$group->get('/calendar/ical/sharing-link', '\Grocy\Controllers\CalendarApiController:IcalSharingLink');
	}
})->add(JsonMiddleware::class)
->add(new ApiKeyAuthMiddleware($container, $container->get('LoginControllerInstance')->GetSessionCookieName(), $container->get('ApiKeyHeaderName')));

// Handle CORS preflight OPTIONS requests
$app->options('/api/{routes:.+}', function(Request $request, Response $response): Response
{
	return $response;
})->add(CorsMiddleware::class);
