<?php

namespace Grocy\Controllers;

use Grocy\Services\SessionService;
use Grocy\Services\DatabaseService;
use Grocy\Services\ApplicationService;
use Grocy\Services\DatabaseMigrationService;
use Grocy\Services\DemoDataGeneratorService;

class LoginController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->SessionService = new SessionService();
	}

	protected $SessionService;

	public function ProcessLogin($request, $response, $args)
	{
		$postParams = $request->getParsedBody();
		if (isset($postParams['username']) && isset($postParams['password']))
		{
			if ($postParams['username'] === HTTP_USER && $postParams['password'] === HTTP_PASSWORD)
			{
				$sessionKey = $this->SessionService->CreateSession();
				setcookie('grocy_session', $sessionKey, time()+2592000); //30 days

				return $response->withRedirect('/');
			}
			else
			{
				return $response->withRedirect('/login?invalid=true');
			}
		}
		else
		{
			return $response->withRedirect('/login?invalid=true');
		}
	}

	public function LoginPage($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'login', [
			'title' => 'Login',
			'contentPage' => 'login.php'
		]);
	}

	public function Logout($request, $response, $args)
	{
		$this->SessionService->RemoveSession($_COOKIE['grocy_session']);
		return $response->withRedirect('/');
	}

	public function Root($request, $response, $args)
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

		return $response->withRedirect('/stockoverview');
	}
}
