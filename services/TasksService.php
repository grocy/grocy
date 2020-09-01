<?php

namespace Grocy\Services;

class TasksService extends BaseService
{
	public function GetCurrent(): \LessQL\Result
	{
		return $this->getDatabase()->tasks_current();
	}

	public function MarkTaskAsCompleted($taskId, $doneTime)
	{
		if (!$this->TaskExists($taskId))
		{
			throw new \Exception('Task does not exist');
		}

		$taskRow = $this->getDatabase()->tasks()->where('id = :1', $taskId)->fetch();
		$taskRow->update([
			'done' => 1,
			'done_timestamp' => $doneTime
		]);

		return true;
	}

	public function UndoTask($taskId)
	{
		if (!$this->TaskExists($taskId))
		{
			throw new \Exception('Task does not exist');
		}

		$taskRow = $this->getDatabase()->tasks()->where('id = :1', $taskId)->fetch();
		$taskRow->update([
			'done' => 0,
			'done_timestamp' => null
		]);

		return true;
	}

	private function TaskExists($taskId)
	{
		$taskRow = $this->getDatabase()->tasks()->where('id = :1', $taskId)->fetch();
		return $taskRow !== null;
	}
}
