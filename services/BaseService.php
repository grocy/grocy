<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\LocalizationService;

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

	protected function getdatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	protected  function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_CULTURE);
	}
}
