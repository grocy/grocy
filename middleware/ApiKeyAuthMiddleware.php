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

		if ($this->ApplicationService->IsDemoInstallation())
		{
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

			if (!$validSession && !$validApiKey)
			{
				$response = $response->withStatus(401);
			}
			else
			{
				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
