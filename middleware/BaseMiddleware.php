<?php

namespace Grocy\Middleware;

use Grocy\Services\ApplicationService;

class BaseMiddleware
{
	protected $AppContainer;

	protected $ApplicationService;

	public function __construct(\DI\Container $container)
	{
		$this->AppContainer = $container;
		$this->ApplicationService = ApplicationService::getInstance();
	}
}
