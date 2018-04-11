<?php

namespace Grocy\Middleware;

class CliMiddleware
{
	public function __construct(\Slim\Container $container) {
		$this->container = $container;
	}

	protected $container;

	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		$response = $next($request, $response);

		if (PHP_SAPI !== 'cli')
		{
			$response->write('Please call this only from CLI');
			return $response->withHeader('Content-Type', 'text/plain')->withStatus(400);
		}

		return $response->withHeader('Content-Type', 'text/plain');
	}
}
