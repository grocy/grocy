<?php

namespace Grocy\Middleware\Auth;

use Grocy\Services\SessionService;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionAuthMiddleware extends BaseAuthMiddleware
{
	public function AuthenticateRequest(Request $request)
	{
		$sessionService = SessionService::GetInstance();

		// First check if the access token is valid
		if (isset($_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS]) && $sessionService->ValidateToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS]))
		{
			return $sessionService->GetUserByToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS]);
		}
		else
		{
			// We have no valid access token => remove the cookie and check if we have a valid remember me token
			self::RemoveSessionCookie(SessionService::SESSION_TOKEN_TYPE_ACCESS);
			if (isset($_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]) && $sessionService->ValidateToken(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]))
			{
				$user = $sessionService->GetUserByToken(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]);
				if ($user !== null)
				{
					// Remember me token is valid => create a new access token and extend the lifetime of the remember me token cookie
					$token = $sessionService->CreateToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $user->id, GetClientUserAgent());
					self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_ACCESS, $token);
					self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]);
					return $user;
				}
			}

			// Remember me token is also not valid => remove the cookie
			self::RemoveSessionCookie(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME);
		}

		return null;
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
