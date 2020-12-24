<?php

namespace Grocy\Middleware;

use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReverseProxyAuthMiddleware extends AuthMiddleware
{
	public function authenticate(Request $request)
	{
		$db = DatabaseService::getInstance()->GetDbConnection();

		if (!defined('GROCY_SHOW_AUTH_VIEWS'))
		{
			define('GROCY_SHOW_AUTH_VIEWS', false);
		}

		// API key authentication is also ok
		$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);
		if ($user !== null)
		{
			return $user;
		}

		$username = $request->getHeader(GROCY_REVERSE_PROXY_AUTH_HEADER);
		if (count($username) !== 1)
		{
			// Invalid configuration of Proxy
			throw new \Exception('ReverseProxyAuthMiddleware: Invalid username from proxy: ' . var_dump($username));
		}
		$username = $username[0];

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
