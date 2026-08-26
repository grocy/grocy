<?php

namespace Grocy\Middleware\Auth;

use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReverseProxyAuthMiddleware extends BaseAuthMiddleware
{
	public function AuthenticateRequest(Request $request)
	{
		define('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION', true);

		// Try to use regular API Key authentication (applies when the reverse proxy is configured to be bypassed for API routes)
		if ($this->IsApiRoute)
		{
			$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
			$user = $auth->AuthenticateRequest($request);
			if ($user !== null)
			{
				return $user;
			}
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
			if (count($username) !== 1 || (count($username) === 1 && strlen($username[0]) === 0))
			{
				// Invalid configuration of Proxy
				throw new \Exception('ReverseProxyAuthMiddleware: ' . GROCY_REVERSE_PROXY_AUTH_HEADER . ' header is missing or invalid');
			}
			$username = $username[0];
		}

		$db = DatabaseService::GetInstance()->GetDbConnection();
		$user = $db->users()->where('username', $username)->fetch();
		if ($user == null)
		{
			$user = UsersService::GetInstance()->CreateUser($username, '', '', '');
		}

		return $user;
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
