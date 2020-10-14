<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class BatteriesApiController extends BaseApiController
{
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
		return $this->FilteredApiResponse($response, $this->getBatteriesService()->GetCurrent(), $request->getQueryParams());
	}

	public function TrackChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_BATTERIES_TRACK_CHARGE_CYCLE);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

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

	public function UndoChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_BATTERIES_UNDO_CHARGE_CYCLE);

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

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
