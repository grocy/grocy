<?php

namespace Grocy\Middleware\Auth;

use Grocy\Services\SessionService;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionAuthMiddleware extends BaseAuthMiddleware
{
	public function AuthenticateRequest(Request $request)
	{
		$sessionService = SessionService::GetInstance();

		if (isset($_COOKIE[SessionService::SESSION_COOKIE_NAME]) && $sessionService->IsValidSession($_COOKIE[SessionService::SESSION_COOKIE_NAME]))
		{
			return $sessionService->GetUserBySessionKey($_COOKIE[SessionService::SESSION_COOKIE_NAME]);
		}
		else
		{
			return null;
		}
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
