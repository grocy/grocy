<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\LocalizationService;
use \Grocy\Services\UsersService;

class BaseController
{
	public function __construct(\Slim\Container $container) {
		#$fp = fopen('/config/data/sql.log', 'a');
        #$time_start = microtime(true);

		$this->AppContainer = $container;
		#fwrite($fp, "%%% Login controller - parent construstor total time : " . round((microtime(true) - $time_start),6) . "\n");
		#fclose($fp);
	}

	private function render($response, $page, $data = [])
	{
		$container = $this->AppContainer;

		$versionInfo = $this->getApplicationService()->GetInstalledVersion();
		$container->view->set('version', $versionInfo->Version);
		$container->view->set('releaseDate', $versionInfo->ReleaseDate);
		#fwrite($fp, "%%% Login controller - parent construstor application service time : " . round((microtime(true) - $time_start),6) . "\n");

		$container->view->set('__t', function(string $text, ...$placeholderValues) use($this->getLocalizationService())
		{
			return $this->getLocalizationService()->__t($text, $placeholderValues);
		});
		$container->view->set('__n', function($number, $singularForm, $pluralForm) use($this->getLocalizationService())
		{
			return $this->getLocalizationService()->__n($number, $singularForm, $pluralForm);
		});
		$container->view->set('GettextPo', $this->getLocalizationService()->GetPoAsJsonString());

		$container->view->set('U', function($relativePath, $isResource = false) use($container)
		{
			return $container->UrlManager->ConstructUrl($relativePath, $isResource);
		});

		$embedded = false;
		if (isset($container->request->getQueryParams()['embedded']))
		{
			$embedded = true;
		}
		$container->view->set('embedded', $embedded);

		$constants = get_defined_constants();
		foreach ($constants as $constant => $value)
		{
			if (substr($constant, 0, 19) !== 'GROCY_FEATURE_FLAG_')
			{
				unset($constants[$constant]);
			}
		}
		$container->view->set('featureFlags', $constants);

		$this->AppContainer = $container;

		return $this->AppContainer->view->render($response, $page, $data);
	}

	private function renderPage($response, $page, $data = [])
	{
		$container = $this->AppContainer;
		$container->view->set('userentitiesForSidebar', $this->getDatabase()->userentities()->where('show_in_sidebar_menu = 1')->orderBy('name'));
		try
		{
			$usersService = new UsersService();
			if (defined('GROCY_USER_ID'))
			{
				$container->view->set('userSettings', $usersService->GetUserSettings(GROCY_USER_ID));
			}
			else
			{
				$container->view->set('userSettings', null);
			}
		}
		catch (\Exception $ex)
		{
			// Happens when database is not initialised or migrated...
		}

		$this->AppContainer = $container;
		return $this->render($response, $page, $data);
	}

    private function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

    private function getDatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	private function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_CULTURE);
	}

	private function getApplicationservice()
	{
		return ApplicationService::getInstance();
	}

	private $userfieldsService = null;

	private function getUserfieldsService()
	{
		if($this->userfieldsService == null)
		{
			$this->userfieldsService = new UserfieldsService();
		}
		return $this->userfieldsService;
	}

	protected $AppContainer;
}
