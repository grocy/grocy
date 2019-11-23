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
		return $this->renderPage($response, 'equipment', [
			'equipment' => $this->getDatabase()->equipment()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('equipment'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('equipment')
		]);
	}

	public function EditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['equipmentId'] == 'new')
		{
			return $this->renderPage($response, 'equipmentform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
		else
		{
			return $this->renderPage($response, 'equipmentform', [
				'equipment' =>  $this->getDatabase()->equipment($args['equipmentId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
	}
}
