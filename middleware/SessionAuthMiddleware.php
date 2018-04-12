<?php

namespace Grocy\Middleware;

use \Grocy\Services\SessionService;

class SessionAuthMiddleware
{
	public function __construct(\Slim\Container $container) {
		$this->container = $container;
	}

	protected $container;

	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		$sessionService = new SessionService();
		if ((!isset($_COOKIE['grocy_session']) || !$sessionService->IsValidSession($_COOKIE['grocy_session'])) && $routeName !== 'login')
		{
			$response = $response->withRedirect('/login');
		}
		else
		{
			$response = $next($request, $response);
		}

		return $response;
	}
}
