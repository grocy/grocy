<?php

namespace Grocy\Middleware;

use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionAuthMiddleware extends AuthMiddleware
{
	protected $SessionCookieName;

	public function __construct(\DI\Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container, $responseFactory);
		$this->SessionCookieName = $this->AppContainer->get('LoginControllerInstance')->GetSessionCookieName();
	}

	public function authenticate(Request $request)
	{
		if (!defined('GROCY_SHOW_AUTH_VIEWS'))
		{
			define('GROCY_SHOW_AUTH_VIEWS', true);
		}

		$sessionService = SessionService::getInstance();

		if (!isset($_COOKIE[$this->SessionCookieName]) || !$sessionService->IsValidSession($_COOKIE[$this->SessionCookieName]))
		{
			return null;
		}
		else
		{
			return $sessionService->GetUserBySessionKey($_COOKIE[$this->SessionCookieName]);
		}
	}
}
