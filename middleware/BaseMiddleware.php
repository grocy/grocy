<?php

namespace Grocy\Middleware;

use \Grocy\Services\ApplicationService;

class BaseMiddleware
{
	public function __construct(\DI\Container $container)
	{
		$this->AppContainer = $container;
		$this->ApplicationService = new ApplicationService();
	}

	protected $AppContainer;
	protected $ApplicationService;
}
