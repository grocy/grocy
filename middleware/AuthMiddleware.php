<?php

namespace Grocy\Middleware;

use Grocy\Services\SessionService;
use DI\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

abstract class AuthMiddleware extends BaseMiddleware
{
	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container);
		$this->ResponseFactory = $responseFactory;
	}

	protected $ResponseFactory;

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
		elseif ($routeName === 'login')
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
			define('GROCY_USER_PICTURE_FILE_NAME', $user->picture_file_name);

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
					return $response->withStatus(302)->withHeader('Location', $this->AppContainer->get('UrlManager')->ConstructUrl('/login'));
				}
			}
			else
			{
				define('GROCY_AUTHENTICATED', true);
				define('GROCY_USER_ID', $user->id);
				define('GROCY_USER_USERNAME', $user->username);
				define('GROCY_USER_PICTURE_FILE_NAME', $user->picture_file_name);

				return $response = $handler->handle($request);
			}
		}
	}

	protected static function SetSessionCookie($sessionKey)
	{
		// Cookie never expires, session validity is up to SessionService
		setcookie(SessionService::SESSION_COOKIE_NAME, $sessionKey, PHP_INT_SIZE == 4 ? PHP_INT_MAX : PHP_INT_MAX >> 32);
	}

	/**
	 * @param array $postParams
	 * @return bool True/False if the provided credentials were valid
	 * @throws \Exception Throws an \Exception if an error happened during credentials processing or if this AuthMiddleware doesn't provide credentials processing (e. g. handles this externally)
	 */
	abstract public static function ProcessLogin(array $postParams);

	/**
	 * @param Request $request
	 * @return mixed|null the user row or null if the request is not authenticated
	 * @throws \Exception Throws an \Exception if config is invalid.
	 */
	abstract protected function authenticate(Request $request);
}
