<?php

namespace Grocy\Controllers;

class UsersController extends BaseController
{
	public function UsersList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'users', [
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
