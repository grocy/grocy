<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;
use \Grocy\Services\DatabaseMigrationService;

class CliController extends BaseController
{
	public function RecreateDemo(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$applicationService = new ApplicationService();
		if ($applicationService->IsDemoInstallation())
		{
			$databaseMigrationService = new DatabaseMigrationService();
			$databaseMigrationService->RecreateDemo();
		}
	}
}
