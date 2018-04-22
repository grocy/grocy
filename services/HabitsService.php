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
				return date('Y-m-d H:i:s');
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

		return array(
			'habit' => $habit,
			'last_tracked' => $habitLastTrackedTime,
			'tracked_count' => $habitTrackedCount
		);
	}

	public function TrackHabit(int $habitId, string $trackedTime)
	{
		if (!$this->HabitExists($habitId))
		{
			throw new \Exception('Habit does not exist');
		}
		
		$logRow = $this->Database->habits_log()->createRow(array(
			'habit_id' => $habitId,
			'tracked_time' => $trackedTime
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
