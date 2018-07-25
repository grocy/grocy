<?php

namespace Grocy\Services;

class HabitsService extends BaseService
{
	const HABIT_TYPE_MANUALLY = 'manually';
	const HABIT_TYPE_DYNAMIC_REGULAR = 'dynamic-regular';

	public function GetCurrentHabits()
	{
		$sql = 'SELECT * from habits_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetNextHabitTime(int $habitId)
	{
		if (!$this->HabitExists($habitId))
		{
			throw new \Exception('Habit does not exist');
		}

		$habit = $this->Database->habits($habitId);
		$habitLastLogRow = $this->DatabaseService->ExecuteDbQuery("SELECT * from habits_current WHERE habit_id = $habitId LIMIT 1")->fetch(\PDO::FETCH_OBJ);

		switch($habit->period_type)
		{
			case self::HABIT_TYPE_MANUALLY:
				return date('2999-12-31 23:59:59');
			case self::HABIT_TYPE_DYNAMIC_REGULAR:
				return date('Y-m-d H:i:s', strtotime('+' . $habit->period_days . ' day', strtotime($habitLastLogRow->last_tracked_time)));
		}

		return null;
	}

	public function GetHabitDetails(int $habitId)
	{
		if (!$this->HabitExists($habitId))
		{
			throw new \Exception('Habit does not exist');
		}

		$habit = $this->Database->habits($habitId);
		$habitTrackedCount = $this->Database->habits_log()->where('habit_id', $habitId)->count();
		$habitLastTrackedTime = $this->Database->habits_log()->where('habit_id', $habitId)->max('tracked_time');
		
		$doneByUserId =  $this->Database->habits_log()->where('habit_id = :1 AND tracked_time = :2', $habitId, $habitLastTrackedTime)->fetch()->done_by_user_id;
		if ($doneByUserId !== null && !empty($doneByUserId))
		{
			$usersService = new UsersService();
			$users = $usersService->GetUsersAsDto();
			$lastDoneByUser = FindObjectInArrayByPropertyValue($users, 'id', $doneByUserId);
		}

		return array(
			'habit' => $habit,
			'last_tracked' => $habitLastTrackedTime,
			'tracked_count' => $habitTrackedCount,
			'last_done_by' => $lastDoneByUser
		);
	}

	public function TrackHabit(int $habitId, string $trackedTime, $doneBy = GROCY_USER_ID)
	{
		if (!$this->HabitExists($habitId))
		{
			throw new \Exception('Habit does not exist');
		}

		$userRow = $this->Database->users()->where('id = :1', $doneBy)->fetch();
		if ($userRow === null)
		{
			throw new \Exception('User does not exist');
		}
		
		$logRow = $this->Database->habits_log()->createRow(array(
			'habit_id' => $habitId,
			'tracked_time' => $trackedTime,
			'done_by_user_id' => $doneBy
		));
		$logRow->save();

		return true;
	}

	private function HabitExists($habitId)
	{
		$habitRow = $this->Database->habits()->where('id = :1', $habitId)->fetch();
		return $habitRow !== null;
	}
}
