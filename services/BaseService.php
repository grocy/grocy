<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;

class BaseService
{
	public function __construct() {
		$this->DatabaseService = new DatabaseService();
		$this->Database = $this->DatabaseService->GetDbConnection();
	}

	protected $DatabaseService;
	protected $Database;
}
