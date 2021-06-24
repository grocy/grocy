<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class OpenApiController extends BaseApiController
{
	public function ApiKeysList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$apiKeys = $this->getDatabase()->api_keys();
		if (!User::hasPermissions(User::PERMISSION_ADMIN))
		{
			$apiKeys = $apiKeys->where('user_id', GROCY_USER_ID);
		}
		return $this->renderPage($request, $response, 'manageapikeys', [
			'apiKeys' => $apiKeys,
			'users' => $this->getDatabase()->users()
		]);
	}

	public function CreateNewApiKey(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$newApiKey = $this->getApiKeyService()->CreateApiKey();
		$newApiKeyId = $this->getApiKeyService()->GetApiKeyId($newApiKey);
		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl("/manageapikeys?CreatedApiKeyId=$newApiKeyId"));
	}

	public function DocumentationSpec(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$spec = $this->getOpenApiSpec();

		$applicationService = $this->getApplicationService();
		$versionInfo = $applicationService->GetInstalledVersion();
		$spec->info->version = $versionInfo->Version;
		$spec->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->get('UrlManager')->ConstructUrl('/manageapikeys'), $spec->info->description);
		$spec->servers[0]->url = $this->AppContainer->get('UrlManager')->ConstructUrl('/api');

		$spec->components->internalSchemas->ExposedEntity_NotIncludingNotEditable = clone $spec->components->internalSchemas->StringEnumTemplate;
		foreach ($spec->components->internalSchemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->internalSchemas->ExposedEntityNoEdit->enum))
			{
				array_push($spec->components->internalSchemas->ExposedEntity_NotIncludingNotEditable->enum, $value);
			}
		}

		$spec->components->internalSchemas->ExposedEntity_NotIncludingNotDeletable = clone $spec->components->internalSchemas->StringEnumTemplate;
		foreach ($spec->components->internalSchemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->internalSchemas->ExposedEntityNoDelete->enum))
			{
				array_push($spec->components->internalSchemas->ExposedEntity_NotIncludingNotDeletable->enum, $value);
			}
		}

		$spec->components->internalSchemas->ExposedEntity_NotIncludingNotListable = clone $spec->components->internalSchemas->StringEnumTemplate;
		foreach ($spec->components->internalSchemas->ExposedEntity->enum as $value)
		{
			if (!in_array($value, $spec->components->internalSchemas->ExposedEntityNoListing->enum))
			{
				array_push($spec->components->internalSchemas->ExposedEntity_NotIncludingNotListable->enum, $value);
			}
		}

		return $this->ApiResponse($response, $spec);
	}

	public function DocumentationUi(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->render($request, $response, 'openapiui');
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
