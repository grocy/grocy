<?php

namespace Grocy\Middleware;

use Grocy\Services\ApplicationService;
use DI\Container;

class BaseMiddleware
{
	protected $AppContainer;
	protected $ApplicationService;

	public function __construct(Container $container)
	{
		$this->AppContainer = $container;
		$this->ApplicationService = ApplicationService::getInstance();
	}
}
