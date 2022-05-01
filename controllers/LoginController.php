<?php

namespace Grocy\Controllers;

use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends BaseController
{
	public function LoginPage(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'login');
	}

	public function Logout(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		$this->getSessionService()->RemoveSession($_COOKIE[SessionService::SESSION_COOKIE_NAME]);
		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/'));
	}

	public function ProcessLogin(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		$authMiddlewareClass = GROCY_AUTH_CLASS;
		if ($authMiddlewareClass::ProcessLogin($this->GetParsedAndFilteredRequestBody($request)))
		{
			return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/'));
		}
		else
		{
			return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/login?invalid=true'));
		}
	}
}
