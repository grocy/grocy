<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;
use \Grocy\Services\LocalizationService;
use \Grocy\Services\StockService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;
use \Grocy\Services\BatteriesService;
use \Grocy\Services\CalendarService;
use \Grocy\Services\SessionService;
use \Grocy\Services\RecipesService;
use \Grocy\Services\TasksService;
use \Grocy\Services\FilesService;
use \Grocy\Services\ChoresService;
use \Grocy\Services\ApiKeyService;

class BaseController
{
	public function __construct(\DI\Container $container) {

		$this->AppContainer = $container;
		$this->View = $container->get('view');
	}

	protected function render($response, $page, $data = [])
	{
		$container = $this->AppContainer;

		$versionInfo = $this->getApplicationService()->GetInstalledVersion();
		$this->View->set('version', $versionInfo->Version);
		$this->View->set('releaseDate', $versionInfo->ReleaseDate);

        $localizationService = $this->getLocalizationService();
		$this->View->set('__t', function(string $text, ...$placeholderValues) use($localizationService)
		{
			return $localizationService->__t($text, $placeholderValues);
		});
		$this->View->set('__n', function($number, $singularForm, $pluralForm) use($localizationService)
		{
			return $localizationService->__n($number, $singularForm, $pluralForm);
		});
		$this->View->set('GettextPo', $localizationService->GetPoAsJsonString());

		$this->View->set('U', function($relativePath, $isResource = false) use($container)
		{
			return $container->get('UrlManager')->ConstructUrl($relativePath, $isResource);
		});

		$embedded = false;
		if (isset($_GET['embedded']))
		{
			$embedded = true;
		}
		$this->View->set('embedded', $embedded);

		$constants = get_defined_constants();
		foreach ($constants as $constant => $value)
		{
			if (substr($constant, 0, 19) !== 'GROCY_FEATURE_FLAG_')
			{
				unset($constants[$constant]);
			}
		}
		$this->View->set('featureFlags', $constants);

		return $this->View->render($response, $page, $data);
	}

	protected function renderPage($response, $page, $data = [])
	{
		$this->View->set('userentitiesForSidebar', $this->getDatabase()->userentities()->where('show_in_sidebar_menu = 1')->orderBy('name'));
		try
		{
			$usersService = $this->getUsersService();
			if (defined('GROCY_USER_ID'))
			{
				$this->View->set('userSettings', $usersService->GetUserSettings(GROCY_USER_ID));
			}
			else
			{
				$this->View->set('userSettings', null);
			}
		}
		catch (\Exception $ex)
		{
			// Happens when database is not initialised or migrated...
		}

		return $this->render($response, $page, $data);
	}

    protected function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

    protected function getDatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	protected function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_CULTURE);
	}

	protected function getApplicationservice()
	{
		return ApplicationService::getInstance();
	}

	protected function getBatteriesService()
	{
		return BatteriesService::getInstance();
	}

	protected function getCalendarService()
	{
		return CalendarService::getInstance();
	}

    protected function getSessionService()
	{
		return SessionService::getInstance();
	}

	protected function getRecipesService()
	{
		return RecipesService::getInstance();
	}

	protected function getStockService()
	{
		return StockService::getInstance();
	}

    protected function getTasksService()
	{
		return TasksService::getInstance();
	}

    protected function getUsersService()
	{
		return UsersService::getInstance();
	}

	protected function getUserfieldsService()
	{
		return UserfieldsService::getInstance();
	}

	protected function getApiKeyService()
	{
		return ApiKeyService::getInstance();
	}

	protected function getChoresService()
	{
		return ChoresService::getInstance();
	}

	protected function getFilesService()
	{
		return FilesService::getInstance();
	}
	
	protected $AppContainer;
}
