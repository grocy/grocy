<?php

namespace Grocy\Middleware\Auth;

use DI\Container;
use Grocy\Services\ApiKeyService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiKeyAuthMiddleware extends BaseAuthMiddleware
{
	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container, $responseFactory);
		$this->ApiKeyHeaderName = $this->AppContainer->get('ApiKeyHeaderName');
	}

	protected readonly string $ApiKeyHeaderName;

	public function AuthenticateRequest(Request $request)
	{
		$validApiKey = false;
		$usedApiKey = null;
		$apiKeyService = new ApiKeyService();

		// First check the key in the configured header
		if ($request->hasHeader($this->ApiKeyHeaderName) && $apiKeyService->IsValidApiKey($request->getHeaderLine($this->ApiKeyHeaderName)))
		{
			$validApiKey = true;
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
			if ($this->RouteName === 'calendar-ical')
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
