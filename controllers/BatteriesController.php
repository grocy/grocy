<?php

namespace Grocy\Controllers;

use \Grocy\Services\BatteriesService;
use \Grocy\Services\UsersService;
use \Grocy\Services\UserfieldsService;

class BatteriesController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->BatteriesService = new BatteriesService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $BatteriesService;
	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$usersService = new UsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['batteries_due_soon_days'];

		return $this->AppContainer->view->render($response, 'batteriesoverview', [
			'batteries' => $this->Database->batteries()->orderBy('name'),
			'current' => $this->BatteriesService->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->UserfieldsService->GetFields('batteries'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('batteries')
		]);
	}

	public function TrackChargeCycle(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'batterytracking', [
			'batteries' =>  $this->Database->batteries()->orderBy('name')
		]);
	}

	public function BatteriesList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'batteries', [
			'batteries' => $this->Database->batteries()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('batteries'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('batteries')
		]);
	}

	public function BatteryEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['batteryId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('batteries')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'battery' =>  $this->Database->batteries($args['batteryId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('batteries')
			]);
		}
	}

	public function Journal(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'batteriesjournal', [
			'chargeCycles' => $this->Database->battery_charge_cycles()->orderBy('tracked_time', 'DESC'),
			'batteries' => $this->Database->batteries()->orderBy('name')
		]);
	}

	public function BatteriesSettings(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'batteriessettings');
	}
}
