<?php

namespace Grocy\Controllers;

use \Grocy\Services\ChoresService;

class ChoresController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ChoresService = new ChoresService();
	}

	protected $ChoresService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'choresoverview', [
			'chores' => $this->Database->chores()->orderBy('name'),
			'currentChores' => $this->ChoresService->GetCurrent(),
			'nextXDays' => 5
		]);
	}

	public function TrackChoreExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'choretracking', [
			'chores' => $this->Database->chores()->orderBy('name'),
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function ChoresList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'chores', [
			'chores' => $this->Database->chores()->orderBy('name')
		]);
	}

	public function Analysis(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'choresanalysis', [
			'choresLog' => $this->Database->chores_log()->orderBy('tracked_time', 'DESC'),
			'chores' => $this->Database->chores()->orderBy('name'),
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function ChoreEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['choredId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'choreform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'choreform', [
				'chore' =>  $this->Database->chores($args['choreId']),
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService'),
				'mode' => 'edit'
			]);
		}
	}
}
