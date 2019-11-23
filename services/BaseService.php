<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\LocalizationService;

class BaseService
{
	public function __construct() {
	}

    protected function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

	protected function getdatabase()
	{
		return $this->DatabaseService->GetDbConnection();
	}

	protected  function getLocalizationService()
	{
		return LocalizationService::getInstance(GROCY_CULTURE);
	}
}
