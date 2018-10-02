<?php

namespace Grocy\Controllers;


class EquipmentController extends BaseController
{
	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'equipment', [
			'equipment' => $this->Database->equipment()->orderBy('name')
		]);
	}

	public function EditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['equipmentId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'equipmentform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'equipmentform', [
				'equipment' =>  $this->Database->equipment($args['equipmentId']),
				'mode' => 'edit'
			]);
		}
	}
}
