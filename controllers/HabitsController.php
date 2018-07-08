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
		$nextHabitTimes = array();
		foreach($this->Database->habits() as $habit)
		{
			$nextHabitTimes[$habit->id] = $this->HabitsService->GetNextHabitTime($habit->id);
		}

		$nextXDays = 5;
		$countDueNextXDays = count(FindAllItemsInArrayByValue($nextHabitTimes, date('Y-m-d', strtotime("+$nextXDays days")), '<'));
		$countOverdue = count(FindAllItemsInArrayByValue($nextHabitTimes, date('Y-m-d', strtotime('-1 days')), '<'));
		return $this->AppContainer->view->render($response, 'habitsoverview', [
			'habits' => $this->Database->habits()->orderBy('name'),
			'currentHabits' => $this->HabitsService->GetCurrentHabits(),
			'nextHabitTimes' => $nextHabitTimes,
			'nextXDays' => $nextXDays,
			'countDueNextXDays' => $countDueNextXDays - $countOverdue,
			'countOverdue' => $countOverdue
		]);
	}

	public function TrackHabitExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habittracking', [
			'habits' => $this->Database->habits()->orderBy('name')
		]);
	}

	public function HabitsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'habits', [
			'habits' => $this->Database->habits()->orderBy('name')
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
