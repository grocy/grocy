<?php

namespace Grocy\Controllers;

use Grocy\Services\HabitsService;

class HabitsController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->HabitsService = new HabitsService();
	}

	protected $HabitsService;

	public function Overview($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'habitsoverview', [
			'title' => 'Habits overview',
			'contentPage' => 'habitsoverview.php',
			'habits' => $this->Database->habits(),
			'currentHabits' => $this->HabitsService->GetCurrentHabits(),
		]);
	}

	public function TrackHabitExecution($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'habittracking', [
			'title' => 'Habit tracking',
			'contentPage' => 'habittracking.php',
			'habits' => $this->Database->habits()
		]);
	}

	public function HabitsList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'habits', [
			'title' => 'Habits',
			'contentPage' => 'habits.php',
			'habits' => $this->Database->habits()
		]);
	}

	public function HabitEditForm($request, $response, $args)
	{
		if ($args['habitId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'habitform', [
				'title' => 'Create habit',
				'contentPage' => 'habitform.php',
				'periodTypes' => GetClassConstants('Grocy\Services\HabitsService'),
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'habitform', [
				'title' => 'Edit habit',
				'contentPage' => 'habitform.php',
				'habit' =>  $this->Database->habits($args['habitId']),
				'periodTypes' => GetClassConstants('Grocy\Services\HabitsService'),
				'mode' => 'edit'
			]);
		}
	}
}
