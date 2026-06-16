<?php

namespace Grocy\Controllers;

use DI\Container;
use Grocy\Controllers\Api\BaseApiController;
use Grocy\Services\ApplicationService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ExceptionController extends BaseApiController
{
	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container);
		$this->ResponseFactory = $responseFactory;
	}

	private $ResponseFactory;

	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails, ?LoggerInterface $logger = null)
	{
		if (!defined('GROCY_LOCALE'))
		{
			define('GROCY_LOCALE', GROCY_DEFAULT_LOCALE);
		}

		$response = $this->ResponseFactory->createResponse();
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
			if (!defined('GROCY_AUTHENTICATED'))
			{
				define('GROCY_AUTHENTICATED', false);
			}

			return $this->RenderPage($response->withStatus(404), 'errors/404', [
				'exception' => $exception
			]);
		}

		if ($exception instanceof HttpForbiddenException)
		{
			return $this->RenderPage($response->withStatus(403), 'errors/403', [
				'exception' => $exception
			]);
		}

		return $this->RenderPage($response->withStatus(500), 'errors/500', [
			'exception' => $exception,
			'systemInfo' => ApplicationService::GetInstance()->GetSystemInfo()
		]);
	}
}
