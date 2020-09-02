<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class OpenApiController extends BaseApiController
{
	public function ApiKeysList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$apiKeys = $this->getDatabase()->api_keys();
		if(!User::hasPermissions(User::PERMISSION_ADMIN))
			$apiKeys = $apiKeys->where('user_id', GROCY_USER_ID);
		return $this->renderPage($response, 'manageapikeys', [
			'apiKeys' =>$apiKeys,
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
		$applicationService = $this->getApplicationService();

		$versionInfo = $applicationService->GetInstalledVersion();
		$this->getOpenApiSpec()->info->version = $versionInfo->Version;
		$this->getOpenApiSpec()->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->get('UrlManager')->ConstructUrl('/manageapikeys'), $this->getOpenApiSpec()->info->description);
		$this->getOpenApiSpec()->servers[0]->url = $this->AppContainer->get('UrlManager')->ConstructUrl('/api');

		return $this->ApiResponse($response, $this->getOpenApiSpec());
	}

	public function DocumentationUi(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->render($response, 'openapiui');
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
