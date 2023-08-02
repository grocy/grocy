<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsersController extends BaseController
{
	public function PermissionList(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_USERS_READ);
		return $this->renderPage($response, 'userpermissions', [
			'user' => $this->getDatabase()->users($args['userId']),
			'permissions' => $this->getDatabase()->uihelper_user_permissions()
				->where('parent IS NULL')->where('user_id', $args['userId'])
		]);
	}

	public function UserEditForm(Request $request, Response $response, array $args)
	{
		if ($args['userId'] == 'new')
		{
			User::checkPermission($request, User::PERMISSION_USERS_CREATE);
			return $this->renderPage($response, 'userform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('users')
			]);
		}
		else
		{
			if ($args['userId'] == GROCY_USER_ID)
			{
				User::checkPermission($request, User::PERMISSION_USERS_EDIT_SELF);
			}
			else
			{
				User::checkPermission($request, User::PERMISSION_USERS_EDIT);
			}

			return $this->renderPage($response, 'userform', [
				'user' => $this->getDatabase()->users($args['userId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('users'),
				'userfieldValues' => $this->getUserfieldsService()->GetAllValues('users')
			]);
		}
	}

	public function UserSettings(Request $request, Response $response, array $args)
	{
		return $this->renderPage($response, 'usersettings', [
			'languages' => array_filter(scandir(__DIR__ . '/../localization'), function ($item)
			{
				if ($item == '.' || $item == '..')
				{
					return false;
				}

				return is_dir(__DIR__ . '/../localization/' . $item);
			})
		]);
	}

	public function UsersList(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_USERS_READ);
		return $this->renderPage($response, 'users', [
			'users' => $this->getDatabase()->users()->orderBy('username'),
			'userfields' => $this->getUserfieldsService()->GetFields('users'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('users')
		]);
	}
}
