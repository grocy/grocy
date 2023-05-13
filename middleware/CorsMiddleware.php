<?php

namespace Grocy\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CorsMiddleware
{
	private $responseFactory;

	public function __construct(ResponseFactoryInterface $responseFactory)
	{
		$this->responseFactory = $responseFactory;
	}

	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		if ($request->getMethod() == 'OPTIONS')
		{
			$response = $this->responseFactory->createResponse(200);
		}
		else
		{
			$response = $handler->handle($request);
		}

		$response = $response->withHeader('Access-Control-Allow-Origin', '*');
		$response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
		$response = $response->withHeader('Access-Control-Allow-Headers', '*');

		return $response;
	}
}
