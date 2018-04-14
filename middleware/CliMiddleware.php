<?php

namespace Grocy\Middleware;

class CliMiddleware extends BaseMiddleware
{
	public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next)
	{
		if (PHP_SAPI !== 'cli')
		{
			$response->write('Please call this only from CLI');
			return $response->withHeader('Content-Type', 'text/plain')->withStatus(400);
		}
		else
		{
			$response = $next($request, $response, $next);
			return $response->withHeader('Content-Type', 'text/plain');
		}
	}
}
