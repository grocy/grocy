<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TasksApiController extends BaseApiController
{
	public function Current(Request $request, Response $response, array $args)
	{
		return $this->FilteredApiResponse($response, $this->getTasksService()->GetCurrent(), $request->getQueryParams());
	}

	public function MarkTaskAsCompleted(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_TASKS_MARK_COMPLETED);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

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

	public function UndoTask(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_TASKS_UNDO_EXECUTION);

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
