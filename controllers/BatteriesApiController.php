<?php

namespace Grocy\Controllers;

use \Grocy\Services\BatteriesService;

class BatteriesApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $BatteriesService = null;

    protected function getBatteriesService()
    {
        if($this->BatteriesService == null)
        {
            $this->BatteriesService = BatteriesService::getInstance();
        }
        return $this->BatteriesService;
    }

	public function TrackChargeCycle(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$trackedTime = date('Y-m-d H:i:s');
			if (array_key_exists('tracked_time', $requestBody) && IsIsoDateTime($requestBody['tracked_time']))
			{
				$trackedTime = $requestBody['tracked_time'];
			}

			$chargeCycleId = $this->getBatteriesService()->TrackChargeCycle($args['batteryId'], $trackedTime);
			return $this->ApiResponse($this->getDatabase()->battery_charge_cycles($chargeCycleId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function BatteryDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->getBatteriesService()->GetBatteryDetails($args['batteryId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->getBatteriesService()->GetCurrent());
	}

	public function UndoChargeCycle(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->ApiResponse($this->getBatteriesService()->UndoChargeCycle($args['chargeCycleId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
