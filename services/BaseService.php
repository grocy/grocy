<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\LocalizationService;

class BaseService
{
	public function __construct() {
	}

	private static $instance = null;

	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self();
		}

		return self::$instance;
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
