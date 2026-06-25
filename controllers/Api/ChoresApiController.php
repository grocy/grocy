<?php

namespace Grocy\Controllers\Api;

use Grocy\Controllers\Users\User;
use Grocy\Helpers\Grocycode;
use Grocy\Helpers\WebhookRunner;
use Grocy\Services\ChoresService;
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
				$choreId = $requestBody['chore_id'];
			}

			if ($choreId === null)
			{
				$chores = $this->DB->chores();
				foreach ($chores as $chore)
				{
					ChoresService::GetInstance()->CalculateNextExecutionAssignment($chore->id);
				}
			}
			else
			{
				ChoresService::GetInstance()->CalculateNextExecutionAssignment($choreId);
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
			return $this->ApiResponse($response, ChoresService::GetInstance()->GetChoreDetails($args['choreId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(Request $request, Response $response, array $args)
	{
		return $this->FilteredApiResponse($response, ChoresService::GetInstance()->GetCurrent(), $request->getQueryParams());
	}

	public function TrackChoreExecution(Request $request, Response $response, array $args)
	{
		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			User::CheckPermission($request, User::PERMISSION_CHORE_TRACK_EXECUTION);

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
				User::CheckPermission($request, User::PERMISSION_CHORE_TRACK_EXECUTION);
			}

			$choreExecutionId = ChoresService::GetInstance()->TrackChore($args['choreId'], $trackedTime, $doneBy, $skipped);
			return $this->ApiResponse($response, $this->DB->chores_log($choreExecutionId));
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
			User::CheckPermission($request, User::PERMISSION_CHORE_UNDO_EXECUTION);

			$this->ApiResponse($response, ChoresService::GetInstance()->UndoChoreExecution($args['executionId']));
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
			$choreDetails = (object)ChoresService::GetInstance()->GetChoreDetails($args['choreId']);

			$webhookData = array_merge([
				'chore' => $choreDetails->chore->name,
				'grocycode' => (string)(new Grocycode(Grocycode::CHORE, $args['choreId'])),
				'details' => $choreDetails,
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
		User::CheckPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		try
		{
			if (filter_var($args['choreIdToKeep'], FILTER_VALIDATE_INT) === false || filter_var($args['choreIdToRemove'], FILTER_VALIDATE_INT) === false)
			{
				throw new \Exception('Provided {choreIdToKeep} or {choreIdToRemove} is not a valid integer');
			}

			$this->ApiResponse($response, ChoresService::GetInstance()->MergeChores($args['choreIdToKeep'], $args['choreIdToRemove']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
