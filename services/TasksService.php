<?php

namespace Grocy\Services;

class TasksService extends BaseService
{
	public function GetCurrent()
	{
		$sql = 'SELECT * from tasks_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function MarkTaskAsCompleted($taskId, $doneTime)
	{
		if (!$this->TaskExists($taskId))
		{
			throw new \Exception('Task does not exist');
		}

		$taskRow = $this->Database->tasks()->where('id = :1', $taskId)->fetch();
		$taskRow->update(array(
			'done' => 1,
			'done_timestamp' => $doneTime
		));

		return true;
	}

	public function UndoTask($taskId)
	{
		if (!$this->TaskExists($taskId))
		{
			throw new \Exception('Task does not exist');
		}

		$taskRow = $this->Database->tasks()->where('id = :1', $taskId)->fetch();
		$taskRow->update(array(
			'done' => 0,
			'done_timestamp' => null
		));

		return true;
	}

	private function TaskExists($taskId)
	{
		$taskRow = $this->Database->tasks()->where('id = :1', $taskId)->fetch();
		return $taskRow !== null;
	}
}
