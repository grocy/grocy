<?php

namespace Grocy\Middleware;

use \Grocy\Services\SessionService;

class SessionAuthMiddleware extends BaseMiddleware
{
	public function __construct(\Slim\Container $container, string $sessionCookieName)
	{
		parent::__construct($container);
		$this->SessionCookieName = $sessionCookieName;
	}

	protected $SessionCookieName;

	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		if ($routeName === 'root' || $this->ApplicationService->IsDemoInstallation())
		{
			define('AUTHENTICATED', $this->ApplicationService->IsDemoInstallation());
			$response = $next($request, $response);
		}
		else
		{
			$sessionService = new SessionService();
			if ((!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName])) && $routeName !== 'login')
			{
				define('AUTHENTICATED', false);
				$response = $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login'));
			}
			else
			{
				define('AUTHENTICATED', $routeName !== 'login');
				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
