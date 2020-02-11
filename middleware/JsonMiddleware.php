<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class JsonMiddleware extends BaseMiddleware
{
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = $handler->handle($request);

		if ($response->hasHeader('Content-Disposition'))
		{
			return $response;
		}
		else
		{
			return $response->withHeader('Content-Type', 'application/json');
		}
	}
}
