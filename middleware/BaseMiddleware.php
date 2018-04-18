<?php

namespace Grocy\Middleware;

class BaseMiddleware
{
	public function __construct(\Slim\Container $container) {
		$this->AppContainer = $container;
	}

	protected $AppContainer;
}
