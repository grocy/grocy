<?php

namespace Grocy\Middleware;

use \Grocy\Services\SessionService;

class SessionAuthMiddleware extends BaseMiddleware
{
	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		if ($routeName === 'root')
		{
			$response = $next($request, $response);
		}
		else
		{
			$sessionService = new SessionService();
			if ((!isset($_COOKIE['grocy_session']) || !$sessionService->IsValidSession($_COOKIE['grocy_session'])) && $routeName !== 'login')
			{
				$response = $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login'));
			}
			else
			{
				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
