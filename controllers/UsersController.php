<?php

namespace Grocy\Controllers;

class UsersController extends BaseController
{
	public function UsersList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'users', [
			'users' => $this->Database->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
			return $this->View->render($response, 'userform', [
				'mode' => 'create'
			]);
		}
		else
		{
			return $this->View->render($response, 'userform', [
				'user' =>  $this->Database->users($args['userId']),
				'mode' => 'edit'
			]);
		}
	}
}
