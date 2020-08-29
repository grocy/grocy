<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;

use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;

class ReverseProxyAuthMiddleware extends AuthMiddleware
{
	function authenticate(Request $request)
	{
		if (!defined('GROCY_SHOW_AUTH_VIEWS'))
		{
			define('GROCY_SHOW_AUTH_VIEWS', false);
		}

		$db = DatabaseService::getInstance()->GetDbConnection();

		$username = $request->getHeader(GROCY_REVERSE_PROXY_AUTH_HEADER);

		if (count($username) !== 1)
		{
			// Invalid configuration of Proxy
			throw new \Exception("ReverseProxyAuthMiddleware: Invalid username from proxy: " . var_dump($username));
		}

		$username = $username[0];

		$user = $db->users()->where('username', $username)->fetch();

		if ($user == null)
		{
			$user = UsersService::getInstance()->CreateUser($username, '', '', '');
		}

		return $user;
	}
}
