<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SystemApiController extends BaseApiController
{
	public function GetConfig(Request $request, Response $response, array $args)
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

	public function GetDbChangedTime(Request $request, Response $response, array $args)
	{
		return $this->ApiResponse($response, [
			'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
		]);
	}

	public function GetSystemInfo(Request $request, Response $response, array $args)
	{
		return $this->ApiResponse($response, $this->getApplicationService()->GetSystemInfo());
	}

	public function GetSystemTime(Request $request, Response $response, array $args)
	{
		try
		{
			$offset = 0;
			$params = $request->getQueryParams();
			if (isset($params['offset']))
			{
				if (filter_var($params['offset'], FILTER_VALIDATE_INT) === false)
				{
					throw new \Exception('Query parameter "offset" is not a valid integer');
				}

				$offset = $params['offset'];
			}

			return $this->ApiResponse($response, $this->getApplicationService()->GetSystemTime($offset));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function LogMissingLocalization(Request $request, Response $response, array $args)
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

	public function GetLocalizationStrings(Request $request, Response $response, array $args)
	{
		return $this->ApiResponse($response, json_decode($this->getLocalizationService()->GetPoAsJsonString()), true);
	}
}
