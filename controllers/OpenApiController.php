<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Services\ApiKeyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OpenApiController extends BaseApiController
{
	public function ApiKeysList(Request $request, Response $response, array $args)
	{
		$selectedKeyId = -1;
		if (isset($request->getQueryParams()['key']) && filter_var($request->getQueryParams()['key'], FILTER_VALIDATE_INT))
		{
			$selectedKeyId = $request->getQueryParams()['key'];
		}

		$apiKeys = $this->getDatabase()->api_keys();
		if (!User::hasPermissions(User::PERMISSION_ADMIN))
		{
			$apiKeys = $apiKeys->where('user_id', GROCY_USER_ID);
		}

		return $this->renderPage($response, 'manageapikeys', [
			'apiKeys' => $apiKeys,
			'users' => $this->getDatabase()->users(),
			'selectedKeyId' => $selectedKeyId
		]);
	}

	public function CreateNewApiKey(Request $request, Response $response, array $args)
	{
		$description = null;
		if (isset($request->getQueryParams()['description']))
		{
			$description = $request->getQueryParams()['description'];
		}

		$newApiKey = $this->getApiKeyService()->CreateApiKey(ApiKeyService::API_KEY_TYPE_DEFAULT, $description);
		$newApiKeyId = $this->getApiKeyService()->GetApiKeyId($newApiKey);
		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl("/manageapikeys?key=$newApiKeyId"));
	}

	public function DocumentationSpec(Request $request, Response $response, array $args)
	{
		$spec = $this->getOpenApiSpec();

		$applicationService = $this->getApplicationService();
		$versionInfo = $applicationService->GetInstalledVersion();
		$spec->info->version = $versionInfo->Version;
		$spec->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->get('UrlManager')->ConstructUrl('/manageapikeys'), $spec->info->description);
		$spec->servers[0]->url = $this->AppContainer->get('UrlManager')->ConstructUrl('/api');

		$spec->components->schemas->ExposedEntity_IncludingUserEntities = clone $spec->components->schemas->StringEnumTemplate;
		;
		foreach ($this->getUserfieldsService()->GetEntities() as $userEntity)
		{
			array_push($spec->components->schemas->ExposedEntity_IncludingUserEntities->enum, $userEntity);
		}
		sort($spec->components->schemas->ExposedEntity_IncludingUserEntities->enum);

		$spec->components->schemas->ExposedEntity_NotIncludingNotEditable = clone $spec->components->schemas->StringEnumTemplate;
		foreach ($spec->components->schemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->schemas->ExposedEntityNoEdit->enum))
			{
				array_push($spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum, $value);
			}
		}
		sort($spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum);

		$spec->components->schemas->ExposedEntity_IncludingUserEntities_NotIncludingNotEditable = clone $spec->components->schemas->StringEnumTemplate;
		foreach ($spec->components->schemas->ExposedEntity_IncludingUserEntities->enum as $value)
		{
			if (!in_array($value, $spec->components->schemas->ExposedEntityNoEdit->enum))
			{
				array_push($spec->components->schemas->ExposedEntity_IncludingUserEntities_NotIncludingNotEditable->enum, $value);
			}
		}
		array_push($spec->components->schemas->ExposedEntity_IncludingUserEntities_NotIncludingNotEditable->enum, 'stock'); // TODO: Don't hardcode this here - stock entries are normally not editable, but the corresponding Userfields are
		sort($spec->components->schemas->ExposedEntity_IncludingUserEntities_NotIncludingNotEditable->enum);

		$spec->components->schemas->ExposedEntity_NotIncludingNotDeletable = clone $spec->components->schemas->StringEnumTemplate;
		foreach ($spec->components->schemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->schemas->ExposedEntityNoDelete->enum))
			{
				array_push($spec->components->schemas->ExposedEntity_NotIncludingNotDeletable->enum, $value);
			}
		}
		sort($spec->components->schemas->ExposedEntity_NotIncludingNotDeletable->enum);

		$spec->components->schemas->ExposedEntity_NotIncludingNotListable = clone $spec->components->schemas->StringEnumTemplate;
		foreach ($spec->components->schemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->schemas->ExposedEntityNoListing->enum))
			{
				array_push($spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum, $value);
			}
		}

		$entity_types = array(
			"products" => "Product",
			"chores"  => "Chore",
			"product_barcodes" => "ProductBarcode",
			"batteries" => "Battery",
			"locations" => "Location",
			"quantity_units" => "QuantityUnit",
			//			"quantity_unit_conversions" => ,
			"shopping_list" => "ShoppingListItem",
			// "shopping_lists" => ,
			"shopping_locations" => "ShoppingLocation",
			//			"recipes" => ,
			// "recipes_pos",
			// "recipes_nestings",
			"tasks" => "Task",
			"task_categories" => "TaskCategory",
			//			"product_groups",
			//"equipment",
			"api_keys" => "ApiKey",
			//"userfields",
			//"userentities",
			//"userobjects",
			//"meal_plan",
			"stock_log" => "StockLogEntry",
			"stock" => "StockEntry",
			///			"stock_current_locations" => ,
			"chores_log" => "ChoreLogEntry"
			//"meal_plan_sections",
			//"products_last_purchased",
			//"products_average_price",
			//"quantity_unit_conversions_resolved",
			//"recipes_pos_resolved"
		);
		// non-generic entity api
		foreach(array_unique(array_merge($spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum,
										 $spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum)) as $value) {
			$listObjectsPath = unserialize(serialize(clone $spec->paths->{"/objects/{entity}"}));

			$type = null;
			if(array_key_exists((string)$value, $entity_types)) {
				$type = (object) array('$ref' => "#/components/schemas/" . $entity_types[$value]);
			} else {
				$type = (object) array("type" => "object");
			}
			$listObjectsPath->get->responses->{"200"}->content->
													   {"application/json"}->schema->items = $type;
			$listObjectsPath->post->requestBody->content->
													   {"application/json"}->schema = $type;

			if(!in_array($value, $spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum)) {
				unset($listObjectsPath->get);
			}
			if(!in_array($value, $spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum)) {
				unset($listObjectsPath->post);
			}

			// TODO: update summary
			$spec->paths->{"/objects/" . $value} = $listObjectsPath;
		}
		unset($spec->paths->{"/objects/{entity}"});

		foreach(array_unique(array_merge($spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum,
										 $spec->components->schemas->ExposedEntity_NotIncludingNotDeletable->enum,
										 $spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum)) as $value) {
			$singleEntityPath = unserialize(serialize(clone $spec->paths->{"/objects/{entity}/{objectId}"}));

			$type = null;
			if(array_key_exists((string)$value, $entity_types)) {
				$type = (object) array('$ref' => "#/components/schemas/" . $entity_types[$value]);
			} else {
				$type = (object) array("type" => "object");
			}

			$singleEntityPath->get->responses->{"200"}->content->
														{"application/json"}->schema = $type;
			$singleEntityPath->put->requestBody->content->
													   {"application/json"}->schema = $type;

			if(!in_array($value, $spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum)) {
				unset($singleEntityPath->get);
			}
			if(!in_array($value, $spec->components->schemas->ExposedEntity_NotIncludingNotEditable->enum)) {
				unset($singleEntityPath->put);
			}
			if(!in_array($value, $spec->components->schemas->ExposedEntity_NotIncludingNotDeletable->enum)) {
				unset($singleEntityPath->delete);
			}

			// TODO: update summary
			$spec->paths->{"/objects/" . $value . '/{objectId}'} = $singleEntityPath;
		}
		unset($spec->paths->{"/objects/{entity}/{objectId}"});

		sort($spec->components->schemas->ExposedEntity_NotIncludingNotListable->enum);

		return $this->ApiResponse($response, $spec);
	}

	public function DocumentationUi(Request $request, Response $response, array $args)
	{
		return $this->render($response, 'openapiui');
	}
}
