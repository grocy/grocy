<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;
use \Grocy\Services\UsersService;

class TasksController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->TasksService = new TasksService();
	}

	protected $TasksService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if (isset($request->getQueryParams()['include_done']))
		{
			$tasks = $this->Database->tasks()->orderBy('name');
		}
		else
		{
			$tasks = $this->TasksService->GetCurrent();
		}

		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['tasks_due_soon_days'];

		return $this->AppContainer->view->render($response, 'tasks', [
			'tasks' => $tasks,
			'nextXDays' => $nextXDays,
			'taskCategories' => $this->Database->task_categories()->orderBy('name'),
			'users' => $this->Database->users()
		]);
	}

	public function TaskEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['taskId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'taskform', [
				'mode' => 'create',
				'taskCategories' => $this->Database->task_categories()->orderBy('name'),
				'users' => $this->Database->users()->orderBy('username')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'taskform', [
				'task' =>  $this->Database->tasks($args['taskId']),
				'mode' => 'edit',
				'taskCategories' => $this->Database->task_categories()->orderBy('name'),
				'users' => $this->Database->users()->orderBy('username')
			]);
		}
	}

	public function TaskCategoriesList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'taskcategories', [
			'taskCategories' => $this->Database->task_categories()->orderBy('name')
		]);
	}

	public function TaskCategoryEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['categoryId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'taskcategoryform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'taskcategoryform', [
				'category' =>  $this->Database->task_categories($args['categoryId']),
				'mode' => 'edit'
			]);
		}
	}

	public function TasksSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'taskssettings');
	}
}
