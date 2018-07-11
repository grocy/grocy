<?php

namespace Grocy\Controllers;

use \Grocy\Services\HabitsService;

class HabitsApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->HabitsService = new HabitsService();
	}

	protected $HabitsService;

	public function TrackHabitExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']) && IsIsoDateTime($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		try
		{
			$this->HabitsService->TrackHabit($args['habitId'], $trackedTime);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function HabitDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->HabitsService->GetHabitDetails($args['habitId']));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
