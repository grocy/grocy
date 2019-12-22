<?php

namespace Grocy\Controllers;

class SystemApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	public function GetDbChangedTime(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
        $response = $this->ApiResponse(array(
            'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
        ));
        return $response;
	}

	public function LogMissingLocalization(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if (GROCY_MODE === 'dev')
		{
			try
			{
				$requestBody = $request->getParsedBody();

				$this->getLocalizationService()->CheckAndAddMissingTranslationToPot($requestBody['text']);
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
		return $this->ApiResponse($this->getApplicationService()->GetSystemInfo());
	}
}
