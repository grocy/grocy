<?php

namespace Grocy\Controllers;

use \Grocy\Services\DatabaseService;

class SystemApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->DatabaseService = new DatabaseService();
	}

	protected $DatabaseService;

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

				$this->LocalizationService->LogMissingLocalization(GROCY_CULTURE, $requestBody['text']);
				return $this->ApiResponse(array('success' => true));
			}
			catch (\Exception $ex)
			{
				return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
			}
		}	
	}
}
