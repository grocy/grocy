<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;
use \Grocy\Services\ApplicationService;

class SystemApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->DatabaseService = new DatabaseService();
		$this->ApplicationService = new ApplicationService();
	}

	protected $DatabaseService;
	protected $ApplicationService;

	public function GetDbChangedTime(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse(array(
			'changed_time' => $this->DatabaseService->GetDbChangedTime()
		));
	}

	public function LogMissingLocalization(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

	public function GetSystemInfo(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->ApplicationService->GetSystemInfo());
	}
}
