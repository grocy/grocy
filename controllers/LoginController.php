<?php

namespace Grocy\Controllers;

use \Grocy\Services\SessionService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\DatabaseMigrationService;
use \Grocy\Services\DemoDataGeneratorService;

class LoginController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->SessionService = new SessionService();
	}

	protected $SessionService;

	public function ProcessLogin(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$postParams = $request->getParsedBody();
		if (isset($postParams['username']) && isset($postParams['password']))
		{
			if ($postParams['username'] === HTTP_USER && $postParams['password'] === HTTP_PASSWORD)
			{
				$sessionKey = $this->SessionService->CreateSession();
				setcookie('grocy_session', $sessionKey, time() + 31536000); // Cookie expires in 1 year, but session validity is up to SessionService

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
		$this->SessionService->RemoveSession($_COOKIE['grocy_session']);
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
}
