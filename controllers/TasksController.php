<?php

namespace Grocy\Controllers;

class TasksController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['include_done']))
		{
			$tasks = $this->getDatabase()->tasks()->orderBy('name');
		}
		else
		{
			$tasks = $this->getTasksService()->GetCurrent();
		}

		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['tasks_due_soon_days'];

		return $this->renderPage($response, 'tasks', [
			'tasks' => $tasks,
			'nextXDays' => $nextXDays,
			'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
			'users' => $this->getDatabase()->users(),
			'userfields' => $this->getUserfieldsService()->GetFields('tasks'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('tasks')
		]);
	}

	public function TaskEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['taskId'] == 'new')
		{
			return $this->renderPage($response, 'taskform', [
				'mode' => 'create',
				'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->getUserfieldsService()->GetFields('tasks')
			]);
		}
		else
		{
			return $this->renderPage($response, 'taskform', [
				'task' =>  $this->getDatabase()->tasks($args['taskId']),
				'mode' => 'edit',
				'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->getUserfieldsService()->GetFields('tasks')
			]);
		}
	}

	public function TaskCategoriesList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'taskcategories', [
			'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('task_categories'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('task_categories')
		]);
	}

	public function TaskCategoryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
				'category' =>  $this->getDatabase()->task_categories($args['categoryId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('task_categories')
			]);
		}
	}

	public function TasksSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'taskssettings');
	}
}
