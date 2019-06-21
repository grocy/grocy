<?php

namespace Grocy\Controllers;

use \Grocy\Services\UserfieldsService;

class GenericEntityController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $UserfieldsService;

	public function UserfieldsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'userfields', [
			'userfields' => $this->UserfieldsService->GetAllFields(),
			'entities' => $this->UserfieldsService->GetEntities()
		]);
	}

	public function UserfieldEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['userfieldId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'userfieldform', [
				'mode' => 'create',
				'userfieldTypes' => $this->UserfieldsService->GetFieldTypes(),
				'entities' => $this->UserfieldsService->GetEntities()
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'userfieldform', [
				'mode' => 'edit',
				'userfield' =>  $this->UserfieldsService->GetField($args['userfieldId']),
				'userfieldTypes' => $this->UserfieldsService->GetFieldTypes(),
				'entities' => $this->UserfieldsService->GetEntities()
			]);
		}
	}
}
