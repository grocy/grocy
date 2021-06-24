<?php

namespace Grocy\Controllers;

use Grocy\Services\DatabaseMigrationService;
use Grocy\Services\DemoDataGeneratorService;

class SystemController extends BaseController
{
	public function About(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'about', [
			'system_info' => $this->getApplicationService()->GetSystemInfo(),
			'changelog' => $this->getApplicationService()->GetChangelog()
		]);
	}

	public function BarcodeScannerTesting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'barcodescannertesting');
	}

	public function Root(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		// Schema migration is done here
		$databaseMigrationService = DatabaseMigrationService::getInstance();
		$databaseMigrationService->MigrateDatabase();

		if (GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease')
		{
			$demoDataGeneratorService = DemoDataGeneratorService::getInstance();
			$demoDataGeneratorService->PopulateDemoData();
		}

		return $response->withRedirect($this->AppContainer->get('UrlManager')->ConstructUrl($this->GetEntryPageRelative()));
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
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
		if (defined('GROCY_ENTRY_PAGE'))
		{
			$entryPage = constant('GROCY_ENTRY_PAGE');
		}
		else
		{
			$entryPage = 'stock';
		}

		// Stock
		if ($entryPage === 'stock' && constant('GROCY_FEATURE_FLAG_STOCK'))
		{
			return '/stockoverview';
		}

		// Shoppinglist
		if ($entryPage === 'shoppinglist' && constant('GROCY_FEATURE_FLAG_SHOPPINGLIST'))
		{
			return '/shoppinglist';
		}

		// Recipes
		if ($entryPage === 'recipes' && constant('GROCY_FEATURE_FLAG_RECIPES'))
		{
			return '/recipes';
		}

		// Chores
		if ($entryPage === 'chores' && constant('GROCY_FEATURE_FLAG_CHORES'))
		{
			return '/choresoverview';
		}

		// Tasks
		if ($entryPage === 'tasks' && constant('GROCY_FEATURE_FLAG_TASKS'))
		{
			return '/tasks';
		}

		// Batteries
		if ($entryPage === 'batteries' && constant('GROCY_FEATURE_FLAG_BATTERIES'))
		{
			return '/batteriesoverview';
		}

		if ($entryPage === 'equipment' && constant('GROCY_FEATURE_FLAG_EQUIPMENT'))
		{
			return '/equipment';
		}

		// Calendar
		if ($entryPage === 'calendar' && constant('GROCY_FEATURE_FLAG_CALENDAR'))
		{
			return '/calendar';
		}

		// Meal Plan
		if ($entryPage === 'mealplan' && constant('GROCY_FEATURE_FLAG_RECIPES'))
		{
			return '/mealplan';
		}

		return '/about';
	}
}
