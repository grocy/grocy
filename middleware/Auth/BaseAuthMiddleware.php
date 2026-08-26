<?php

namespace Grocy\Middleware\Auth;

use Grocy\Middleware\BaseMiddleware;
use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

abstract class BaseAuthMiddleware extends BaseMiddleware
{
	protected ?string $RouteName = null;
	protected bool $IsApiRoute = false;

	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$this->RouteName = $route->getName();
		$this->IsApiRoute = string_starts_with($request->getUri()->getPath(), '/api/');

		if ($this->RouteName === 'root' || $this->RouteName === 'login')
		{
			// Root and Login routes are public/unauthenticated

			define('GROCY_AUTHENTICATED', false);
			return $handler->handle($request);
		}

		if (GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease' || GROCY_IS_EMBEDDED_INSTALL || GROCY_DISABLE_AUTH)
		{
			// These modes use default user context (without authentication) only

			$sessionService = SessionService::GetInstance();
			$user = $sessionService->GetDefaultUser();

			define('GROCY_AUTHENTICATED', true);
			define('GROCY_USER_USERNAME', $user->username);
			define('GROCY_USER_PICTURE_FILE_NAME', $user->picture_file_name);

			return $handler->handle($request);
		}
		else
		{
			// Normal authentication flow (up to specific middleware implementation)

			$user = $this->AuthenticateRequest($request);

			if ($user === null)
			{
				define('GROCY_AUTHENTICATED', false);
				$response = $this->ResponseFactory->createResponse();

				if ($this->IsApiRoute)
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

	protected static function SetSessionCookie(int $tokenType, string $token)
	{
		switch ($tokenType)
		{
			case SessionService::SESSION_TOKEN_TYPE_ACCESS:
				setcookie(SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS, $token, [
					'expires' => 0, // Browser session end
					'httponly' => true,
					'samesite' => 'Lax',
					'path' => '/'
				]);
				break;

			case SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME:
				setcookie(SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME, $token, [
					'expires' => time() + SessionService::SESSION_TOKEN_EXPIRATION_SECONDS_REMEMBER_ME,
					'httponly' => true,
					'samesite' => 'Lax',
					'path' => '/'
				]);
				break;
		}
	}

	protected static function RemoveSessionCookie(int $tokenType)
	{
		switch ($tokenType)
		{
			case SessionService::SESSION_TOKEN_TYPE_ACCESS:
				setcookie(SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS, '', [
					'expires' => time() - 3600,
					'httponly' => true,
					'samesite' => 'Lax',
					'path' => '/'
				]);
				break;

			case SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME:
				setcookie(SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME, '', [
					'expires' => time() - 3600,
					'httponly' => true,
					'samesite' => 'Lax',
					'path' => '/'
				]);
				break;
		}
	}

	/**
	 * @param array $postParams
	 * @return bool True/False if the provided credentials were valid
	 * @throws \Exception Throws an \Exception if an error happened during credentials processing or if this authentication middlware doesn't provide credentials processing (e.g. handles this externally)
	 */
	abstract public static function ProcessLogin(array $postParams);

	/**
	 * @param Request $request
	 * @return mixed|null the user row or null if the request is not authenticated
	 * @throws \Exception Throws an \Exception if authentaction config is invalid
	 */
	abstract protected function AuthenticateRequest(Request $request);
}
