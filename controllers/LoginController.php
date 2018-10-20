<?php

namespace Grocy\Controllers;

use \Grocy\Services\SessionService;
use \Grocy\Services\DatabaseMigrationService;
use \Grocy\Services\DemoDataGeneratorService;

class LoginController extends BaseController
{
	public function __construct(\Slim\Container $container, string $sessionCookieName)
	{
		parent::__construct($container);
		$this->SessionService = new SessionService();
		$this->SessionCookieName = $sessionCookieName;
	}

	protected $SessionService;
	protected $SessionCookieName;

	public function ProcessLogin(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$postParams = $request->getParsedBody();
		if (isset($postParams['username']) && isset($postParams['password']))
		{
			$user = $this->Database->users()->where('username', $postParams['username'])->fetch();
			$inputPassword = $postParams['password'];
			$stayLoggedInPermanently = $postParams['stay_logged_in'] == 'on';

			if ($user !== null && password_verify($inputPassword, $user->password))
			{
				$sessionKey = $this->SessionService->CreateSession($user->id, $stayLoggedInPermanently);
				setcookie($this->SessionCookieName, $sessionKey, intval(time() + 31220640000)); // Cookie expires in 999 years, but session validity is up to SessionService

				if (password_needs_rehash($user->password, PASSWORD_DEFAULT))
				{
					$user->update(array(
						'password' => password_hash($inputPassword, PASSWORD_DEFAULT)
					));
				}

				return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/'));
			}
			else
			{
				return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login?invalid=true'));
			}
		}
		else
		{
			return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login?invalid=true'));
		}
	}

	public function LoginPage(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'login');
	}

	public function Logout(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$this->SessionService->RemoveSession($_COOKIE[$this->SessionCookieName]);
		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/'));
	}

	public function Root(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		// Schema migration is done here
		$databaseMigrationService = new DatabaseMigrationService();
		$databaseMigrationService->MigrateDatabase();

		if (GROCY_IS_DEMO_INSTALL)
		{
			$demoDataGeneratorService = new DemoDataGeneratorService();
			$demoDataGeneratorService->PopulateDemoData();
		}

		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/stockoverview'));
	}

	public function GetSessionCookieName()
	{
		return $this->SessionCookieName;
	}
}
