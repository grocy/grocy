<?php

namespace Grocy\Controllers\Api;

use Grocy\Services\ApplicationService;
use Grocy\Services\DatabaseService;
use Grocy\Services\LocalizationService;
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
			unset(
				$constants['GROCY_AUTHENTICATED'],
				$constants['GROCY_DATAPATH'],
				$constants['GROCY_IS_EMBEDDED_INSTALL'],
				$constants['GROCY_REVERSE_PROXY_AUTH_HEADER'],
				$constants['GROCY_REVERSE_PROXY_AUTH_USE_ENV'],
				$constants['GROCY_LDAP_ADDRESS'],
				$constants['GROCY_LDAP_BASE_DN'],
				$constants['GROCY_LDAP_BIND_DN'],
				$constants['GROCY_LDAP_BIND_PW'],
				$constants['GROCY_LDAP_USER_FILTER'],
				$constants['GROCY_LDAP_UID_ATTR'],
				$constants['GROCY_USER_USERNAME'],
				$constants['GROCY_USER_PICTURE_FILE_NAME'],
				$constants['GROCY_USER_ID']
			);

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
			'changed_time' => DatabaseService::GetInstance()->GetDbChangedTime()
		]);
	}

	public function GetSystemInfo(Request $request, Response $response, array $args)
	{
		return $this->ApiResponse($response, ApplicationService::GetInstance()->GetSystemInfo());
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

			return $this->ApiResponse($response, ApplicationService::GetInstance()->GetSystemTime($offset));
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

				LocalizationService::GetInstance()->CheckAndAddMissingTranslationToPot($requestBody['text']);
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
		return $this->ApiResponse($response, json_decode(LocalizationService::GetInstance()->GetPoAsJsonString()), true);
	}
}
