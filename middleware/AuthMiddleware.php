<?php

namespace Grocy\Middleware;

use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

abstract class AuthMiddleware extends BaseMiddleware
{
	protected $ResponseFactory;

	public function __construct(\DI\Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container);
		$this->ResponseFactory = $responseFactory;
	}

	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$routeName = $route->getName();
		$isApiRoute = string_starts_with($request->getUri()->getPath(), '/api/');

		if ($routeName === 'root')
		{
			return $handler->handle($request);
		}
		else

		if ($routeName === 'login')
		{
			define('GROCY_AUTHENTICATED', false);
			return $handler->handle($request);
		}

		if (GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease' || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			$sessionService = SessionService::getInstance();
			$user = $sessionService->GetDefaultUser();

			define('GROCY_AUTHENTICATED', true);
			define('GROCY_USER_USERNAME', $user->username);

			return $handler->handle($request);
		}
		else
		{
			$user = $this->authenticate($request);

			if ($user === null)
			{
				define('GROCY_AUTHENTICATED', false);

				$response = $this->ResponseFactory->createResponse();

				if ($isApiRoute)
				{
					return $response->withStatus(401);
				}
				else
				{
					return $response->withHeader('Location', $this->AppContainer->get('UrlManager')->ConstructUrl('/login'));
				}

			}
			else
			{
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);
				define('GROCY_USER_USERNAME', $user->username);

				return $response = $handler->handle($request);
			}

		}

	}

	/**
	 * @param Request $request
	 * @return mixed|null the user row or null if the request is not authenticated
	 * @throws \Exception Throws an \Exception if config is invalid.
	 */
	protected abstract function authenticate(Request $request);
}
