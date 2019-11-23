<?php

namespace Grocy\Controllers;

class UsersController extends BaseController
{
	public function UsersList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'users', [
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
			return $this->renderPage($response, 'userform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->renderPage($response, 'userform', [
				'user' =>  $this->getDatabase()->users($args['userId']),
				'mode' => 'edit'
			]);
		}
	}
}
