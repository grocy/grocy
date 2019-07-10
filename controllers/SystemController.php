<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;
use \Grocy\Services\DatabaseMigrationService;
use \Grocy\Services\DemoDataGeneratorService;

class SystemController extends BaseController
{
	protected $ApplicationService;

	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ApplicationService = new ApplicationService();
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

		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl($this->GetEntryPageRelative()));
	}

	/**
	 * Get the entry page of the application based on the value of the entry page setting.
	 *
	 * We fallback to the about page when no entry page is specified or
	 * when the specified entry page has been disabled.
	 *
	 * @return string
	 */
	private function GetEntryPageRelative()
	{
		if (defined('GROCY_ENTRY_PAGE')) {
			$entryPage = constant('GROCY_ENTRY_PAGE');
		} else {
			$entryPage = 'stock';
		}

		// Stock
		if ($entryPage === 'stock' && constant('GROCY_FEATURE_FLAG_STOCK')) {
			return '/stockoverview';
		}

		// Shoppinglist
		if ($entryPage === 'shoppinglist' && constant('GROCY_FEATURE_FLAG_SHOPPINGLIST')) {
			return '/shoppinglist';
		}

		// Recipes
		if ($entryPage === 'recipes' && constant('GROCY_FEATURE_FLAG_RECIPES')) {
			return '/recipes';
		}

		// Chores
		if ($entryPage === 'chores' && constant('GROCY_FEATURE_FLAG_CHORES')) {
			return '/choresoverview';
		}

		// Tasks
		if ($entryPage === 'tasks' && constant('GROCY_FEATURE_FLAG_TASKS')) {
			return '/tasks';
		}

		// Batteries
		if ($entryPage === 'batteries' && constant('GROCY_FEATURE_FLAG_BATTERIES')) {
			return '/batteriesoverview';
		}

		if ($entryPage === 'equipment' && constant('GROCY_FEATURE_FLAG_EQUIPMENT')) {
			return '/equipment';
		}

		// Calendar
		if ($entryPage === 'calendar' && constant('GROCY_FEATURE_FLAG_CALENDAR')) {
			return '/calendar';
		}

		return '/about';
	}

	public function About(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'about', [
			'system_info' => $this->ApplicationService->GetSystemInfo(),
			'changelog' => $this->ApplicationService->GetChangelog()
		]);
	}
}
