<?php

namespace Grocy\Controllers;

class ChoresController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['chores_due_soon_days'];

		return $this->renderPage($response, 'choresoverview', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'currentChores' => $this->getChoresService()->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('chores'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('chores'),
			'users' => $usersService->GetUsersAsDto()
		]);
	}

	public function TrackChoreExecution(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'choretracking', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function ChoresList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'chores', [
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'userfields' => $this->getUserfieldsService()->GetFields('chores'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('chores')
		]);
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'choresjournal', [
			'choresLog' => $this->getDatabase()->chores_log()->orderBy('tracked_time', 'DESC'),
			'chores' => $this->getDatabase()->chores()->orderBy('name'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function ChoreEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$users = $usersService->GetUsersAsDto();

		if ($args['choreId'] == 'new')
		{
			return $this->renderPage($response, 'choreform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_PERIOD_TYPE_'),
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('chores'),
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
				'userfields' => $this->getUserfieldsService()->GetFields('chores'),
				'assignmentTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_ASSIGNMENT_TYPE_'),
				'users' => $users,
				'products' => $this->getDatabase()->products()->orderBy('name')
			]);
		}
	}

	public function ChoresSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'choressettings');
	}
}
