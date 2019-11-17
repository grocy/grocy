<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\LocalizationService;

class BaseService
{
	public function __construct() {
		$this->DatabaseService = DatabaseService::getInstance();
		$this->Database = $this->DatabaseService->GetDbConnection();

		$localizationService = new LocalizationService(GROCY_CULTURE);
		$this->LocalizationService = $localizationService;
	}

	protected $DatabaseService;
	protected $Database;
	protected $LocalizationService;
}
