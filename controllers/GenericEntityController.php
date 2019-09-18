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

	public function UserentitiesList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'userentities', [
			'userentities' => $this->Database->userentities()->orderBy('name')
		]);
	}

	public function UserobjectsList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$userentity = $this->Database->userentities()->where('name = :1', $args['userentityName'])->fetch();

		return $this->AppContainer->view->render($response, 'userobjects', [
			'userentity' => $userentity,
			'userobjects' => $this->Database->userobjects()->where('userentity_id = :1', $userentity->id),
			'userfields' => $this->UserfieldsService->GetFields('userentity-' . $args['userentityName']),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('userentity-' . $args['userentityName'])
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

	public function UserentityEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['userentityId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'userentityform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'userentityform', [
				'mode' => 'edit',
				'userentity' =>  $this->Database->userentities($args['userentityId'])
			]);
		}
	}

	public function UserobjectEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$userentity = $this->Database->userentities()->where('name = :1', $args['userentityName'])->fetch();

		if ($args['userobjectId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'userobjectform', [
				'userentity' => $userentity,
				'mode' => 'create',
				'userfields' => $this->UserfieldsService->GetFields('userentity-' . $args['userentityName'])
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'userobjectform', [
				'userentity' => $userentity,
				'mode' => 'edit',
				'userobject' =>  $this->Database->userobjects($args['userobjectId']),
				'userfields' => $this->UserfieldsService->GetFields('userentity-' . $args['userentityName'])
			]);
		}
	}
}
