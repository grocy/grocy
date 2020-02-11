<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;
use \Grocy\Services\ApiKeyService;

class OpenApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->ApiKeyService = new ApiKeyService();
	}

	protected $ApiKeyService;

	public function DocumentationUi(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'openapiui');
	}

	public function DocumentationSpec(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$applicationService = new ApplicationService();

		$versionInfo = $applicationService->GetInstalledVersion();
		$this->OpenApiSpec->info->version = $versionInfo->Version;
		$this->OpenApiSpec->info->description = str_replace('PlaceHolderManageApiKeysUrl', $this->AppContainer->get('UrlManager')->ConstructUrl('/manageapikeys'), $this->OpenApiSpec->info->description);
		$this->OpenApiSpec->servers[0]->url = $this->AppContainer->get('UrlManager')->ConstructUrl('/api');

		return $this->ApiResponse($response, $this->OpenApiSpec);
	}

	public function ApiKeysList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'manageapikeys', [
			'apiKeys' => $this->Database->api_keys(),
			'users' => $this->Database->users()
		]);
	}

	public function CreateNewApiKey(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$newApiKey = $this->ApiKeyService->CreateApiKey();
		$newApiKeyId = $this->ApiKeyService->GetApiKeyId($newApiKey);
		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl("/manageapikeys?CreatedApiKeyId=$newApiKeyId"));
	}
}
