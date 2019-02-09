<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;
use \Grocy\Services\DatabaseMigrationService;
use \Grocy\Services\DemoDataGeneratorService;

class SystemController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ApplicationService = new ApplicationService();
	}

	protected $ApplicationService;

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

	public function About(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'about', [
			'system_info' => $this->ApplicationService->GetSystemInfo()
		]);
	}
}
