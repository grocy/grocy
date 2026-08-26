<?php

namespace Grocy\Controllers;

use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController extends BaseController
{
	public function LoginPage(Request $request, Response $response, array $args)
	{
		return $this->RenderPage($response, 'login');
	}

	public function Logout(Request $request, Response $response, array $args)
	{
		SessionService::GetInstance()->DeleteToken(SessionService::SESSION_TOKEN_TYPE_ACCESS, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_ACCESS]);
		if (isset($_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]))
		{
			SessionService::GetInstance()->DeleteToken(SessionService::SESSION_TOKEN_TYPE_REMEMBER_ME, $_COOKIE[SessionService::SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME]);
		}

		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/'));
	}

	public function ProcessLogin(Request $request, Response $response, array $args)
	{
		$authMiddlewareClass = GROCY_AUTH_CLASS;

		$postParams = $request->getParsedBody();
		if (isset($postParams['password_base64']))
		{
			$postParams['password'] = base64_decode($postParams['password_base64']);
		}
		unset($postParams['password_base64']);

		if ($authMiddlewareClass::ProcessLogin($postParams))
		{
			return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/'));
		}
		else
		{
			return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/login?invalid=true'));
		}
	}
}
