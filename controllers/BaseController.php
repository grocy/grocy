<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		$this->AppContainer = $container;

		$databaseService = new DatabaseService();
		$this->Database = $databaseService->GetDbConnection();

		$applicationService = new ApplicationService();
		$container->view->set('version', $applicationService->GetInstalledVersion());
	}

	protected $AppContainer;
	protected $Database;
}
