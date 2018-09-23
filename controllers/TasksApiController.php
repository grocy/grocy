<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;

class TasksApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->TasksService = new TasksService();
	}

	protected $TasksService;

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->TasksService->GetCurrent());
	}

	public function MarkTaskAsCompleted(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$doneTime = date('Y-m-d H:i:s');
		if (isset($request->getQueryParams()['done_time']) && !empty($request->getQueryParams()['done_time']) && IsIsoDateTime($request->getQueryParams()['done_time']))
		{
			$doneTime = $request->getQueryParams()['done_time'];
		}

		try
		{
			$this->TasksService->MarkTaskAsCompleted($args['taskId'], $doneTime);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
