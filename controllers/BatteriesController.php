<?php

namespace Grocy\Controllers;

class BatteriesController extends BaseController
{
	public function BatteriesList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['include_disabled']))
		{
			$batteries = $this->getDatabase()->batteries()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$batteries = $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		}

		return $this->renderPage($request, $response, 'batteries', [
			'batteries' => $batteries,
			'userfields' => $this->getUserfieldsService()->GetFields('batteries'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('batteries')
		]);
	}

	public function BatteriesSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'batteriessettings');
	}

	public function BatteryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['batteryId'] == 'new')
		{
			return $this->renderPage($request, $response, 'batteryform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('batteries')
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'batteryform', [
				'battery' => $this->getDatabase()->batteries($args['batteryId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('batteries')
			]);
		}
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'batteriesjournal', [
			'chargeCycles' => $this->getDatabase()->battery_charge_cycles()->orderBy('tracked_time', 'DESC'),
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['batteries_due_soon_days'];

		return $this->renderPage($request, $response, 'batteriesoverview', [
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'current' => $this->getBatteriesService()->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('batteries'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('batteries')
		]);
	}

	public function TrackChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'batterytracking', [
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
