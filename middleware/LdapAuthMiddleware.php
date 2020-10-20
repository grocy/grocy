<?php

namespace Grocy\Middleware;

use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Grocy\Services\SessionService;
use Psr\Http\Message\ServerRequestInterface as Request;

class LdapAuthMiddleware extends AuthMiddleware
{
	public function authenticate(Request $request)
	{
		// TODO: Reuse DefaultAuthMiddleware->authenticate somehow

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
		if ($connect = ldap_connect(GROCY_LDAP_ADDRESS))
		{
			ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

			if ($bind = ldap_bind($connect, GROCY_LDAP_DOMAIN . '\\' . $postParams['username'], $postParams['password']))
			{
				$fields = '(|(samaccountname=*' . $postParams['username'] . '*))';

				$search = ldap_search($connect, GROCY_LDAP_BASE_DN, $fields);
				$result = ldap_get_entries($connect, $search);

				$ldapFirstName = $result[0]['givenname'][0];
				$ldapLastName = $result[0]['sn'][0];

				ldap_close($connect);

				$db = DatabaseService::getInstance()->GetDbConnection();
				$user = $db->users()->where('username', $postParams['username'])->fetch();
				if ($user == null)
				{
					$user = UsersService::getInstance()->CreateUser($postParams['username'], $ldapFirstName, $ldapLastName, '');
				}

				$sessionKey = SessionService::getInstance()->CreateSession($user->id, $postParams['stay_logged_in'] == 'on');
				self::SetSessionCookie($sessionKey);

				return true;
			}
			else
			{
				// LDAP authentication failed
				return false;
			}
		}
		else
		{
			// LDAP connection failed
			return false;
		}
	}
}
