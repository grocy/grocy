<?php

namespace Grocy\Controllers;

use \Grocy\Services\HabitsService;

class HabitsController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->HabitsService = new HabitsService();
	}

	protected $HabitsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habitsoverview', [
			'habits' => $this->Database->habits()->orderBy('name'),
			'currentHabits' => $this->HabitsService->GetCurrent(),
			'nextXDays' => 5
		]);
	}

	public function TrackHabitExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habittracking', [
			'habits' => $this->Database->habits()->orderBy('name'),
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function HabitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habits', [
			'habits' => $this->Database->habits()->orderBy('name')
		]);
	}

	public function Analysis(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habitsanalysis', [
			'habitsLog' => $this->Database->habits_log()->orderBy('tracked_time', 'DESC'),
			'habits' => $this->Database->habits()->orderBy('name'),
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function HabitEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['habitId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'habitform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\HabitsService'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'habitform', [
				'habit' =>  $this->Database->habits($args['habitId']),
				'periodTypes' => GetClassConstants('\Grocy\Services\HabitsService'),
				'mode' => 'edit'
			]);
		}
	}
}
