<?php

namespace Grocy\Controllers;

use \Grocy\Services\SessionService;
use \Grocy\Services\ApplicationService;
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

			if ($user !== null && password_verify($inputPassword, $user->password))
			{
				$sessionKey = $this->SessionService->CreateSession($user->id);
				setcookie($this->SessionCookieName, $sessionKey, time() + 31536000); // Cookie expires in 1 year, but session validity is up to SessionService
				define('GROCY_USER_USERNAME', $user->username);
				define('GROCY_USER_ID', $user->id);

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

		$applicationService = new ApplicationService();
		if ($applicationService->IsDemoInstallation())
		{
			$demoDataGeneratorService = new DemoDataGeneratorService();
			$demoDataGeneratorService->PopulateDemoData();
		}

		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/stockoverview'));
	}

	public function UsersList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'users', [
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'userform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'userform', [
				'user' =>  $this->Database->users($args['userId']),
				'mode' => 'edit'
			]);
		}
	}

	public function GetSessionCookieName()
	{
		return $this->SessionCookieName;
	}
}
