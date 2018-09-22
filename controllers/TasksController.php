<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;

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
		return $this->AppContainer->view->render($response, 'tasks', [
			'tasks' => $this->Database->tasks()->orderBy('name'),
			'nextXDays' => 5
		]);
	}

	public function TaskEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['taskdId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'taskform', [
				'mode' => 'create',
				'taskCategories' => $this->Database->task_categories()->orderBy('name')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'taskform', [
				'task' =>  $this->Database->tasks($args['taskId']),
				'mode' => 'edit',
				'taskCategories' => $this->Database->task_categories()->orderBy('name')
			]);
		}
	}
}
