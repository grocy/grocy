<?php

namespace Grocy\Controllers;

class SystemApiController extends BaseApiController
{
	public function GetConfig(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$constants = get_defined_constants();

			// Some GROCY_* constants are not really config settings and therefore should not be exposed
			unset($constants['GROCY_AUTHENTICATED'], $constants['GROCY_DATAPATH'], $constants['GROCY_IS_EMBEDDED_INSTALL'], $constants['GROCY_USER_ID']);

			$returnArray = [];

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

	public function GetDbChangedTime(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, [
			'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
		]);
	}

	public function GetSystemInfo(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getApplicationService()->GetSystemInfo());
	}

	public function GetSystemTime(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$offset = 0;
		if (isset($args['offset']))
			$offset = $args['offset'];
		return $this->ApiResponse($response, $this->getApplicationService()->GetSystemTime($offset));
	}

	public function LogMissingLocalization(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (GROCY_MODE === 'dev')
		{
			try
			{
				$requestBody = $this->GetParsedAndFilteredRequestBody($request);

				$this->getLocalizationService()->CheckAndAddMissingTranslationToPot($requestBody['text']);
				return $this->EmptyApiResponse($response);
			}
			catch (\Exception $ex)
			{
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		}
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
