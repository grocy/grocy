<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;

class TasksApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $TasksService = null;

    protected function getTasksService()
	{
		if($this->TasksService == null)
		{
			$this->TasksService = TasksService::getInstance();
		}
		return $this->TasksService;
	}

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->getTasksService()->GetCurrent());
	}

	public function MarkTaskAsCompleted(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$doneTime = date('Y-m-d H:i:s');
			if (array_key_exists('done_time', $requestBody) && IsIsoDateTime($requestBody['done_time']))
			{
				$doneTime = $requestBody['done_time'];
			}

			$this->getTasksService()->MarkTaskAsCompleted($args['taskId'], $doneTime);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoTask(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->getTasksService()->UndoTask($args['taskId']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
