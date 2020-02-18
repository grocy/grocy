<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

use Grocy\Services\SessionService;
use Grocy\Services\ApiKeyService;

class ApiKeyAuthMiddleware extends BaseMiddleware
{
	public function __construct(\DI\Container $container, string $sessionCookieName, string $apiKeyHeaderName)
	{
		parent::__construct($container);
		$this->SessionCookieName = $sessionCookieName;
		$this->ApiKeyHeaderName = $apiKeyHeaderName;
	}

	protected $SessionCookieName;
	protected $ApiKeyHeaderName;

	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$routeName = $route->getName();

		if (GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease' || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			define('GROCY_AUTHENTICATED', true);
			$response = $handler->handle($request);
		}
		else
		{
			$validSession = true;
			$validApiKey = true;
			$usedApiKey = null;

			$sessionService = SessionService::getInstance();
			if (!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName]))
			{
				$validSession = false;
			}

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
					}
				}
			}

			if (!$validSession && !$validApiKey)
			{
				define('GROCY_AUTHENTICATED', false);
				$response = new \Slim\Psr7\Response(); // No content when unauthorized
				$response = $response->withStatus(401);
			}
			elseif ($validApiKey)
			{
				$user = $apiKeyService->GetUserByApiKey($usedApiKey);
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);

				$response = $handler->handle($request);
			}
			elseif ($validSession)
			{
				$user = $sessionService->GetUserBySessionKey($_COOKIE[$this->SessionCookieName]);
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);

				$response = $handler->handle($request);
			}
		}

		return $response;
	}
}
