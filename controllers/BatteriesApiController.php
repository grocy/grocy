<?php

namespace Grocy\Controllers;

use \Grocy\Services\BatteriesService;

class BatteriesApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->BatteriesService = new BatteriesService();
	}

	protected $BatteriesService;

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

			$chargeCycleId = $this->BatteriesService->TrackChargeCycle($args['batteryId'], $trackedTime);
			return $this->ApiResponse($this->Database->battery_charge_cycles($chargeCycleId));
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
			return $this->ApiResponse($this->BatteriesService->GetBatteryDetails($args['batteryId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->BatteriesService->GetCurrent());
	}

	public function UndoChargeCycle(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->ApiResponse($this->BatteriesService->UndoChargeCycle($args['chargeCycleId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
