<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;

class SystemApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->DatabaseService = new DatabaseService();
		$this->ApplicationService = new ApplicationService();
	}

	protected $DatabaseService;
	protected $ApplicationService;

	public function GetDbChangedTime(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, array(
			'changed_time' => $this->DatabaseService->GetDbChangedTime()
		));
	}

	public function LogMissingLocalization(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (GROCY_MODE === 'dev')
		{
			try
			{
				$requestBody = $request->getParsedBody();

				$this->LocalizationService->CheckAndAddMissingTranslationToPot($requestBody['text']);
				return $this->EmptyApiResponse($response);
			}
			catch (\Exception $ex)
			{
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		}	
	}

	public function GetSystemInfo(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->ApplicationService->GetSystemInfo());
	}
}
