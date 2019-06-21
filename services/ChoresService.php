<?php

namespace Grocy\Services;

class ChoresService extends BaseService
{
	const CHORE_TYPE_MANUALLY = 'manually';
	const CHORE_TYPE_DYNAMIC_REGULAR = 'dynamic-regular';
	const CHORE_TYPE_DAILY = 'daily';
	const CHORE_TYPE_weekly = 'weekly';
	const CHORE_TYPE_monthly = 'monthly';

	public function GetCurrent()
	{
		$sql = 'SELECT * from chores_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetChoreDetails(int $choreId)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$chore = $this->Database->chores($choreId);
		$choreTrackedCount = $this->Database->chores_log()->where('chore_id = :1 AND undone = 0', $choreId)->count();
		$choreLastTrackedTime = $this->Database->chores_log()->where('chore_id = :1 AND undone = 0', $choreId)->max('tracked_time');
		$nextExeuctionTime = $this->Database->chores_current()->where('chore_id', $choreId)->min('next_estimated_execution_time');
		
		$lastChoreLogRow =  $this->Database->chores_log()->where('chore_id = :1 AND tracked_time = :2 AND undone = 0', $choreId, $choreLastTrackedTime)->fetch();
		$lastDoneByUser = null;
		if ($lastChoreLogRow !== null && !empty($lastChoreLogRow))
		{
			$usersService = new UsersService();
			$users = $usersService->GetUsersAsDto();
			$lastDoneByUser = FindObjectInArrayByPropertyValue($users, 'id', $lastChoreLogRow->done_by_user_id);
		}

		return array(
			'chore' => $chore,
			'last_tracked' => $choreLastTrackedTime,
			'tracked_count' => $choreTrackedCount,
			'last_done_by' => $lastDoneByUser,
			'next_estimated_execution_time' => $nextExeuctionTime
		);
	}

	public function TrackChore(int $choreId, string $trackedTime, $doneBy = GROCY_USER_ID)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$userRow = $this->Database->users()->where('id = :1', $doneBy)->fetch();
		if ($userRow === null)
		{
			throw new \Exception('User does not exist');
		}

		$chore = $this->Database->chores($choreId);
		if ($chore->track_date_only == 1)
		{
			$trackedTime = substr($trackedTime, 0, 10) . ' 00:00:00';
		}
		
		$logRow = $this->Database->chores_log()->createRow(array(
			'chore_id' => $choreId,
			'tracked_time' => $trackedTime,
			'done_by_user_id' => $doneBy
		));
		$logRow->save();

		return $this->Database->lastInsertId();
	}

	private function ChoreExists($choreId)
	{
		$choreRow = $this->Database->chores()->where('id = :1', $choreId)->fetch();
		return $choreRow !== null;
	}

	public function UndoChoreExecution($executionId)
	{
		$logRow = $this->Database->chores_log()->where('id = :1 AND undone = 0', $executionId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Execution does not exist or was already undone');
		}

		// Update log entry
		$logRow->update(array(
			'undone' => 1,
			'undone_timestamp' => date('Y-m-d H:i:s')
		));
	}
}
