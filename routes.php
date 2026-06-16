<?php

use Grocy\Controllers\Api\BatteriesApiController;
use Grocy\Controllers\Api\CalendarApiController;
use Grocy\Controllers\Api\ChoresApiController;
use Grocy\Controllers\Api\FilesApiController;
use Grocy\Controllers\Api\GenericEntityApiController;
use Grocy\Controllers\Api\OpenApiController;
use Grocy\Controllers\Api\PrintApiController;
use Grocy\Controllers\Api\RecipesApiController;
use Grocy\Controllers\Api\StockApiController;
use Grocy\Controllers\Api\SystemApiController;
use Grocy\Controllers\Api\TasksApiController;
use Grocy\Controllers\Api\UsersApiController;
use Grocy\Controllers\BatteriesController;
use Grocy\Controllers\CalendarController;
use Grocy\Controllers\ChoresController;
use Grocy\Controllers\EquipmentController;
use Grocy\Controllers\GenericEntityController;
use Grocy\Controllers\LoginController;
use Grocy\Controllers\RecipesController;
use Grocy\Controllers\StockController;
use Grocy\Controllers\StockReportsController;
use Grocy\Controllers\SystemController;
use Grocy\Controllers\TasksController;
use Grocy\Controllers\UsersController;
use Grocy\Middleware\CorsMiddleware;
use Grocy\Middleware\JsonMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->group('', function (RouteCollectorProxy $group)
{
	// System routes
	$group->get('/', [SystemController::class, 'Root'])->setName('root');
	$group->get('/about', [SystemController::class, 'About']);
	$group->get('/manifest', [SystemController::class, 'Manifest']);
	$group->get('/barcodescannertesting', [SystemController::class, 'BarcodeScannerTesting']);

	// Login routes
	$group->get('/login', [LoginController::class, 'LoginPage'])->setName('login');
	$group->post('/login', [LoginController::class, 'ProcessLogin'])->setName('login');
	$group->get('/logout', [LoginController::class, 'Logout']);

	// Generic entity interaction
	$group->get('/userfields', [GenericEntityController::class, 'UserfieldsList']);
	$group->get('/userfield/{userfieldId}', [GenericEntityController::class, 'UserfieldEditForm']);
	$group->get('/userentities', [GenericEntityController::class, 'UserentitiesList']);
	$group->get('/userentity/{userentityId}', [GenericEntityController::class, 'UserentityEditForm']);
	$group->get('/userobjects/{userentityName}', [GenericEntityController::class, 'UserobjectsList']);
	$group->get('/userobject/{userentityName}/{userobjectId}', [GenericEntityController::class, 'UserobjectEditForm']);

	// User routes
	$group->get('/users', [UsersController::class, 'UsersList']);
	$group->get('/user/{userId}', [UsersController::class, 'UserEditForm']);
	$group->get('/user/{userId}/permissions', [UsersController::class, 'PermissionList']);
	$group->get('/usersettings', [UsersController::class, 'UserSettings']);

	// Stock master data routes
	$group->get('/products', [StockController::class, 'ProductsList']);
	$group->get('/product/{productId}', [StockController::class, 'ProductEditForm']);
	$group->get('/quantityunits', [StockController::class, 'QuantityUnitsList']);
	$group->get('/quantityunit/{quantityunitId}', [StockController::class, 'QuantityUnitEditForm']);
	$group->get('/quantityunitconversion/{quConversionId}', [StockController::class, 'QuantityUnitConversionEditForm']);
	$group->get('/productgroups', [StockController::class, 'ProductGroupsList']);
	$group->get('/productgroup/{productGroupId}', [StockController::class, 'ProductGroupEditForm']);
	$group->get('/product/{productId}/grocycode', [StockController::class, 'ProductGrocycodeImage']);

	// Stock handling routes
	$group->get('/stockoverview', [StockController::class, 'Overview']);
	$group->get('/stockentries', [StockController::class, 'Stockentries']);
	$group->get('/purchase', [StockController::class, 'Purchase']);
	$group->get('/consume', [StockController::class, 'Consume']);
	$group->get('/transfer', [StockController::class, 'Transfer']);
	$group->get('/inventory', [StockController::class, 'Inventory']);
	$group->get('/stockentry/{entryId}', [StockController::class, 'StockEntryEditForm']);
	$group->get('/stocksettings', [StockController::class, 'StockSettings']);
	$group->get('/locations', [StockController::class, 'LocationsList']);
	$group->get('/location/{locationId}', [StockController::class, 'LocationEditForm']);
	$group->get('/stockjournal', [StockController::class, 'Journal']);
	$group->get('/locationcontentsheet', [StockController::class, 'LocationContentSheet']);
	$group->get('/quantityunitpluraltesting', [StockController::class, 'QuantityUnitPluralFormTesting']);
	$group->get('/stockjournal/summary', [StockController::class, 'JournalSummary']);
	$group->get('/productbarcodes/{productBarcodeId}', [StockController::class, 'ProductBarcodesEditForm']);
	$group->get('/stockentry/{entryId}/grocycode', [StockController::class, 'StockEntryGrocycodeImage']);
	$group->get('/stockentry/{entryId}/label', [StockController::class, 'StockEntryGrocycodeLabel']);
	$group->get('/quantityunitconversionsresolved', [StockController::class, 'QuantityUnitConversionsResolved']);
	$group->get('/stockreports/spendings', [StockReportsController::class, 'Spendings']);

	// Stock price tracking
	$group->get('/shoppinglocations', [StockController::class, 'ShoppingLocationsList']);
	$group->get('/shoppinglocation/{shoppingLocationId}', [StockController::class, 'ShoppingLocationEditForm']);

	// Shopping list routes
	$group->get('/shoppinglist', [StockController::class, 'ShoppingList']);
	$group->get('/shoppinglistitem/{itemId}', [StockController::class, 'ShoppingListItemEditForm']);
	$group->get('/shoppinglist/{listId}', [StockController::class, 'ShoppingListEditForm']);
	$group->get('/shoppinglistsettings', [StockController::class, 'ShoppingListSettings']);

	// Recipe routes
	$group->get('/recipes', [RecipesController::class, 'Overview']);
	$group->get('/recipe/{recipeId}', [RecipesController::class, 'RecipeEditForm']);
	$group->get('/recipe/{recipeId}/pos/{recipePosId}', [RecipesController::class, 'RecipePosEditForm']);
	$group->get('/recipessettings', [RecipesController::class, 'RecipesSettings']);
	$group->get('/recipe/{recipeId}/grocycode', [RecipesController::class, 'RecipeGrocycodeImage']);

	// Meal plan routes
	$group->get('/mealplan', [RecipesController::class, 'MealPlan']);
	$group->get('/mealplansections', [RecipesController::class, 'MealPlanSectionsList']);
	$group->get('/mealplansection/{sectionId}', [RecipesController::class, 'MealPlanSectionEditForm']);

	// Chore routes
	$group->get('/choresoverview', [ChoresController::class, 'Overview']);
	$group->get('/choretracking', [ChoresController::class, 'TrackChoreExecution']);
	$group->get('/choresjournal', [ChoresController::class, 'Journal']);
	$group->get('/chores', [ChoresController::class, 'ChoresList']);
	$group->get('/chore/{choreId}', [ChoresController::class, 'ChoreEditForm']);
	$group->get('/choressettings', [ChoresController::class, 'ChoresSettings']);
	$group->get('/chore/{choreId}/grocycode', [ChoresController::class, 'ChoreGrocycodeImage']);

	// Battery routes
	$group->get('/batteriesoverview', [BatteriesController::class, 'Overview']);
	$group->get('/batterytracking', [BatteriesController::class, 'TrackChargeCycle']);
	$group->get('/batteriesjournal', [BatteriesController::class, 'Journal']);
	$group->get('/batteries', [BatteriesController::class, 'BatteriesList']);
	$group->get('/battery/{batteryId}', [BatteriesController::class, 'BatteryEditForm']);
	$group->get('/batteriessettings', [BatteriesController::class, 'BatteriesSettings']);
	$group->get('/battery/{batteryId}/grocycode', [BatteriesController::class, 'BatteryGrocycodeImage']);

	// Task routes
	$group->get('/tasks', [TasksController::class, 'Overview']);
	$group->get('/task/{taskId}', [TasksController::class, 'TaskEditForm']);
	$group->get('/taskcategories', [TasksController::class, 'TaskCategoriesList']);
	$group->get('/taskcategory/{categoryId}', [TasksController::class, 'TaskCategoryEditForm']);
	$group->get('/taskssettings', [TasksController::class, 'TasksSettings']);

	// Equipment routes
	$group->get('/equipment', [EquipmentController::class, 'Overview']);
	$group->get('/equipment/{equipmentId}', [EquipmentController::class, 'EditForm']);

	// Calendar routes
	$group->get('/calendar', [CalendarController::class, 'Overview']);

	// OpenAPI routes
	$group->get('/api', [OpenApiController::class, 'DocumentationUi']);
	$group->get('/manageapikeys', [OpenApiController::class, 'ApiKeysList']);
	$group->get('/manageapikeys/new', [OpenApiController::class, 'CreateNewApiKey']);
});

