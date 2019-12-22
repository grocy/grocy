<?php

namespace Grocy\Controllers;

#use \Grocy\Services\ApplicationService;
use \Grocy\Services\ApiKeyService;

class OpenApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $ApiKeyService = null;

	protected function getApiKeyService()
	{
		if($this->ApiKeyService == null)
		{
			$this->ApiKeyService = new ApiKeyService();
		}
		return $this->ApiKeyService;
	}

	public function DocumentationUi(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->render($response, 'openapiui');
	}

	public function DocumentationSpec(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$applicationService = $this->getApplicationService;

		$versionInfo = $applicationService->GetInstalledVersion();
		$this->getOpenApiSpec()->info->version = $versionInfo->Version;
		$this->getOpenApiSpec()->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->UrlManager->ConstructUrl('/manageapikeys'), $this->getOpenApiSpec()->info->description);
		$this->getOpenApiSpec()->servers[0]->url = $this->AppContainer->UrlManager->ConstructUrl('/api');

		return $this->ApiResponse($this->getOpenApiSpec());
	}

	public function ApiKeysList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'manageapikeys', [
			'apiKeys' => $this->getDatabase()->api_keys(),
			'users' => $this->getDatabase()->users()
		]);
	}

	public function CreateNewApiKey(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$newApiKey = $this->getApiKeyService()->CreateApiKey();
		$newApiKeyId = $this->getApiKeyService()->GetApiKeyId($newApiKey);
		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl("/manageapikeys?CreatedApiKeyId=$newApiKeyId"));
	}
}
