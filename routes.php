<?php

use Grocy\Middleware\JsonMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->group('', function (RouteCollectorProxy $group)
{
	// System routes
	$group->get('/', '\Grocy\Controllers\SystemController:Root')->setName('root');
	$group->get('/about', '\Grocy\Controllers\SystemController:About');
	$group->get('/manifest', '\Grocy\Controllers\SystemController:Manifest');
	$group->get('/barcodescannertesting', '\Grocy\Controllers\SystemController:BarcodeScannerTesting');

	// Login routes
	$group->get('/login', '\Grocy\Controllers\LoginController:LoginPage')->setName('login');
	$group->post('/login', '\Grocy\Controllers\LoginController:ProcessLogin')->setName('login');
	$group->get('/logout', '\Grocy\Controllers\LoginController:Logout');

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
	$group->get('/user/{userId}/permissions', '\Grocy\Controllers\UsersController:PermissionList');
	$group->get('/usersettings', '\Grocy\Controllers\UsersController:UserSettings');

	$group->get('/files/{group}/{fileName}', '\Grocy\Controllers\FilesApiController:ShowFile');

	// Stock master data routes
	$group->get('/products', '\Grocy\Controllers\StockController:ProductsList');
	$group->get('/product/{productId}', '\Grocy\Controllers\StockController:ProductEditForm');
	$group->get('/quantityunits', '\Grocy\Controllers\StockController:QuantityUnitsList');
	$group->get('/quantityunit/{quantityunitId}', '\Grocy\Controllers\StockController:QuantityUnitEditForm');
	$group->get('/quantityunitconversion/{quConversionId}', '\Grocy\Controllers\StockController:QuantityUnitConversionEditForm');
	$group->get('/productgroups', '\Grocy\Controllers\StockController:ProductGroupsList');
	$group->get('/productgroup/{productGroupId}', '\Grocy\Controllers\StockController:ProductGroupEditForm');
	$group->get('/product/{productId}/grocycode', '\Grocy\Controllers\StockController:ProductGrocycodeImage');

	// Stock handling routes
	$group->get('/stockoverview', '\Grocy\Controllers\StockController:Overview');
	$group->get('/stockentries', '\Grocy\Controllers\StockController:Stockentries');
	$group->get('/purchase', '\Grocy\Controllers\StockController:Purchase');
	$group->get('/consume', '\Grocy\Controllers\StockController:Consume');
	$group->get('/transfer', '\Grocy\Controllers\StockController:Transfer');
	$group->get('/inventory', '\Grocy\Controllers\StockController:Inventory');
	$group->get('/stockentry/{entryId}', '\Grocy\Controllers\StockController:StockEntryEditForm');
	$group->get('/stocksettings', '\Grocy\Controllers\StockController:StockSettings');
	$group->get('/locations', '\Grocy\Controllers\StockController:LocationsList');
	$group->get('/location/{locationId}', '\Grocy\Controllers\StockController:LocationEditForm');
	$group->get('/stockjournal', '\Grocy\Controllers\StockController:Journal');
	$group->get('/locationcontentsheet', '\Grocy\Controllers\StockController:LocationContentSheet');
	$group->get('/quantityunitpluraltesting', '\Grocy\Controllers\StockController:QuantityUnitPluralFormTesting');
	$group->get('/stockjournal/summary', '\Grocy\Controllers\StockController:JournalSummary');
	$group->get('/productbarcodes/{productBarcodeId}', '\Grocy\Controllers\StockController:ProductBarcodesEditForm');
	$group->get('/stockentry/{entryId}/grocycode', '\Grocy\Controllers\StockController:StockEntryGrocycodeImage');
	$group->get('/stockentry/{entryId}/label', '\Grocy\Controllers\StockController:StockEntryGrocycodeLabel');
	$group->get('/quantityunitconversionsresolved', '\Grocy\Controllers\StockController:QuantityUnitConversionsResolved');
	$group->get('/stockreports/spendings', '\Grocy\Controllers\StockReportsController:Spendings');

	// Stock price tracking
	$group->get('/shoppinglocations', '\Grocy\Controllers\StockController:ShoppingLocationsList');
	$group->get('/shoppinglocation/{shoppingLocationId}', '\Grocy\Controllers\StockController:ShoppingLocationEditForm');

	// Shopping list routes
	$group->get('/shoppinglist', '\Grocy\Controllers\StockController:ShoppingList');
	$group->get('/shoppinglistitem/{itemId}', '\Grocy\Controllers\StockController:ShoppingListItemEditForm');
	$group->get('/shoppinglist/{listId}', '\Grocy\Controllers\StockController:ShoppingListEditForm');
	$group->get('/shoppinglistsettings', '\Grocy\Controllers\StockController:ShoppingListSettings');

	// Recipe routes
	$group->get('/recipes', '\Grocy\Controllers\RecipesController:Overview');
	$group->get('/recipe/{recipeId}', '\Grocy\Controllers\RecipesController:RecipeEditForm');
	$group->get('/recipe/{recipeId}/pos/{recipePosId}', '\Grocy\Controllers\RecipesController:RecipePosEditForm');
	$group->get('/recipessettings', '\Grocy\Controllers\RecipesController:RecipesSettings');
	$group->get('/recipe/{recipeId}/grocycode', '\Grocy\Controllers\RecipesController:RecipeGrocycodeImage');

	// Meal plan routes
	$group->get('/mealplan', '\Grocy\Controllers\RecipesController:MealPlan');
	$group->get('/mealplansections', '\Grocy\Controllers\RecipesController:MealPlanSectionsList');
	$group->get('/mealplansection/{sectionId}', '\Grocy\Controllers\RecipesController:MealPlanSectionEditForm');

	// Chore routes
	$group->get('/choresoverview', '\Grocy\Controllers\ChoresController:Overview');
	$group->get('/choretracking', '\Grocy\Controllers\ChoresController:TrackChoreExecution');
	$group->get('/choresjournal', '\Grocy\Controllers\ChoresController:Journal');
	$group->get('/chores', '\Grocy\Controllers\ChoresController:ChoresList');
	$group->get('/chore/{choreId}', '\Grocy\Controllers\ChoresController:ChoreEditForm');
	$group->get('/choressettings', '\Grocy\Controllers\ChoresController:ChoresSettings');
	$group->get('/chore/{choreId}/grocycode', '\Grocy\Controllers\ChoresController:ChoreGrocycodeImage');

	// Battery routes
	$group->get('/batteriesoverview', '\Grocy\Controllers\BatteriesController:Overview');
	$group->get('/batterytracking', '\Grocy\Controllers\BatteriesController:TrackChargeCycle');
	$group->get('/batteriesjournal', '\Grocy\Controllers\BatteriesController:Journal');
	$group->get('/batteries', '\Grocy\Controllers\BatteriesController:BatteriesList');
	$group->get('/battery/{batteryId}', '\Grocy\Controllers\BatteriesController:BatteryEditForm');
	$group->get('/batteriessettings', '\Grocy\Controllers\BatteriesController:BatteriesSettings');
	$group->get('/battery/{batteryId}/grocycode', '\Grocy\Controllers\BatteriesController:BatteryGrocycodeImage');

	// Task routes
	$group->get('/tasks', '\Grocy\Controllers\TasksController:Overview');
	$group->get('/task/{taskId}', '\Grocy\Controllers\TasksController:TaskEditForm');
	$group->get('/taskcategories', '\Grocy\Controllers\TasksController:TaskCategoriesList');
	$group->get('/taskcategory/{categoryId}', '\Grocy\Controllers\TasksController:TaskCategoryEditForm');
	$group->get('/taskssettings', '\Grocy\Controllers\TasksController:TasksSettings');

	// Equipment routes
	$group->get('/equipment', '\Grocy\Controllers\EquipmentController:Overview');
	$group->get('/equipment/{equipmentId}', '\Grocy\Controllers\EquipmentController:EditForm');

	// Calendar routes
	$group->get('/calendar', '\Grocy\Controllers\CalendarController:Overview');

	// OpenAPI routes
	$group->get('/api', '\Grocy\Controllers\OpenApiController:DocumentationUi');
	$group->get('/manageapikeys', '\Grocy\Controllers\OpenApiController:ApiKeysList');
	$group->get('/manageapikeys/new', '\Grocy\Controllers\OpenApiController:CreateNewApiKey');
});

