<?php

namespace Grocy\Services;

class TasksService extends BaseService
{
	public function GetCurrent()
	{
		$sql = 'SELECT * from tasks_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	private function TaskExists($taskId)
	{
		$taskRow = $this->Database->tasks()->where('id = :1', $taskId)->fetch();
		return $taskRow !== null;
	}
}
