<?php

namespace Grocy\Controllers;

use \Grocy\Services\BatteriesService;

class BatteriesController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->BatteriesService = new BatteriesService();
	}

	protected $BatteriesService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$nextChargeTimes = array();
		foreach($this->Database->batteries() as $battery)
		{
			$nextChargeTimes[$battery->id] = $this->BatteriesService->GetNextChargeTime($battery->id);
		}

		$nextXDays = 5;
		$countDueNextXDays = count(FindAllItemsInArrayByValue($nextChargeTimes, date('Y-m-d', strtotime("+$nextXDays days")), '<'));
		$countOverdue = count(FindAllItemsInArrayByValue($nextChargeTimes, date('Y-m-d', strtotime('-1 days')), '<'));
		return $this->AppContainer->view->render($response, 'batteriesoverview', [
			'batteries' => $this->Database->batteries()->orderBy('name'),
			'current' => $this->BatteriesService->GetCurrent(),
			'nextChargeTimes' => $nextChargeTimes,
			'nextXDays' => $nextXDays,
			'countDueNextXDays' => $countDueNextXDays - $countOverdue,
			'countOverdue' => $countOverdue
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
			'batteries' => $this->Database->batteries()->orderBy('name')
		]);
	}

	public function BatteryEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['batteryId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'battery' =>  $this->Database->batteries($args['batteryId']),
				'mode' => 'edit'
			]);
		}
	}
}
