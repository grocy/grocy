<?php

namespace Grocy\Middleware;

use \Grocy\Services\SessionService;
use \Grocy\Services\LocalizationService;

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
		$sessionService = new SessionService();

		if ($routeName === 'root')
		{
			$response = $next($request, $response);
		}
		elseif (GROCY_IS_DEMO_INSTALL || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			$user = $sessionService->GetDefaultUser();
			define('GROCY_AUTHENTICATED', true);
			define('GROCY_USER_USERNAME', $user->username);

			$response = $next($request, $response);
		}
		else
		{
			if ((!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName])) && $routeName !== 'login')
			{
				define('GROCY_AUTHENTICATED', false);
				$response = $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login'));
			}
			else
			{
				if ($routeName !== 'login')
				{
					$user = $sessionService->GetUserBySessionKey($_COOKIE[$this->SessionCookieName]);
					define('GROCY_AUTHENTICATED', true);
					define('GROCY_USER_USERNAME', $user->username);
					define('GROCY_USER_ID', $user->id);
				}
				else
				{
					define('GROCY_AUTHENTICATED', false);
				}

				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
