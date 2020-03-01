<?php

namespace Grocy\Controllers;

class BatteriesApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function TrackChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
			return $this->ApiResponse($response, $this->getDatabase()->battery_charge_cycles($chargeCycleId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function BatteryDetails(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getBatteriesService()->GetBatteryDetails($args['batteryId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getBatteriesService()->GetCurrent());
	}

	public function UndoChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$this->ApiResponse($response, $this->getBatteriesService()->UndoChargeCycle($args['chargeCycleId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
