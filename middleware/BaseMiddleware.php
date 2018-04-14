<?php

namespace Grocy\Middleware;

class BaseMiddleware
{
	public function __construct(\Slim\Container $container) {
		$this->container = $container;
	}

	protected $container;
}
