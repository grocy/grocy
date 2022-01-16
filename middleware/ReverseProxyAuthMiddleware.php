<?php

namespace Grocy\Middleware;

use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReverseProxyAuthMiddleware extends AuthMiddleware
{
	public function authenticate(Request $request)
	{
		define('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION', true);

		$db = DatabaseService::getInstance()->GetDbConnection();

		// API key authentication is also ok
		$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);
		if ($user !== null)
		{
			return $user;
		}

		if (GROCY_REVERSE_PROXY_AUTH_USE_ENV)
		{
			if (!isset($_SERVER[GROCY_REVERSE_PROXY_AUTH_HEADER]))
			{
				// Variable is not set
				throw new \Exception('ReverseProxyAuthMiddleware: ' . GROCY_REVERSE_PROXY_AUTH_HEADER . ' env variable is missing (could not be found in $_SERVER array)');
			}

			$username = $_SERVER[GROCY_REVERSE_PROXY_AUTH_HEADER];
			if (strlen($username) === 0)
			{
				// Variable is empty
				throw new \Exception('ReverseProxyAuthMiddleware: ' . GROCY_REVERSE_PROXY_AUTH_HEADER . ' env variable is invalid');
			}
		}
		else
		{
			$username = $request->getHeader(GROCY_REVERSE_PROXY_AUTH_HEADER);
			if (count($username) !== 1)
			{
				// Invalid configuration of Proxy
				throw new \Exception('ReverseProxyAuthMiddleware: ' . GROCY_REVERSE_PROXY_AUTH_HEADER . ' header is missing or invalid');
			}
			$username = $username[0];
		}

		$user = $db->users()->where('username', $username)->fetch();
		if ($user == null)
		{
			$user = UsersService::getInstance()->CreateUser($username, '', '', '');
		}

		return $user;
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
