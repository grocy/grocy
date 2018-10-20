<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\LocalizationService;
use \Grocy\Services\UsersService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		$databaseService = new DatabaseService();
		$this->Database = $databaseService->GetDbConnection();
		
		$localizationService = new LocalizationService(GROCY_CULTURE);
		$this->LocalizationService = $localizationService;

		if (GROCY_MODE === 'prerelease')
		{
			$commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
			$commitDate = trim(exec('git log --date=iso --pretty="%cd" -n1 HEAD'));
			
			$container->view->set('version', "pre-release-$commitHash");
			$container->view->set('releaseDate', \substr($commitDate, 0, 19));
		}
		else
		{
			$applicationService = new ApplicationService();
			$versionInfo = $applicationService->GetInstalledVersion();
			$container->view->set('version', $versionInfo->Version);
			$container->view->set('releaseDate', $versionInfo->ReleaseDate);
		}

		$container->view->set('localizationStrings', $localizationService->GetCurrentCultureLocalizations());
		$container->view->set('L', function($text, ...$placeholderValues) use($localizationService)
		{
			return $localizationService->Localize($text, ...$placeholderValues);
		});
		$container->view->set('U', function($relativePath, $isResource = false) use($container)
		{
			return $container->UrlManager->ConstructUrl($relativePath, $isResource);
		});

		try {
			$usersService = new UsersService();
			if (defined('GROCY_USER_ID')) {
				$container->view->set('userSettings', $usersService->GetUserSettings(GROCY_USER_ID));
			}
		}
		catch (\Exception $ex)
		{
			// Happens when database is not initialised or migrated...
		}

		$this->AppContainer = $container;
	}

	protected $AppContainer;
	protected $Database;
	protected $LocalizationService;
}
