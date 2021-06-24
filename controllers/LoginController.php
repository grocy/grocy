<?php

namespace Grocy\Controllers;

use Grocy\Services\SessionService;

class LoginController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function LoginPage(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'login');
	}

	public function Logout(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$this->getSessionService()->RemoveSession($_COOKIE[SessionService::SESSION_COOKIE_NAME]);
		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl('/'));
	}

	public function ProcessLogin(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
