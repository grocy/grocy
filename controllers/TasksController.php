<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class TasksController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->TasksService = new TasksService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $TasksService;
	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if (isset($request->getQueryParams()['include_done']))
		{
			$tasks = $this->getDatabase()->tasks()->orderBy('name');
		}
		else
		{
			$tasks = $this->TasksService->GetCurrent();
		}

		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['tasks_due_soon_days'];

		return $this->renderPage($response, 'tasks', [
			'tasks' => $tasks,
			'nextXDays' => $nextXDays,
			'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
			'users' => $this->getDatabase()->users(),
			'userfields' => $this->UserfieldsService->GetFields('tasks'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('tasks')
		]);
	}

	public function TaskEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['taskId'] == 'new')
		{
			return $this->renderPage($response, 'taskform', [
				'mode' => 'create',
				'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->UserfieldsService->GetFields('tasks')
			]);
		}
		else
		{
			return $this->renderPage($response, 'taskform', [
				'task' =>  $this->getDatabase()->tasks($args['taskId']),
				'mode' => 'edit',
				'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
				'users' => $this->getDatabase()->users()->orderBy('username'),
				'userfields' => $this->UserfieldsService->GetFields('tasks')
			]);
		}
	}

	public function TaskCategoriesList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'taskcategories', [
			'taskCategories' => $this->getDatabase()->task_categories()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('task_categories'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('task_categories')
		]);
	}

	public function TaskCategoryEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['categoryId'] == 'new')
		{
			return $this->renderPage($response, 'taskcategoryform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('task_categories')
			]);
		}
		else
		{
			return $this->renderPage($response, 'taskcategoryform', [
				'category' =>  $this->getDatabase()->task_categories($args['categoryId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('task_categories')
			]);
		}
	}

	public function TasksSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'taskssettings');
	}
}
