<?php

class HabitsService
{
	const HABIT_TYPE_MANUALLY = 'manually';
	const HABIT_TYPE_DYNAMIC_REGULAR = 'dynamic-regular';

	public static function GetCurrentHabits()
	{
		$sql = 'SELECT * from habits_current';
		return DatabaseService::ExecuteDbQuery(DatabaseService::GetDbConnectionRaw(), $sql)->fetchAll(PDO::FETCH_OBJ);
	}

	public static function GetNextHabitTime(int $habitId)
	{
		$db = DatabaseService::GetDbConnection();

		$habit = $db->habits($habitId);
		$habitLastLogRow = DatabaseService::ExecuteDbQuery(DatabaseService::GetDbConnectionRaw(), "SELECT * from habits_current WHERE habit_id = $habitId LIMIT 1")->fetch(PDO::FETCH_OBJ);

		switch ($habit->period_type)
		{
			case self::HABIT_TYPE_MANUALLY:
				return date('Y-m-d H:i:s');
			case self::HABIT_TYPE_DYNAMIC_REGULAR:
				return date('Y-m-d H:i:s', strtotime('+' . $habit->period_days . ' day', strtotime($habitLastLogRow->last_tracked_time)));
		}

		return null;
	}

	public static function GetHabitDetails(int $habitId)
	{
		$db = DatabaseService::GetDbConnection();

		$habit = $db->habits($habitId);
		$habitTrackedCount = $db->habits_log()->where('habit_id', $habitId)->count();
		$habitLastTrackedTime = $db->habits_log()->where('habit_id', $habitId)->max('tracked_time');

		return array(
			'habit' => $habit,
			'last_tracked' => $habitLastTrackedTime,
			'tracked_count' => $habitTrackedCount
		);
	}

	public static function TrackHabit(int $habitId, string $trackedTime)
	{
		$db = DatabaseService::GetDbConnection();

		$logRow = $db->habits_log()->createRow(array(
			'habit_id' => $habitId,
			'tracked_time' => $trackedTime
		));
		$logRow->save();

		return true;
	}
}
