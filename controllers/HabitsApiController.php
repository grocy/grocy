<?php

namespace Grocy\Controllers;

use Grocy\Services\HabitsService;

class HabitsApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->HabitsService = new HabitsService();
	}

	protected $HabitsService;

	public function TrackHabitExecution($request, $response, $args)
	{
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		return $this->ApiEncode(array('success' => $this->HabitsService->TrackHabit($args['habitId'], $trackedTime)));
	}

	public function HabitDetails($request, $response, $args)
	{
		return $this->ApiEncode($this->HabitsService->GetHabitDetails($args['habitId']));
	}
}
