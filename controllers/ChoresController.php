<?php

namespace Grocy\Controllers;

use \Grocy\Services\ChoresService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class ChoresController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->ChoresService = new ChoresService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $ChoresService;
	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['chores_due_soon_days'];

		return $this->AppContainer->view->render($response, 'choresoverview', [
			'chores' => $this->Database->chores()->orderBy('name'),
			'currentChores' => $this->ChoresService->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->UserfieldsService->GetFields('chores'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('chores')
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
			'chores' => $this->Database->chores()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('chores'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('chores')
		]);
	}

	public function Journal(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'choresjournal', [
			'choresLog' => $this->Database->chores_log()->orderBy('tracked_time', 'DESC'),
			'chores' => $this->Database->chores()->orderBy('name'),
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function ChoreEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['choreId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'choreform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService'),
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('chores')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'choreform', [
				'chore' =>  $this->Database->chores($args['choreId']),
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService'),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('chores')
			]);
		}
	}

	public function ChoresSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'choressettings');
	}
}
