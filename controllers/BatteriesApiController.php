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
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		return $this->ApiResponse(array('success' => $this->BatteriesService->TrackChargeCycle($args['batteryId'], $trackedTime)));
	}

	public function BatteryDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->BatteriesService->GetBatteryDetails($args['batteryId']));
	}
}
