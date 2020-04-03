<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

use Grocy\Services\SessionService;
use Grocy\Services\LocalizationService;

class SessionAuthMiddleware extends BaseMiddleware
{
	public function __construct(\DI\Container $container, string $sessionCookieName, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container);
		$this->SessionCookieName = $sessionCookieName;
		$this->ResponseFactory = $responseFactory;
	}

	protected $SessionCookieName;
	protected $ResponseFactory;

	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$routeName = $route->getName();
		$sessionService = SessionService::getInstance();

		if ($routeName === 'root')
		{
			$response = $handler->handle($request);
		}
		elseif (GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease' || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			$user = $sessionService->GetDefaultUser();
			define('GROCY_AUTHENTICATED', true);
			define('GROCY_USER_USERNAME', $user->username);

			$response = $handler->handle($request);
		}
		else
		{
			if ((!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName])) && $routeName !== 'login')
			{
				define('GROCY_AUTHENTICATED', false);
				$response = $this->ResponseFactory->createResponse();
				return $response->withHeader('Location', $this->AppContainer->get('UrlManager')->ConstructUrl('/login'));
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

				$response = $handler->handle($request);
			}
		}

		return $response;
	}
}
