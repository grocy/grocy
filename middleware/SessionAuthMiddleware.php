<?php

namespace Grocy\Middleware;

use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionAuthMiddleware extends AuthMiddleware
{
	public function __construct(\DI\Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container, $responseFactory);
	}

	public function authenticate(Request $request)
	{
		if (!defined('GROCY_SHOW_AUTH_VIEWS'))
		{
			define('GROCY_SHOW_AUTH_VIEWS', true);
		}

		$sessionService = SessionService::getInstance();

		if (!isset($_COOKIE[SessionService::SESSION_COOKIE_NAME]) || !$sessionService->IsValidSession($_COOKIE[SessionService::SESSION_COOKIE_NAME]))
		{
			return null;
		}
		else
		{
			return $sessionService->GetUserBySessionKey($_COOKIE[SessionService::SESSION_COOKIE_NAME]);
		}
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
