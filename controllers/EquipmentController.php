<?php

namespace Grocy\Controllers;

use \Grocy\Services\UserfieldsService;

class EquipmentController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $UserfieldsService;

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'equipment', [
			'equipment' => $this->Database->equipment()->orderBy('name'),
			'userfields' => $this->UserfieldsService->GetFields('equipment'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('equipment')
		]);
	}

	public function EditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['equipmentId'] == 'new')
		{
			return $this->View->render($response, 'equipmentform', [
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
		else
		{
			return $this->View->render($response, 'equipmentform', [
				'equipment' =>  $this->Database->equipment($args['equipmentId']),
				'mode' => 'edit',
				'userfields' => $this->UserfieldsService->GetFields('equipment')
			]);
		}
	}
}
