<?php

namespace Grocy\Middleware;

use \Grocy\Services\ApplicationService;

class BaseMiddleware
{
	public function __construct(\Slim\Container $container)
	{
		$this->AppContainer = $container;
		$this->ApplicationService = new ApplicationService();
	}

	protected $AppContainer;
	protected $ApplicationService;
}
