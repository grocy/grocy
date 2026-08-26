<?php

namespace Grocy\Middleware\Auth;

use Grocy\Services\DatabaseService;
use Grocy\Services\SessionService;
use Psr\Http\Message\ServerRequestInterface as Request;

class DefaultAuthMiddleware extends BaseAuthMiddleware
{
	protected function AuthenticateRequest(Request $request)
	{
		if ($this->IsApiRoute)
		{
			// Session cookie or API Key is ok
			$auth = new SessionAuthMiddleware($this->AppContainer, $this->ResponseFactory);
			$user = $auth->AuthenticateRequest($request);
			if ($user !== null)
			{
				return $user;
			}

			$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
			$user = $auth->AuthenticateRequest($request);
			return $user;
		}
		else
		{
			// Only session cookie is ok
			$auth = new SessionAuthMiddleware($this->AppContainer, $this->ResponseFactory);
			$user = $auth->AuthenticateRequest($request);
			return $user;
		}
	}

	public static function ProcessLogin(array $postParams)
	{
		if (empty($postParams['username']) || empty($postParams['password']))
		{
			return false;
		}

		$db = DatabaseService::GetInstance()->GetDbConnection();

		$user = $db->users()->where('username', $postParams['username'])->fetch();
		$inputPassword = $postParams['password'];
		$rememberMe = isset($postParams['remember_me']) && $postParams['remember_me'] == 'on';

		if ($user !== null && password_verify($inputPassword, $user->password))
		{
			$token = SessionService::GetInstance()->CreateToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $user->id, GetClientUserAgent());
			self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_ACCESS, $token);

			if ($rememberMe)
			{
				$token = SessionService::GetInstance()->CreateToken(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $user->id, GetClientUserAgent());
				self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $token);
			}

			if (password_needs_rehash($user->password, PASSWORD_ARGON2ID))
			{
				$user->update([
					'password' => password_hash($inputPassword, PASSWORD_ARGON2ID)
				]);
			}

			return true;
		}
		else
		{
			return false;
		}
	}
}
