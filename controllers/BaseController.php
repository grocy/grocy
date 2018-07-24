<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\LocalizationService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		$databaseService = new DatabaseService();
		$this->Database = $databaseService->GetDbConnection();
		
		$localizationService = new LocalizationService(GROCY_CULTURE);
		$this->LocalizationService = $localizationService;

		$applicationService = new ApplicationService();
		$versionInfo = $applicationService->GetInstalledVersion();
		$container->view->set('version', $versionInfo->Version);
		$container->view->set('releaseDate', $versionInfo->ReleaseDate);
		$container->view->set('isEmbeddedInstallation', $applicationService->IsEmbeddedInstallation());

		$container->view->set('localizationStrings', $localizationService->GetCurrentCultureLocalizations());
		$container->view->set('L', function($text, ...$placeholderValues) use($localizationService)
		{
			return $localizationService->Localize($text, ...$placeholderValues);
		});
		$container->view->set('U', function($relativePath, $isResource = false) use($container)
		{
			return $container->UrlManager->ConstructUrl($relativePath, $isResource);
		});

		$this->AppContainer = $container;
	}

	protected $AppContainer;
	protected $Database;
	protected $LocalizationService;
}
