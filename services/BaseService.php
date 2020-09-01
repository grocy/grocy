<?php

namespace Grocy\Services;

class BaseService
{
	private static $instances = [];

	public function __construct()
	{
	}

	public static function getInstance()
	{
		$className = get_called_class();

		if (!isset(self::$instances[$className]))
		{
			self::$instances[$className] = new $className();
		}

		return self::$instances[$className];
	}

	protected function getBatteriesService()
	{
		return BatteriesService::getInstance();
	}

	protected function getChoresService()
	{
		return ChoresService::getInstance();
	}

	protected function getDatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	protected function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

	protected function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_LOCALE);
	}

	protected function getStockservice()
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
}
