<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Helpers\WebhookRunner;
use Grocy\Helpers\Grocycode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ChoresApiController extends BaseApiController
{
	public function CalculateNextExecutionAssignments(Request $request, Response $response, array $args)
	{
		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

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

	public function ChoreDetails(Request $request, Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getChoresService()->GetChoreDetails($args['choreId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(Request $request, Response $response, array $args)
	{
		return $this->FilteredApiResponse($response, $this->getChoresService()->GetCurrent(), $request->getQueryParams());
	}

	public function TrackChoreExecution(Request $request, Response $response, array $args)
	{
		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			User::checkPermission($request, User::PERMISSION_CHORE_TRACK_EXECUTION);

			$trackedTime = date('Y-m-d H:i:s');
			if (array_key_exists('tracked_time', $requestBody) && (IsIsoDateTime($requestBody['tracked_time']) || IsIsoDate($requestBody['tracked_time'])))
			{
				$trackedTime = $requestBody['tracked_time'];
			}

			$skipped = false;
			if (array_key_exists('skipped', $requestBody) && filter_var($requestBody['skipped'], FILTER_VALIDATE_BOOLEAN) !== false)
			{
				$skipped = $requestBody['skipped'];
			}

			$doneBy = GROCY_USER_ID;
			if (array_key_exists('done_by', $requestBody) && !empty($requestBody['done_by']))
			{
				$doneBy = $requestBody['done_by'];
			}

			if ($doneBy != GROCY_USER_ID)
			{
				User::checkPermission($request, User::PERMISSION_CHORE_TRACK_EXECUTION);
			}

			$choreExecutionId = $this->getChoresService()->TrackChore($args['choreId'], $trackedTime, $doneBy, $skipped);
			return $this->ApiResponse($response, $this->getDatabase()->chores_log($choreExecutionId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoChoreExecution(Request $request, Response $response, array $args)
	{
		try
		{
			User::checkPermission($request, User::PERMISSION_CHORE_UNDO_EXECUTION);

			$this->ApiResponse($response, $this->getChoresService()->UndoChoreExecution($args['executionId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ChorePrintLabel(Request $request, Response $response, array $args)
	{
		try
		{
			$chore = $this->getDatabase()->chores()->where('id', $args['choreId'])->fetch();

			$webhookData = array_merge([
				'chore' => $chore->name,
				'grocycode' => (string)(new Grocycode(Grocycode::CHORE, $args['choreId'])),
			], GROCY_LABEL_PRINTER_PARAMS);

			if (GROCY_LABEL_PRINTER_RUN_SERVER)
			{
				(new WebhookRunner())->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
			}

			return $this->ApiResponse($response, $webhookData);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function MergeChores(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		try
		{
			if (filter_var($args['choreIdToKeep'], FILTER_VALIDATE_INT) === false || filter_var($args['choreIdToRemove'], FILTER_VALIDATE_INT) === false)
			{
				throw new \Exception('Provided {choreIdToKeep} or {choreIdToRemove} is not a valid integer');
			}

			$this->ApiResponse($response, $this->getChoresService()->MergeChores($args['choreIdToKeep'], $args['choreIdToRemove']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
