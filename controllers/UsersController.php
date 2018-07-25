<?php

namespace Grocy\Controllers;

class UsersController extends BaseController
{
	public function UsersList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'users', [
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'userform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'userform', [
				'user' =>  $this->Database->users($args['userId']),
				'mode' => 'edit'
			]);
		}
	}
}
