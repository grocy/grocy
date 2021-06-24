<?php

namespace Grocy\Controllers;

class ChoresController extends BaseController
{
	public function ChoreEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$users = $usersService->GetUsersAsDto();

		if ($args['choreId'] == 'new')
		{
			return $this->renderPage($request, $response, 'choreform', [
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_PERIOD_TYPE_'),
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('chores'),
				'assignmentTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_ASSIGNMENT_TYPE_'),
				'users' => $users,
				'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'choreform', [
				'chore' => $this->getDatabase()->chores($args['choreId']),
				'periodTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_PERIOD_TYPE_'),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('chores'),
				'assignmentTypes' => GetClassConstants('\Grocy\Services\ChoresService', 'CHORE_ASSIGNMENT_TYPE_'),
				'users' => $users,
				'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE')
			]);
		}
	}

	public function ChoresList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['include_disabled']))
		{
			$chores = $this->getDatabase()->chores()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$chores = $this->getDatabase()->chores()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		}

		return $this->renderPage($request, $response, 'chores', [
			'chores' => $chores,
			'userfields' => $this->getUserfieldsService()->GetFields('chores'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('chores')
		]);
	}

	public function ChoresSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'choressettings');
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'choresjournal', [
			'choresLog' => $this->getDatabase()->chores_log()->orderBy('tracked_time', 'DESC'),
			'chores' => $this->getDatabase()->chores()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['chores_due_soon_days'];

		return $this->renderPage($request, $response, 'choresoverview', [
			'chores' => $this->getDatabase()->chores()->orderBy('name', 'COLLATE NOCASE'),
			'currentChores' => $this->getChoresService()->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('chores'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('chores'),
			'users' => $usersService->GetUsersAsDto()
		]);
	}

	public function TrackChoreExecution(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'choretracking', [
			'chores' => $this->getDatabase()->chores()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
