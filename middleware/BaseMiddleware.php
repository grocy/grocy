<?php

namespace Grocy\Middleware;

use \Grocy\Services\ApplicationService;

class BaseMiddleware
{
	public function __construct(\Slim\Container $container)
	{
		$this->AppContainer = $container;
		$this->ApplicationService = ApplicationService::getInstance();
	}

	protected $AppContainer;
	protected $ApplicationService;
}
