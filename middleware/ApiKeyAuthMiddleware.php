<?php

namespace Grocy\Middleware;

use Grocy\Services\ApiKeyService;
use DI\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class ApiKeyAuthMiddleware extends AuthMiddleware
{
	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container, $responseFactory);
		$this->ApiKeyHeaderName = $this->AppContainer->get('ApiKeyHeaderName');
	}

	protected $ApiKeyHeaderName;

	public function authenticate(Request $request)
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$routeName = $route->getName();

		$validApiKey = true;
		$usedApiKey = null;

		$apiKeyService = new ApiKeyService();

		// First check of the API key in the configured header
		if (!$request->hasHeader($this->ApiKeyHeaderName) || !$apiKeyService->IsValidApiKey($request->getHeaderLine($this->ApiKeyHeaderName)))
		{
			$validApiKey = false;
		}
		else
		{
			$usedApiKey = $request->getHeaderLine($this->ApiKeyHeaderName);
		}

		// Not recommended, but it's also possible to provide the API key via a query parameter (same name as the configured header)
		if (!$validApiKey && !empty($request->getQueryParam($this->ApiKeyHeaderName)) && $apiKeyService->IsValidApiKey($request->getQueryParam($this->ApiKeyHeaderName)))
		{
			$validApiKey = true;
			$usedApiKey = $request->getQueryParam($this->ApiKeyHeaderName);
		}

		// Handling of special purpose API keys
		if (!$validApiKey)
		{
			if ($routeName === 'calendar-ical')
			{
				if ($request->getQueryParam('secret') !== null && $apiKeyService->IsValidApiKey($request->getQueryParam('secret'), ApiKeyService::API_KEY_TYPE_SPECIAL_PURPOSE_CALENDAR_ICAL))
				{
					$validApiKey = true;
					$usedApiKey = $request->getQueryParam('secret');
				}
			}
		}

		if ($validApiKey)
		{
			return $apiKeyService->GetUserByApiKey($usedApiKey);
		}
		else
		{
			return null;
		}
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
