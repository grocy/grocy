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

		if ($routeName === 'root' || $this->ApplicationService->IsDemoInstallation() || $this->ApplicationService->IsEmbeddedInstallation())
		{
			if ($this->ApplicationService->IsDemoInstallation() || $this->ApplicationService->IsEmbeddedInstallation())
			{
				define('AUTHENTICATED', true);
				
				$localizationService = new LocalizationService(CULTURE);
				define('GROCY_USER_USERNAME', $localizationService->Localize('Demo User'));
				define('GROCY_USER_ID', -1);
			}

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
				if ($routeName !== 'login')
				{
					$user = $sessionService->GetUserBySessionKey($_COOKIE[$this->SessionCookieName]);
					define('AUTHENTICATED', true);
					define('GROCY_USER_USERNAME', $user->username);
					define('GROCY_USER_ID', $user->id);
				}
				else
				{
					define('AUTHENTICATED', false);
				}

				$response = $next($request, $response);
			}
		}

		return $response;
	}
}
