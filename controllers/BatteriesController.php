<?php

namespace Grocy\Controllers;

use Grocy\Services\BatteriesService;

class BatteriesController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->BatteriesService = new BatteriesService();
	}

	protected $BatteriesService;

	public function Overview($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'batteriesoverview', [
			'title' => 'Batteries overview',
			'contentPage' => 'batteriesoverview.php',
			'batteries' => $this->Database->batteries(),
			'current' => $this->BatteriesService->GetCurrent(),
		]);
	}

	public function TrackChargeCycle($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'batterytracking', [
			'title' => 'Battery tracking',
			'contentPage' => 'batterytracking.php',
			'batteries' =>  $this->Database->batteries()
		]);
	}

	public function BatteriesList($request, $response, $args)
	{
		return $this->AppContainer->view->render($response, 'batteries', [
			'title' => 'Batteries',
			'contentPage' => 'batteries.php',
			'batteries' => $this->Database->batteries()
		]);
	}

	public function BatteryEditForm($request, $response, $args)
	{
		if ($args['batteryId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'title' => 'Create battery',
				'contentPage' => 'batteryform.php',
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'batteryform', [
				'title' => 'Edit battery',
				'contentPage' => 'batteryform.php',
				'battery' =>  $this->Database->batteries($args['batteryId']),
				'mode' => 'edit'
			]);
		}
	}
}
