<?php

namespace Grocy\Services;

#use \Grocy\Services\DatabaseService;
#use \Grocy\Services\LocalizationService;

class BaseService
{
	public function __construct() {
	}

	private static $instances = array();

	public static function getInstance()
	{
		$className = get_called_class();
		if(!isset(self::$instances[$className])) 
		{
			self::$instances[$className] = new $className();
		}

		return self::$instances[$className];
	}

    protected function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

	protected function getDatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	protected  function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_CULTURE);
	}

	protected function getStockservice()
	{
		return StockService::getInstance();
	}

	protected function getTasksService()
	{
		return TasksService::getInstance();
	}

	protected function getChoresService()
	{
		return ChoresService::getInstance();
	}

	protected function getBatteriesService()
	{
		return BatteriesService::getInstance();
	}

	protected function getUsersService()
	{
		return UsersService::getInstance();
	}
}