$app->group('/api', function (RouteCollectorProxy $group)
{
	// OpenAPI
	$group->get('/openapi/specification', [OpenApiController::class, 'DocumentationSpec']);

	// System
	$group->get('/system/info', [SystemApiController::class, 'GetSystemInfo']);
	$group->get('/system/time', [SystemApiController::class, 'GetSystemTime']);
	$group->get('/system/db-changed-time', [SystemApiController::class, 'GetDbChangedTime']);
	$group->get('/system/config', [SystemApiController::class, 'GetConfig']);
	$group->post('/system/log-missing-localization', [SystemApiController::class, 'LogMissingLocalization']);
	$group->get('/system/localization-strings', [SystemApiController::class, 'GetLocalizationStrings']);

	// Generic entity interaction
	$group->get('/objects/{entity}', [GenericEntityApiController::class, 'GetObjects']);
	$group->get('/objects/{entity}/{objectId}', [GenericEntityApiController::class, 'GetObject']);
	$group->post('/objects/{entity}', [GenericEntityApiController::class, 'AddObject']);
	$group->put('/objects/{entity}/{objectId}', [GenericEntityApiController::class, 'EditObject']);
	$group->delete('/objects/{entity}/{objectId}', [GenericEntityApiController::class, 'DeleteObject']);
	$group->get('/userfields/{entity}/{objectId}', [GenericEntityApiController::class, 'GetUserfields']);
	$group->put('/userfields/{entity}/{objectId}', [GenericEntityApiController::class, 'SetUserfields']);

	// Files
	$group->put('/files/{group}/{fileName}', [FilesApiController::class, 'UploadFile']);
	$group->get('/files/{group}/{fileName}', [FilesApiController::class, 'ServeFile']);
	$group->delete('/files/{group}/{fileName}', [FilesApiController::class, 'DeleteFile']);

	// Users
	$group->get('/users', [UsersApiController::class, 'GetUsers']);
	$group->post('/users', [UsersApiController::class, 'CreateUser']);
	$group->put('/users/{userId}', [UsersApiController::class, 'EditUser']);
	$group->delete('/users/{userId}', [UsersApiController::class, 'DeleteUser']);
	$group->get('/users/{userId}/permissions', [UsersApiController::class, 'ListPermissions']);
	$group->post('/users/{userId}/permissions', [UsersApiController::class, 'AddPermission']);
	$group->put('/users/{userId}/permissions', [UsersApiController::class, 'SetPermissions']);

	// User
	$group->get('/user', [UsersApiController::class, 'CurrentUser']);
	$group->get('/user/settings', [UsersApiController::class, 'GetUserSettings']);
	$group->get('/user/settings/{settingKey}', [UsersApiController::class, 'GetUserSetting']);
	$group->put('/user/settings/{settingKey}', [UsersApiController::class, 'SetUserSetting']);
	$group->delete('/user/settings/{settingKey}', [UsersApiController::class, 'DeleteUserSetting']);

	// Stock
	$group->get('/stock', [StockApiController::class, 'CurrentStock']);
	$group->get('/stock/entry/{entryId}', [StockApiController::class, 'StockEntry']);
	$group->put('/stock/entry/{entryId}', [StockApiController::class, 'EditStockEntry']);
	$group->get('/stock/volatile', [StockApiController::class, 'CurrentVolatileStock']);
	$group->get('/stock/products/{productId}', [StockApiController::class, 'ProductDetails']);
	$group->get('/stock/products/{productId}/entries', [StockApiController::class, 'ProductStockEntries']);
	$group->get('/stock/products/{productId}/locations', [StockApiController::class, 'ProductStockLocations']);
	$group->get('/stock/products/{productId}/price-history', [StockApiController::class, 'ProductPriceHistory']);
	$group->post('/stock/products/{productId}/add', [StockApiController::class, 'AddProduct']);
	$group->post('/stock/products/{productId}/consume', [StockApiController::class, 'ConsumeProduct']);
	$group->post('/stock/products/{productId}/transfer', [StockApiController::class, 'TransferProduct']);
	$group->post('/stock/products/{productId}/inventory', [StockApiController::class, 'InventoryProduct']);
	$group->post('/stock/products/{productId}/open', [StockApiController::class, 'OpenProduct']);
	$group->post('/stock/products/{productIdToKeep}/merge/{productIdToRemove}', [StockApiController::class, 'MergeProducts']);
	$group->get('/stock/products/by-barcode/{barcode}', [StockApiController::class, 'ProductDetailsByBarcode']);
	$group->post('/stock/products/by-barcode/{barcode}/add', [StockApiController::class, 'AddProductByBarcode']);
	$group->post('/stock/products/by-barcode/{barcode}/consume', [StockApiController::class, 'ConsumeProductByBarcode']);
	$group->post('/stock/products/by-barcode/{barcode}/transfer', [StockApiController::class, 'TransferProductByBarcode']);
	$group->post('/stock/products/by-barcode/{barcode}/inventory', [StockApiController::class, 'InventoryProductByBarcode']);
	$group->post('/stock/products/by-barcode/{barcode}/open', [StockApiController::class, 'OpenProductByBarcode']);
	$group->get('/stock/locations/{locationId}/entries', [StockApiController::class, 'LocationStockEntries']);
	$group->get('/stock/bookings/{bookingId}', [StockApiController::class, 'StockBooking']);
	$group->post('/stock/bookings/{bookingId}/undo', [StockApiController::class, 'UndoBooking']);
	$group->get('/stock/transactions/{transactionId}', [StockApiController::class, 'StockTransactions']);
	$group->post('/stock/transactions/{transactionId}/undo', [StockApiController::class, 'UndoTransaction']);
	$group->get('/stock/barcodes/external-lookup/{barcode}', [StockApiController::class, 'ExternalBarcodeLookup']);
	$group->get('/stock/products/{productId}/printlabel', [StockApiController::class, 'ProductPrintLabel']);
	$group->get('/stock/entry/{entryId}/printlabel', [StockApiController::class, 'StockEntryPrintLabel']);

	// Shopping list
	$group->post('/stock/shoppinglist/add-missing-products', [StockApiController::class, 'AddMissingProductsToShoppingList']);
	$group->post('/stock/shoppinglist/add-overdue-products', [StockApiController::class, 'AddOverdueProductsToShoppingList']);
	$group->post('/stock/shoppinglist/add-expired-products', [StockApiController::class, 'AddExpiredProductsToShoppingList']);
	$group->post('/stock/shoppinglist/clear', [StockApiController::class, 'ClearShoppingList']);
	$group->post('/stock/shoppinglist/add-product', [StockApiController::class, 'AddProductToShoppingList']);
	$group->post('/stock/shoppinglist/remove-product', [StockApiController::class, 'RemoveProductFromShoppingList']);

	// Recipes
	$group->post('/recipes/{recipeId}/add-not-fulfilled-products-to-shoppinglist', [RecipesApiController::class, 'AddNotFulfilledProductsToShoppingList']);
	$group->get('/recipes/{recipeId}/fulfillment', [RecipesApiController::class, 'GetRecipeFulfillment']);
	$group->post('/recipes/{recipeId}/consume', [RecipesApiController::class, 'ConsumeRecipe']);
	$group->get('/recipes/fulfillment', [RecipesApiController::class, 'GetRecipeFulfillment']);
	$group->Post('/recipes/{recipeId}/copy', [RecipesApiController::class, 'CopyRecipe']);
	$group->get('/recipes/{recipeId}/printlabel', [RecipesApiController::class, 'RecipePrintLabel']);


	// Chores
	$group->get('/chores', [ChoresApiController::class, 'Current']);
	$group->get('/chores/{choreId}', [ChoresApiController::class, 'ChoreDetails']);
	$group->post('/chores/{choreId}/execute', [ChoresApiController::class, 'TrackChoreExecution']);
	$group->post('/chores/executions/{executionId}/undo', [ChoresApiController::class, 'UndoChoreExecution']);
	$group->post('/chores/executions/calculate-next-assignments', [ChoresApiController::class, 'CalculateNextExecutionAssignments']);
	$group->get('/chores/{choreId}/printlabel', [ChoresApiController::class, 'ChorePrintLabel']);
	$group->post('/chores/{choreIdToKeep}/merge/{choreIdToRemove}', [ChoresApiController::class, 'MergeChores']);

	// Printing
	$group->get('/print/shoppinglist/thermal', [PrintApiController::class, 'PrintShoppingListThermal']);

	// Batteries
	$group->get('/batteries', [BatteriesApiController::class, 'Current']);
	$group->get('/batteries/{batteryId}', [BatteriesApiController::class, 'BatteryDetails']);
	$group->post('/batteries/{batteryId}/charge', [BatteriesApiController::class, 'TrackChargeCycle']);
	$group->post('/batteries/charge-cycles/{chargeCycleId}/undo', [BatteriesApiController::class, 'UndoChargeCycle']);
	$group->get('/batteries/{batteryId}/printlabel', [BatteriesApiController::class, 'BatteryPrintLabel']);

	// Tasks
	$group->get('/tasks', [TasksApiController::class, 'Current']);
	$group->post('/tasks/{taskId}/complete', [TasksApiController::class, 'MarkTaskAsCompleted']);
	$group->post('/tasks/{taskId}/undo', [TasksApiController::class, 'UndoTask']);

	// Calendar
	$group->get('/calendar/ical', [CalendarApiController::class, 'Ical'])->setName('calendar-ical');
	$group->get('/calendar/ical/sharing-link', [CalendarApiController::class, 'IcalSharingLink']);
})->add(new CorsMiddleware($container, $app->getResponseFactory()))->add(new JsonMiddleware($container, $app->getResponseFactory()));


// For CORS preflight OPTIONS requests
$app->options('/api/{routes:.+}', function (Request $request, Response $response): Response
{
	return $response;
})->add(new CorsMiddleware($container, $app->getResponseFactory()));
