<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class UsersApiController extends BaseApiController
{
	public function AddPermission(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			User::checkPermission($request, User::PERMISSION_ADMIN);
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$this->getDatabase()->user_permissions()->createRow([
				'user_id' => $args['userId'],
				'permission_id' => $requestBody['permission_id']
			])->save();
			return $this->EmptyApiResponse($response);
		}
		catch (\Slim\Exception\HttpSpecializedException $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage(), $ex->getCode());
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CreateUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_USERS_CREATE);
		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			$this->getUsersService()->CreateUser($requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password'], $requestBody['picture_file_name']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function DeleteUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_USERS_EDIT);
		try
		{
			$this->getUsersService()->DeleteUser($args['userId']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function EditUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['userId'] == GROCY_USER_ID)
		{
			User::checkPermission($request, User::PERMISSION_USERS_EDIT_SELF);
		}
		else
		{
			User::checkPermission($request, User::PERMISSION_USERS_EDIT);
		}

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			$this->getUsersService()->EditUser($args['userId'], $requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password'], $requestBody['picture_file_name']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function GetUserSetting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$value = $this->getUsersService()->GetUserSetting(GROCY_USER_ID, $args['settingKey']);
			return $this->ApiResponse($response, ['value' => $value]);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function GetUserSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getUsersService()->GetUserSettings(GROCY_USER_ID));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function GetUsers(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_USERS_READ);
		try
		{
			return $this->FilteredApiResponse($response, $this->getUsersService()->GetUsersAsDto(), $request->getQueryParams());
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CurrentUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getUsersService()->GetUsersAsDto()->where('id', GROCY_USER_ID));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ListPermissions(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			User::checkPermission($request, User::PERMISSION_ADMIN);

			return $this->ApiResponse(
				$response,
				$this->getDatabase()->user_permissions()->where('user_id', $args['userId'])
			);
		}
		catch (\Slim\Exception\HttpSpecializedException $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage(), $ex->getCode());
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function SetPermissions(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			User::checkPermission($request, User::PERMISSION_ADMIN);

			$requestBody = $request->getParsedBody();
			$db = $this->getDatabase();
			$db->user_permissions()
				->where('user_id', $args['userId'])
				->delete();

			$perms = [];
			if (GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease')
			{
				// For demo mode always all users have and keep the ADMIN permission
				$perms[] = [
					'user_id' => $args['userId'],
					'permission_id' => 1
				];
			}
			else
			{
				foreach ($requestBody['permissions'] as $perm_id)
				{
					$perms[] = [
						'user_id' => $args['userId'],
						'permission_id' => $perm_id
					];
				}
			}
			$db->insert('user_permissions', $perms, 'batch');

			return $this->EmptyApiResponse($response);
		}
		catch (\Slim\Exception\HttpSpecializedException $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage(), $ex->getCode());
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function SetUserSetting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			$value = $this->getUsersService()->SetUserSetting(GROCY_USER_ID, $args['settingKey'], $requestBody['value']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function DeleteUserSetting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$value = $this->getUsersService()->DeleteUserSetting(GROCY_USER_ID, $args['settingKey']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
