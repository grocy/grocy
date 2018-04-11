<?php

namespace Grocy\Controllers;

use Grocy\Services\DatabaseService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		$this->AppContainer = $container;

		$databaseService = new DatabaseService();
		$this->Database = $databaseService->GetDbConnection();
	}

	protected $AppContainer;
	protected $Database;
}
