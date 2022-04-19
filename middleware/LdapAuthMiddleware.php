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
		define('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION', true);

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

			// bind with service account to retrieve user DN
			if ($bind = ldap_bind($connect, GROCY_LDAP_BIND_DN, GROCY_LDAP_BIND_PW))
			{
				$filter = '(&(' . GROCY_LDAP_UID_ATTR . '=' . $postParams['username'] . ')' . GROCY_LDAP_USER_FILTER . ')';

				$search = ldap_search($connect, GROCY_LDAP_BASE_DN, $filter);
				if ($search === false)
				{
					throw new \Exception('LDAP error: ' . ldap_error($connect));
				}

				$result = ldap_get_entries($connect, $search);
				if ($result === false)
				{
					throw new \Exception('LDAP error: ' . ldap_error($connect));
				}

				$ldapFirstName = $result[0]['givenname'][0];
				$ldapLastName = $result[0]['sn'][0];
				$ldapDistinguishedName = $result[0]['dn'];
				$ldapUidAttribute = $result[0][strtolower(GROCY_LDAP_UID_ATTR)][0];

				if (is_null($ldapDistinguishedName))
				{
					// User not found
					return false;
				}
			}
			else
			{
				// Bind authentication failed
				return false;
			}

			// bind with user account to validate password
			if ($bind = ldap_bind($connect, $ldapDistinguishedName, $postParams['password']))
			{
				ldap_close($connect);

				$db = DatabaseService::getInstance()->GetDbConnection();
				$user = $db->users()->where('username', $ldapUidAttribute)->fetch();
				if ($user == null)
				{
					$user = UsersService::getInstance()->CreateUser($ldapUidAttribute, $ldapFirstName, $ldapLastName, '');
				}

				$sessionKey = SessionService::getInstance()->CreateSession($user->id, $postParams['stay_logged_in'] == 'on');
				self::SetSessionCookie($sessionKey);

				return true;
			}
			else
			{
				ldap_close($connect);

				// User authentication failed
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
