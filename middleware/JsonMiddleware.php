<?php

namespace Grocy\Middleware;

class JsonMiddleware
{
	public function __construct(\Slim\Container $container) {
		$this->container = $container;
	}

	protected $container;

	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$response = $next($request, $response, $next);
		return $response->withHeader('Content-Type', 'application/json');
	}
}
