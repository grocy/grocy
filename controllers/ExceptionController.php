<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ExceptionController extends BaseApiController
{
	/**
	 * @var \Slim\App
	 */
	private $app;

	public function __construct(\Slim\App $app, \DI\Container $container)
	{
		parent::__construct($container);
		$this->app = $app;
	}

	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails, ?LoggerInterface $logger = null)
	{
		$response = $this->app->getResponseFactory()->createResponse();
		$isApiRoute = string_starts_with($request->getUri()->getPath(), '/api/');

		if (!defined('GROCY_AUTHENTICATED'))
		{
			define('GROCY_AUTHENTICATED', false);
		}

		if ($isApiRoute)
		{
			$status = 500;

			if ($exception instanceof HttpException)
			{
				$status = $exception->getCode();
			}

			$data = [
				'error_message' => $exception->getMessage()
			];

			if ($displayErrorDetails)
			{
				$data['error_details'] = [
					'stack_trace' => $exception->getTraceAsString(),
					'file' => $exception->getFile(),
					'line' => $exception->getLine()
				];
			}

			return $this->ApiResponse($response->withStatus($status)->withHeader('Content-Type', 'application/json'), $data);
		}

		if ($exception instanceof HttpNotFoundException)
		{
			define('GROCY_AUTHENTICATED', false);

			return $this->renderPage($request, $response->withStatus(404), 'errors/404', [
				'exception' => $exception
			]);
		}

		if ($exception instanceof HttpForbiddenException)
		{
			return $this->renderPage($request, $response->withStatus(403), 'errors/403', [
				'exception' => $exception
			]);
		}

		return $this->renderPage($request, $response->withStatus(500), 'errors/500', [
			'exception' => $exception
		]);
	}
}
