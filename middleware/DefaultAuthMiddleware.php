<?php

namespace Grocy\Middleware;

use Grocy\Services\DatabaseService;
use Grocy\Services\SessionService;
use Psr\Http\Message\ServerRequestInterface as Request;

class DefaultAuthMiddleware extends AuthMiddleware
{
	protected function authenticate(Request $request)
	{
		// First try to authenticate by API key
		$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);

		if ($user !== null)
		{
			return $user;
		}

		// Then by session cookie
		$auth = new SessionAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);
		return $user;
	}

	public static function ProcessLogin(array $postParams)
	{
		if (isset($postParams['username']) && isset($postParams['password']))
		{
			$db = DatabaseService::getInstance()->GetDbConnection();

			$user = $db->users()->where('username', $postParams['username'])->fetch();
			$inputPassword = $postParams['password'];
			$stayLoggedInPermanently = $postParams['stay_logged_in'] == 'on';

			if ($user !== null && password_verify($inputPassword, $user->password))
			{
				$sessionKey = SessionService::getInstance()->CreateSession($user->id, $stayLoggedInPermanently);
				self::SetSessionCookie($sessionKey);

				if (password_needs_rehash($user->password, PASSWORD_DEFAULT))
				{
					$user->update([
						'password' => password_hash($inputPassword, PASSWORD_DEFAULT)
					]);
				}

				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
