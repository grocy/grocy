<?php

namespace Grocy\Middleware;

use DI\Container;
use Grocy\Services\ApplicationService;
use Psr\Http\Message\ResponseFactoryInterface;

class BaseMiddleware
{
	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		$this->AppContainer = $container;
		$this->ResponseFactory = $responseFactory;
		$this->ApplicationService = ApplicationService::GetInstance();
	}

	protected Container $AppContainer;
	protected ResponseFactoryInterface $ResponseFactory;
	protected ApplicationService $ApplicationService;
}
