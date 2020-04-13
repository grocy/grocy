<?php

namespace Grocy\Controllers;

class SystemApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function GetDbChangedTime(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
        return $this->ApiResponse($response, array(
            'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
        ));
	}

	public function GetConfig(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$constants = get_defined_constants();

			// Some GROCY_* constants are not really config settings and therefore should not be exposed
			unset($constants['GROCY_AUTHENTICATED']);
			unset($constants['GROCY_DATAPATH']);
			unset($constants['GROCY_IS_EMBEDDED_INSTALL']);
			unset($constants['GROCY_USER_ID']);

			$returnArray = array();
			foreach ($constants as $constant => $value)
			{
				if (substr($constant, 0, 6) === 'GROCY_')
				{
					$returnArray[substr($constant, 6)] = $value;
				}
			}

			return $this->ApiResponse($response, $returnArray);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function LogMissingLocalization(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

	public function GetSystemInfo(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getApplicationService()->GetSystemInfo());
	}
}
