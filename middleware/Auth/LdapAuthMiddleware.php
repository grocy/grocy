<?php

namespace Grocy\Middleware\Auth;

use Grocy\Services\DatabaseService;
use Grocy\Services\SessionService;
use Grocy\Services\UsersService;
use Psr\Http\Message\ServerRequestInterface as Request;

class LdapAuthMiddleware extends BaseAuthMiddleware
{
	public function AuthenticateRequest(Request $request)
	{
		define('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION', true);

		$auth = new DefaultAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		return $auth->AuthenticateRequest($request);
	}

	public static function ProcessLogin(array $postParams)
	{
		if (empty($postParams['username']) || empty($postParams['password']))
		{
			return false;
		}

		if ($connect = ldap_connect(GROCY_LDAP_ADDRESS))
		{
			ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

			// Bind with service account to retrieve user DN
			if (ldap_bind($connect, GROCY_LDAP_BIND_DN, GROCY_LDAP_BIND_PW))
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
					ldap_close($connect);
					return false;
				}
			}
			else
			{
				// Bind authentication failed
				throw new \Exception('LDAP error: ' . ldap_error($connect));
			}

			// Bind with user account to validate password
			if (ldap_bind($connect, $ldapDistinguishedName, $postParams['password']))
			{
				$db = DatabaseService::GetInstance()->GetDbConnection();
				$user = $db->users()->where('username', $ldapUidAttribute)->fetch();
				if ($user == null)
				{
					$user = UsersService::GetInstance()->CreateUser($ldapUidAttribute, $ldapFirstName, $ldapLastName, '');
				}

				$token = SessionService::GetInstance()->CreateToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $user->id, GetClientUserAgent());
				self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_ACCESS, $token);

				$rememberMe = isset($postParams['remember_me']) && $postParams['remember_me'] == 'on';
				if ($rememberMe)
				{
					$token = SessionService::GetInstance()->CreateToken(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $user->id, GetClientUserAgent());
					self::SetSessionCookie(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $token);
				}

				return true;
			}
			else
			{
				// User authentication failed
				ldap_close($connect);
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
