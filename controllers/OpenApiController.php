<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;
use \Grocy\Services\ApiKeyService;

class OpenApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ApiKeyService = new ApiKeyService();
	}

	protected $ApiKeyService;

	public function DocumentationUi(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'openapiui');
	}

	public function DocumentationSpec(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$applicationService = new ApplicationService();

		$versionInfo = $applicationService->GetInstalledVersion();
		$this->OpenApiSpec->info->version = $versionInfo->Version;
		$this->OpenApiSpec->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->UrlManager->ConstructUrl('/manageapikeys'), $this->OpenApiSpec->info->description);
		$this->OpenApiSpec->servers[0]->url = $this->AppContainer->UrlManager->ConstructUrl('/api');

		return $this->ApiResponse($this->OpenApiSpec);
	}

	public function ApiKeysList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'manageapikeys', [
			'apiKeys' => $this->Database->api_keys(),
			'users' => $this->Database->users()
		]);
	}

	public function CreateNewApiKey(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$newApiKey = $this->ApiKeyService->CreateApiKey();
		$newApiKeyId = $this->ApiKeyService->GetApiKeyId($newApiKey);
		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl("/manageapikeys?CreatedApiKeyId=$newApiKeyId"));
	}
}
