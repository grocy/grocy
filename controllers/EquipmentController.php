<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EquipmentController extends BaseController
{
	protected $UserfieldsService;

	public function EditForm(Request $request, Response $response, array $args)
	{
		if ($args['equipmentId'] == 'new')
		{
			return $this->renderPage($response, 'equipmentform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('equipment')
			]);
		}
		else
		{
			return $this->renderPage($response, 'equipmentform', [
				'equipment' => $this->getDatabase()->equipment($args['equipmentId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('equipment')
			]);
		}
	}

	public function Overview(Request $request, Response $response, array $args)
	{
		return $this->renderPage($response, 'equipment', [
			'equipment' => $this->getDatabase()->equipment()->orderBy('name', 'COLLATE NOCASE'),
			'userfields' => $this->getUserfieldsService()->GetFields('equipment'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('equipment')
		]);
	}
}
