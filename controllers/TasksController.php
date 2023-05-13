<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TasksController extends BaseController
{
	public function Overview(Request $request, Response $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['tasks_due_soon_days'];

		if (isset($request->getQueryParams()['include_done']))
		{
			$tasks = $this->getDatabase()->tasks()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$tasks = $this->getTasksService()->GetCurrent();
		}

		foreach ($tasks as $task)
		{
			if (empty($task->due_date))
			{
				$task->due_type = '';
			}
			elseif ($task->due_date < date('Y-m-d 23:59:59', strtotime('-1 days')))
			{
				$task->due_type = 'overdue';
			}
			elseif ($task->due_date <= date('Y-m-d 23:59:59'))
			{
				$task->due_type = 'duetoday';
			}
			elseif ($nextXDays > 0 && $task->due_date <= date('Y-m-d 23:59:59', strtotime('+' . $nextXDays . ' days')))
			{
				$task->due_type = 'duesoon';
			}
		}

		return $this->renderPage($response, 'tasks', [
			'tasks' => $tasks,
			'nextXDays' => $nextXDays,
			'taskCategories' => $this->getDatabase()->task_categories()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'users' => $this->getDatabase()->users(),
			'userfields' => $this->getUserfieldsService()->GetFields('tasks'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('tasks')
		]);
	}

	public function TaskCategoriesList(Request $request, Response $response, array $args)
	{
		if (isset($request->getQueryParams()['include_disabled']))
		{
			$categories = $this->getDatabase()->task_categories()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$categories = $this->getDatabase()->task_categories()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		}

		return $this->renderPage($response, 'taskcategories', [
			'taskCategories' => $categories,
			'userfields' => $this->getUserfieldsService()->GetFields('task_categories'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('task_categories')
		]);
	}

	public function TaskCategoryEditForm(Request $request, Response $response, array $args)
	{
		if ($args['categoryId'] == 'new')
		{
			return $this->renderPage($response, 'taskcategoryform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('task_categories')
			]);
		}
		else
		{
			return $this->renderPage($response, 'taskcategoryform', [
				'category' => $this->getDatabase()->task_categories($args['categoryId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('task_categories')
			]);
		}
	}

	public function TaskEditForm(Request $request, Response $response, array $args)
	{
		if ($args['taskId'] == 'new')
		{
			return $this->renderPage($response, 'taskform', [
				'mode' => 'create',
				'taskCategories' => $this->getDatabase()->task_categories()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->getUserfieldsService()->GetFields('tasks')
			]);
		}
		else
		{
			return $this->renderPage($response, 'taskform', [
				'task' => $this->getDatabase()->tasks($args['taskId']),
				'mode' => 'edit',
				'taskCategories' => $this->getDatabase()->task_categories()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->getUserfieldsService()->GetFields('tasks')
			]);
		}
	}

	public function TasksSettings(Request $request, Response $response, array $args)
	{
		return $this->renderPage($response, 'taskssettings');
	}
}
