<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\LocalizationService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		$this->AppContainer = $container;

		$databaseService = new DatabaseService();
		$this->Database = $databaseService->GetDbConnection();

		$applicationService = new ApplicationService();
		$container->view->set('version', $applicationService->GetInstalledVersion());

		$localizationService = new LocalizationService(CULTURE);
		$container->view->set('localizationStrings', $localizationService->GetCurrentCultureLocalizations());
		$container->view->set('L', function($text, ...$placeholderValues) use($localizationService)
		{
			return $localizationService->Localize($text, ...$placeholderValues);
		});
	}

	protected $AppContainer;
	protected $Database;
}
