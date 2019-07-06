<?php

namespace Grocy\Middleware;

use \Grocy\Services\SessionService;
use \Grocy\Services\ApiKeyService;

class ApiKeyAuthMiddleware extends BaseMiddleware
{
	public function __construct(\Slim\Container $container, string $sessionCookieName, string $apiKeyHeaderName)
	{
		parent::__construct($container);
		$this->SessionCookieName = $sessionCookieName;
		$this->ApiKeyHeaderName = $apiKeyHeaderName;
	}

	protected $SessionCookieName;
	protected $ApiKeyHeaderName;

	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		if (GROCY_IS_DEMO_INSTALL || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			define('GROCY_AUTHENTICATED', true);
			$response = $next($request, $response);
		}
		else
		{
			$validSession = true;
			$validApiKey = true;
			
			$sessionService = new SessionService();
			if (!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName]))
			{
				$validSession = false;
			}

			$apiKeyService = new ApiKeyService();
			if (!$request->hasHeader($this->ApiKeyHeaderName) || !$apiKeyService->IsValidApiKey($request->getHeaderLine($this->ApiKeyHeaderName)))
			{
				$validApiKey = false;
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
				$response = $response->withStatus(401);
			}
			elseif ($validApiKey)
			{
				$user = $apiKeyService->GetUserByApiKey($request->getHeaderLine($this->ApiKeyHeaderName));
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);

				$response = $next($request, $response);
			}
			elseif ($validSession)
			{
				$user = $sessionService->GetUserBySessionKey($_COOKIE[$this->SessionCookieName]);
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);

				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
