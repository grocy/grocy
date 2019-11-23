<?php

namespace Grocy\Controllers;

#use \Grocy\Services\DatabaseService;
#use \Grocy\Services\ApplicationService;

class SystemApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		#$this->ApplicationService = ApplicationService::getInstance();
	}

	#protected $DatabaseService;
	#protected $ApplicationService;

	public function GetDbChangedTime(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		#$fp = fopen('/config/data/sql.log', 'a');
		#fwrite($fp, "---- getting db changed time ----\n");
		#$time_start = microtime(true);
        $response = $this->ApiResponse(array(
            'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
        ));
		#fwrite($fp, "----Total execution time in seconds: " . round((microtime(true) - $time_start),4) . "\n");
		#fwrite($fp, "---- time obtained ----\n");
		#fclose($fp);
        return $response;
		#return $this->ApiResponse(array(
		#	'changed_time' => $this->getDatabaseService()->GetDbChangedTime()
		#));
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