$app->group('/api', function (RouteCollectorProxy $group)
{
	// OpenAPI
	$group->get('/openapi/specification', '\Grocy\Controllers\OpenApiController:DocumentationSpec');

	// System
	$group->get('/system/info', '\Grocy\Controllers\SystemApiController:GetSystemInfo');
	$group->get('/system/time', '\Grocy\Controllers\SystemApiController:GetSystemTime');
	$group->get('/system/db-changed-time', '\Grocy\Controllers\SystemApiController:GetDbChangedTime');
	$group->get('/system/config', '\Grocy\Controllers\SystemApiController:GetConfig');
	$group->post('/system/log-missing-localization', '\Grocy\Controllers\SystemApiController:LogMissingLocalization');
	$group->get('/system/localization-strings', '\Grocy\Controllers\SystemApiController:GetLocalizationStrings');

	// Generic entity interaction
	$group->get('/objects/{entity}', '\Grocy\Controllers\GenericEntityApiController:GetObjects');
	$group->get('/objects/{entity}/{objectId}', '\Grocy\Controllers\GenericEntityApiController:GetObject');
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
	$group->get('/users/{userId}/permissions', '\Grocy\Controllers\UsersApiController:ListPermissions');
	$group->post('/users/{userId}/permissions', '\Grocy\Controllers\UsersApiController:AddPermission');
	$group->put('/users/{userId}/permissions', '\Grocy\Controllers\UsersApiController:SetPermissions');

	// User
	$group->get('/user', '\Grocy\Controllers\UsersApiController:CurrentUser');
	$group->get('/user/settings', '\Grocy\Controllers\UsersApiController:GetUserSettings');
	$group->get('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:GetUserSetting');
	$group->put('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:SetUserSetting');
	$group->delete('/user/settings/{settingKey}', '\Grocy\Controllers\UsersApiController:DeleteUserSetting');

	// Stock
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
	$group->post('/stock/products/{productIdToKeep}/merge/{productIdToRemove}', '\Grocy\Controllers\StockApiController:MergeProducts');
	$group->get('/stock/products/by-barcode/{barcode}', '\Grocy\Controllers\StockApiController:ProductDetailsByBarcode');
	$group->post('/stock/products/by-barcode/{barcode}/add', '\Grocy\Controllers\StockApiController:AddProductByBarcode');
	$group->post('/stock/products/by-barcode/{barcode}/consume', '\Grocy\Controllers\StockApiController:ConsumeProductByBarcode');
	$group->post('/stock/products/by-barcode/{barcode}/transfer', '\Grocy\Controllers\StockApiController:TransferProductByBarcode');
	$group->post('/stock/products/by-barcode/{barcode}/inventory', '\Grocy\Controllers\StockApiController:InventoryProductByBarcode');
	$group->post('/stock/products/by-barcode/{barcode}/open', '\Grocy\Controllers\StockApiController:OpenProductByBarcode');
	$group->get('/stock/locations/{locationId}/entries', '\Grocy\Controllers\StockApiController:LocationStockEntries');
	$group->get('/stock/bookings/{bookingId}', '\Grocy\Controllers\StockApiController:StockBooking');
	$group->post('/stock/bookings/{bookingId}/undo', '\Grocy\Controllers\StockApiController:UndoBooking');
	$group->get('/stock/transactions/{transactionId}', '\Grocy\Controllers\StockApiController:StockTransactions');
	$group->post('/stock/transactions/{transactionId}/undo', '\Grocy\Controllers\StockApiController:UndoTransaction');
	$group->get('/stock/barcodes/external-lookup/{barcode}', '\Grocy\Controllers\StockApiController:ExternalBarcodeLookup');
	$group->get('/stock/products/{productId}/printlabel', '\Grocy\Controllers\StockApiController:ProductPrintLabel');
	$group->get('/stock/entry/{entryId}/printlabel', '\Grocy\Controllers\StockApiController:StockEntryPrintLabel');

	// Shopping list
	$group->post('/stock/shoppinglist/add-missing-products', '\Grocy\Controllers\StockApiController:AddMissingProductsToShoppingList');
	$group->post('/stock/shoppinglist/add-overdue-products', '\Grocy\Controllers\StockApiController:AddOverdueProductsToShoppingList');
	$group->post('/stock/shoppinglist/add-expired-products', '\Grocy\Controllers\StockApiController:AddExpiredProductsToShoppingList');
	$group->post('/stock/shoppinglist/clear', '\Grocy\Controllers\StockApiController:ClearShoppingList');
	$group->post('/stock/shoppinglist/add-product', '\Grocy\Controllers\StockApiController:AddProductToShoppingList');
	$group->post('/stock/shoppinglist/remove-product', '\Grocy\Controllers\StockApiController:RemoveProductFromShoppingList');

	// Recipes
	$group->post('/recipes/{recipeId}/add-not-fulfilled-products-to-shoppinglist', '\Grocy\Controllers\RecipesApiController:AddNotFulfilledProductsToShoppingList');
	$group->get('/recipes/{recipeId}/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
	$group->post('/recipes/{recipeId}/consume', '\Grocy\Controllers\RecipesApiController:ConsumeRecipe');
	$group->get('/recipes/fulfillment', '\Grocy\Controllers\RecipesApiController:GetRecipeFulfillment');
	$group->Post('/recipes/{recipeId}/copy', '\Grocy\Controllers\RecipesApiController:CopyRecipe');
	$group->get('/recipes/{recipeId}/printlabel', '\Grocy\Controllers\RecipesApiController:RecipePrintLabel');


	// Chores
	$group->get('/chores', '\Grocy\Controllers\ChoresApiController:Current');
	$group->get('/chores/{choreId}', '\Grocy\Controllers\ChoresApiController:ChoreDetails');
	$group->post('/chores/{choreId}/execute', '\Grocy\Controllers\ChoresApiController:TrackChoreExecution');
	$group->post('/chores/executions/{executionId}/undo', '\Grocy\Controllers\ChoresApiController:UndoChoreExecution');
	$group->post('/chores/executions/calculate-next-assignments', '\Grocy\Controllers\ChoresApiController:CalculateNextExecutionAssignments');
	$group->get('/chores/{choreId}/printlabel', '\Grocy\Controllers\ChoresApiController:ChorePrintLabel');
	$group->post('/chores/{choreIdToKeep}/merge/{choreIdToRemove}', '\Grocy\Controllers\ChoresApiController:MergeChores');

	//Printing
	$group->get('/print/shoppinglist/thermal', '\Grocy\Controllers\PrintApiController:PrintShoppingListThermal');

	// Batteries
	$group->get('/batteries', '\Grocy\Controllers\BatteriesApiController:Current');
	$group->get('/batteries/{batteryId}', '\Grocy\Controllers\BatteriesApiController:BatteryDetails');
	$group->post('/batteries/{batteryId}/charge', '\Grocy\Controllers\BatteriesApiController:TrackChargeCycle');
	$group->post('/batteries/charge-cycles/{chargeCycleId}/undo', '\Grocy\Controllers\BatteriesApiController:UndoChargeCycle');
	$group->get('/batteries/{batteryId}/printlabel', '\Grocy\Controllers\BatteriesApiController:BatteryPrintLabel');

	// Tasks
	$group->get('/tasks', '\Grocy\Controllers\TasksApiController:Current');
	$group->post('/tasks/{taskId}/complete', '\Grocy\Controllers\TasksApiController:MarkTaskAsCompleted');
	$group->post('/tasks/{taskId}/undo', '\Grocy\Controllers\TasksApiController:UndoTask');

	// Calendar
	$group->get('/calendar/ical', '\Grocy\Controllers\CalendarApiController:Ical')->setName('calendar-ical');
	$group->get('/calendar/ical/sharing-link', '\Grocy\Controllers\CalendarApiController:IcalSharingLink');
})->add(JsonMiddleware::class);

// Handle CORS preflight OPTIONS requests
$app->options('/api/{routes:.+}', function (Request $request, Response $response): Response
{
	return $response->withStatus(204);
});
