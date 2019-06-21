<?php

namespace Grocy\Controllers;

use \Grocy\Services\UserfieldsService;

class EquipmentController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'equipment', [
			'equipment' => $this->Database->equipment()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('equipment'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('equipment')
		]);
	}

	public function EditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['equipmentId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'equipmentform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'equipmentform', [
				'equipment' =>  $this->Database->equipment($args['equipmentId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
	}
}
