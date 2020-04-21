<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class CorsMiddleware extends BaseMiddleware
{
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = $handler->handle($request);

		//$routeContext = RouteContext::fromRequest($request);
		//$routingResults = $routeContext->getRoutingResults();
		//$methods = $routingResults->getAllowedMethods();
		//$requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

		$response = $handler->handle($request);

		$response = $response->withHeader('Access-Control-Allow-Origin', '*');
		$response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
		$response = $response->withHeader('Access-Control-Allow-Headers', '*');
		$response = $response->withStatus(204);

		return $response;
	}
}
