<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class UsersController extends BaseController
{
	public function UsersList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
        User::checkPermission($request, User::PERMISSION_READ_USER);
        return $this->renderPage($response, 'users', [
			'users' => $this->getDatabase()->users()->orderBy('username')
		]);
	}

	public function UserEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
            User::checkPermission($request, User::PERMISSION_CREATE_USER);
            return $this->renderPage($response, 'userform', [
				'mode' => 'create'
			]);
		}
		else
		{
		    if($args['userId'] == GROCY_USER_ID)
                User::checkPermission($request, User::PERMISSION_EDIT_SELF);
		    else User::checkPermission($request, User::PERMISSION_EDIT_USER);
            return $this->renderPage($response, 'userform', [
				'user' =>  $this->getDatabase()->users($args['userId']),
				'mode' => 'edit'
			]);
		}
	}

    public function PermissionList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
    {
        User::checkPermission($request, User::PERMISSION_READ_USER);
        return $this->renderPage($response, 'permissions', [
            'user' => $this->getDatabase()->users($args['userId']),
            'permissions' => $this->getDatabase()->uihelper_permission()
                ->where('parent IS NULL')->where('user_id', $args['userId']),
        ]);
    }
}
