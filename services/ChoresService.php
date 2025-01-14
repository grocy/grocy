<?php

namespace Grocy\Services;

class ChoresService extends BaseService
{
	const CHORE_ASSIGNMENT_TYPE_IN_ALPHABETICAL_ORDER = 'in-alphabetical-order';
	const CHORE_ASSIGNMENT_TYPE_NO_ASSIGNMENT = 'no-assignment';
	const CHORE_ASSIGNMENT_TYPE_RANDOM = 'random';
	const CHORE_ASSIGNMENT_TYPE_WHO_LEAST_DID_FIRST = 'who-least-did-first';
	const CHORE_PERIOD_TYPE_HOURLY = 'hourly';
	const CHORE_PERIOD_TYPE_DAILY = 'daily';
	const CHORE_PERIOD_TYPE_MANUALLY = 'manually';
	const CHORE_PERIOD_TYPE_MONTHLY = 'monthly';
	const CHORE_PERIOD_TYPE_WEEKLY = 'weekly';
	const CHORE_PERIOD_TYPE_YEARLY = 'yearly';
	const CHORE_PERIOD_TYPE_ADAPTIVE = 'adaptive';

	public function CalculateNextExecutionAssignment($choreId)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$chore = $this->getDatabase()->chores($choreId);

		if (!empty($chore->rescheduled_next_execution_assigned_to_user_id))
		{
			$nextExecutionUserId = $chore->rescheduled_next_execution_assigned_to_user_id;
		}
		else
		{
			$choreLastTrackedTime = $this->getDatabase()->chores_log()->where('chore_id = :1 AND undone = 0', $choreId)->max('tracked_time');
			$lastChoreLogRow = $this->getDatabase()->chores_log()->where('chore_id = :1 AND tracked_time = :2 AND undone = 0', $choreId, $choreLastTrackedTime)->orderBy('row_created_timestamp', 'DESC')->fetch();
			$lastDoneByUserId = $lastChoreLogRow->done_by_user_id;

			$users = $this->getUsersService()->GetUsersAsDto();
			$assignedUsers = [];
			foreach ($users as $user)
			{
				if (!empty($chore->assignment_config) && in_array($user->id, explode(',', $chore->assignment_config)))
				{
					$assignedUsers[] = $user;
				}
			}

			$nextExecutionUserId = null;
			if ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_RANDOM)
			{
				// Random assignment and only 1 user in the group? Well, ok - will be hard to guess the next one...
				if (count($assignedUsers) == 1)
				{
					$nextExecutionUserId = array_shift($assignedUsers)->id;
				}
				else
				{
					$nextExecutionUserId = $assignedUsers[array_rand($assignedUsers)]->id;
				}
			}
			elseif ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_IN_ALPHABETICAL_ORDER)
			{
				usort($assignedUsers, function ($a, $b)
				{
					return strcmp($a->display_name, $b->display_name);
				});

				$nextRoundMatches = false;
				foreach ($assignedUsers as $user)
				{
					if ($nextRoundMatches)
					{
						$nextExecutionUserId = $user->id;
						break;
					}

					if ($user->id == $lastDoneByUserId)
					{
						$nextRoundMatches = true;
					}
				}

				// If nothing has matched, probably it was the last user in the sorted list -> the first one is the next one
				if ($nextExecutionUserId == null)
				{
					$nextExecutionUserId = array_shift($assignedUsers)->id;
				}
			}
			elseif ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_WHO_LEAST_DID_FIRST)
			{
				$row = $this->getDatabase()->chores_execution_users_statistics()->where('chore_id = :1', $choreId)->orderBy('execution_count')->limit(1)->fetch();
				if ($row != null)
				{
					$nextExecutionUserId = $row->user_id;
				}
			}
		}

		$chore->update([
			'next_execution_assigned_to_user_id' => $nextExecutionUserId
		]);
	}

	public function GetChoreDetails(int $choreId)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$users = $this->getUsersService()->GetUsersAsDto();

		$chore = $this->getDatabase()->chores($choreId);
		$choreTrackedCount = $this->getDatabase()->chores_log()->where('chore_id = :1 AND undone = 0 AND skipped = 0', $choreId)->count();
		$choreLastTrackedTime = $this->getDatabase()->chores_log()->where('chore_id = :1 AND undone = 0 AND skipped = 0', $choreId)->max('tracked_time');
		$nextExecutionTime = $this->getDatabase()->chores_current()->where('chore_id', $choreId)->min('next_estimated_execution_time');
		$averageExecutionFrequency = $this->getDatabase()->chores_execution_average_frequency()->where('chore_id', $choreId)->min('average_frequency_hours');

		$lastChoreLogRow = $this->getDatabase()->chores_log()->where('chore_id = :1 AND tracked_time = :2 AND undone = 0', $choreId, $choreLastTrackedTime)->fetch();
		$lastDoneByUser = null;
		if ($lastChoreLogRow !== null && !empty($lastChoreLogRow))
		{
			$lastDoneByUser = FindObjectInArrayByPropertyValue($users, 'id', $lastChoreLogRow->done_by_user_id);
		}

		$nextExecutionAssignedUser = null;
		if (!empty($chore->next_execution_assigned_to_user_id))
		{
			$nextExecutionAssignedUser = FindObjectInArrayByPropertyValue($users, 'id', $chore->next_execution_assigned_to_user_id);
		}

		return [
			'chore' => $chore,
			'last_tracked' => $choreLastTrackedTime,
			'tracked_count' => $choreTrackedCount,
			'last_done_by' => $lastDoneByUser,
			'next_estimated_execution_time' => $nextExecutionTime,
			'next_execution_assigned_user' => $nextExecutionAssignedUser,
			'average_execution_frequency_hours' => $averageExecutionFrequency
		];
	}

	public function GetCurrent()
	{
		$users = $this->getUsersService()->GetUsersAsDto();

		$chores = $this->getDatabase()->chores_current();
		foreach ($chores as $chore)
		{
			if (!empty($chore->next_execution_assigned_to_user_id))
			{
				$chore->next_execution_assigned_user = FindObjectInArrayByPropertyValue($users, 'id', $chore->next_execution_assigned_to_user_id);
			}
			else
			{
				$chore->next_execution_assigned_user = null;
			}
		}

		return $chores;
	}

	public function TrackChore(int $choreId, string $trackedTime, $doneBy = GROCY_USER_ID, $skipped = false)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$userRow = $this->getDatabase()->users()->where('id = :1', $doneBy)->fetch();
		if ($userRow === null)
		{
			throw new \Exception('User does not exist');
		}

		$chore = $this->getDatabase()->chores($choreId);
		if ($chore->track_date_only == 1)
		{
			$trackedTime = substr($trackedTime, 0, 10) . ' 00:00:00';
		}

		if ($skipped)
		{
			if ($chore->period_type == self::CHORE_PERIOD_TYPE_MANUALLY)
			{
				throw new \Exception('Chores without a schedule can\'t be skipped');
			}
		}

		$scheduledExecutionTime = $this->getDatabase()->chores_current()->where('chore_id = :1', $chore->id)->fetch()->next_estimated_execution_time;
		$logRow = $this->getDatabase()->chores_log()->createRow([
			'chore_id' => $choreId,
			'tracked_time' => $trackedTime,
			'done_by_user_id' => $doneBy,
			'skipped' => BoolToInt($skipped),
			'scheduled_execution_time' => $scheduledExecutionTime
		]);
		$logRow->save();
		$lastInsertId = $this->getDatabase()->lastInsertId();

		if ($chore->consume_product_on_execution == 1 && !empty($chore->product_id))
		{
			$transactionId = uniqid();
			$this->getStockService()->ConsumeProduct($chore->product_id, $chore->product_amount, false, StockService::TRANSACTION_TYPE_CONSUME, 'default', null, null, $transactionId, true);
		}

		if (!empty($chore->rescheduled_date))
		{
			$chore->update([
				'rescheduled_date' => null
			]);
		}

		if (!empty($chore->rescheduled_next_execution_assigned_to_user_id))
		{
			$chore->update([
				'rescheduled_next_execution_assigned_to_user_id' => null
			]);
		}

		$this->CalculateNextExecutionAssignment($choreId);

		return $lastInsertId;
	}

	public function UndoChoreExecution($executionId)
	{
		$logRow = $this->getDatabase()->chores_log()->where('id = :1 AND undone = 0', $executionId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Execution does not exist or was already undone');
		}

		// Update log entry
		$logRow->update([
			'undone' => 1,
			'undone_timestamp' => date('Y-m-d H:i:s')
		]);

		$this->CalculateNextExecutionAssignment($logRow->chore_id);
	}

	public function MergeChores(int $choreIdToKeep, int $choreIdToRemove)
	{
		if (!$this->ChoreExists($choreIdToKeep))
		{
			throw new \Exception('$choreIdToKeep does not exist or is inactive');
		}

		if (!$this->ChoreExists($choreIdToRemove))
		{
			throw new \Exception('$choreIdToRemove does not exist or is inactive');
		}

		if ($choreIdToKeep == $choreIdToRemove)
		{
			throw new \Exception('$choreIdToKeep cannot equal $choreIdToRemove');
		}

		$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();
		try
		{
			$choreToKeep = $this->getDatabase()->chores($choreIdToKeep);
			$choreToRemove = $this->getDatabase()->chores($choreIdToRemove);

			$this->getDatabaseService()->ExecuteDbStatement('UPDATE chores_log SET chore_id = ' . $choreIdToKeep . ' WHERE chore_id = ' . $choreIdToRemove);
			$this->getDatabaseService()->ExecuteDbStatement('DELETE FROM chores WHERE id = ' . $choreIdToRemove);
		}
		catch (\Exception $ex)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
			throw $ex;
		}
		$this->getDatabaseService()->GetDbConnectionRaw()->commit();
	}

	private function ChoreExists($choreId)
	{
		$choreRow = $this->getDatabase()->chores()->where('id = :1', $choreId)->fetch();
		return $choreRow !== null;
	}
}
