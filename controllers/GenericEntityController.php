<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GenericEntityController extends BaseController
{
	public function UserentitiesList(Request $request, Response $response, array $args)
	{
		return $this->renderPage($response, 'userentities', [
			'userentities' => $this->getDatabase()->userentities()->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function UserentityEditForm(Request $request, Response $response, array $args)
	{
		if ($args['userentityId'] == 'new')
		{
			return $this->renderPage($response, 'userentityform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->renderPage($response, 'userentityform', [
				'mode' => 'edit',
				'userentity' => $this->getDatabase()->userentities($args['userentityId'])
			]);
		}
	}

	public function UserfieldEditForm(Request $request, Response $response, array $args)
	{
		if ($args['userfieldId'] == 'new')
		{
			return $this->renderPage($response, 'userfieldform', [
				'mode' => 'create',
				'userfieldTypes' => $this->getUserfieldsService()->GetFieldTypes(),
				'entities' => $this->getUserfieldsService()->GetEntities()
			]);
		}
		else
		{
			return $this->renderPage($response, 'userfieldform', [
				'mode' => 'edit',
				'userfield' => $this->getUserfieldsService()->GetField($args['userfieldId']),
				'userfieldTypes' => $this->getUserfieldsService()->GetFieldTypes(),
				'entities' => $this->getUserfieldsService()->GetEntities()
			]);
		}
	}

	public function UserfieldsList(Request $request, Response $response, array $args)
	{
		return $this->renderPage($response, 'userfields', [
			'userfields' => $this->getUserfieldsService()->GetAllFields(),
			'entities' => $this->getUserfieldsService()->GetEntities()
		]);
	}

	public function UserobjectEditForm(Request $request, Response $response, array $args)
	{
		$userentity = $this->getDatabase()->userentities()->where('name = :1', $args['userentityName'])->fetch();

		if ($args['userobjectId'] == 'new')
		{
			return $this->renderPage($response, 'userobjectform', [
				'userentity' => $userentity,
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('userentity-' . $args['userentityName'])
			]);
		}
		else
		{
			return $this->renderPage($response, 'userobjectform', [
				'userentity' => $userentity,
				'mode' => 'edit',
				'userobject' => $this->getDatabase()->userobjects($args['userobjectId']),
				'userfields' => $this->getUserfieldsService()->GetFields('userentity-' . $args['userentityName'])
			]);
		}
	}

	public function UserobjectsList(Request $request, Response $response, array $args)
	{
		$userentity = $this->getDatabase()->userentities()->where('name = :1', $args['userentityName'])->fetch();

		return $this->renderPage($response, 'userobjects', [
			'userentity' => $userentity,
			'userobjects' => $this->getDatabase()->userobjects()->where('userentity_id = :1', $userentity->id),
			'userfields' => $this->getUserfieldsService()->GetFields('userentity-' . $args['userentityName']),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('userentity-' . $args['userentityName'])
		]);
	}
}
