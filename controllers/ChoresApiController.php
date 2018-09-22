<?php

namespace Grocy\Controllers;

use \Grocy\Services\ChoresService;

class ChoresApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ChoresService = new ChoresService();
	}

	protected $ChoresService;

	public function TrackChoreExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$trackedTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['tracked_time']) && !empty($request->getQueryParams()['tracked_time']) && IsIsoDateTime($request->getQueryParams()['tracked_time']))
		{
			$trackedTime = $request->getQueryParams()['tracked_time'];
		}

		$doneBy = GROCY_USER_ID;
		if (isset($request->getQueryParams()['done_by']) && !empty($request->getQueryParams()['done_by']))
		{
			$doneBy = $request->getQueryParams()['done_by'];
		}

		try
		{
			$this->ChoresService->TrackChore($args['choreId'], $trackedTime, $doneBy);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function ChoreDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->ChoresService->GetChoreDetails($args['choreId']));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->ChoresService->GetCurrent());
	}
}
