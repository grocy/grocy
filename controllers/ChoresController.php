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

		return $this->renderPage($response, 'choresoverview', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'currentChores' => $this->ChoresService->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->UserfieldsService->GetFields('chores'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('chores'),
			'users' => $usersService->GetUsersAsDto()
		]);
	}

	public function TrackChoreExecution(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'choretracking', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function ChoresList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'chores', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('chores'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('chores')
		]);
	}

	public function Journal(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'choresjournal', [
			'choresLog' => $this->getDatabase()->chores_log()->orderBy('tracked_time', 'DESC'),
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function ChoreEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$usersService = new UsersService();
		$users = $usersService->GetUsersAsDto();

		if ($args['choreId'] == 'new')
		{
			return $this->renderPage($response, 'choreform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_PERIOD_TYPE_'),
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('chores'),
				'assignmentTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_ASSIGNMENT_TYPE_'),
				'users' => $users,
				'products' => $this->getDatabase()->products()->orderBy('name')
			]);
		}
		else
		{
			return $this->renderPage($response, 'choreform', [
				'chore' =>  $this->getDatabase()->chores($args['choreId']),
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_PERIOD_TYPE_'),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('chores'),
				'assignmentTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_ASSIGNMENT_TYPE_'),
				'users' => $users,
				'products' => $this->getDatabase()->products()->orderBy('name')
			]);
		}
	}

	public function ChoresSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'choressettings');
	}
}
