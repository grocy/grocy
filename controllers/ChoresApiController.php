<?php

namespace Grocy\Controllers;

use \Grocy\Services\ChoresService;

class ChoresApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $ChoresService = null;

	protected function getChoresService()
	{
		if($this->ChoresService == null)
		{
			$this->ChoresService = ChoresService::getInstance();
		}
		return $this->ChoresService;
	}

	public function TrackChoreExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$trackedTime = date('Y-m-d H:i:s');
			if (array_key_exists('tracked_time', $requestBody) && (IsIsoDateTime($requestBody['tracked_time']) || IsIsoDate($requestBody['tracked_time'])))
			{
				$trackedTime = $requestBody['tracked_time'];
			}

			$doneBy = GROCY_USER_ID;
			if (array_key_exists('done_by', $requestBody) && !empty($requestBody['done_by']))
			{
				$doneBy = $requestBody['done_by'];
			}

			$choreExecutionId = $this->getChoresService()->TrackChore($args['choreId'], $trackedTime, $doneBy);
			return $this->ApiResponse($this->getDatabase()->chores_log($choreExecutionId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ChoreDetails(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->getChoresService()->GetChoreDetails($args['choreId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->getChoresService()->GetCurrent());
	}

	public function UndoChoreExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->ApiResponse($this->getChoresService()->UndoChoreExecution($args['executionId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CalculateNextExecutionAssignments(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$requestBody = $request->getParsedBody();

			$choreId = null;
			if (array_key_exists('chore_id', $requestBody) && !empty($requestBody['chore_id']) && is_numeric($requestBody['chore_id']))
			{
				$choreId = intval($requestBody['chore_id']);
			}

			if ($choreId === null)
			{
				$chores = $this->getDatabase()->chores();
				foreach ($chores as $chore)
				{
					$this->getChoresService()->CalculateNextExecutionAssignment($chore->id);
				}
			}
			else
			{
				$this->getChoresService()->CalculateNextExecutionAssignment($choreId);
			}

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
