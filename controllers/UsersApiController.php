<?php

namespace Grocy\Controllers;

use \Grocy\Services\UsersService;

class UsersApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->UsersService = new UsersService();
	}

	protected $UsersService;

	public function GetUsers(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($this->UsersService->GetUsersAsDto());
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function CreateUser(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$this->UsersService->CreateUser($requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password']);
			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function DeleteUser(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->UsersService->DeleteUser($args['userId']);
			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function EditUser(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$this->UsersService->EditUser($args['userId'], $requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password']);
			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function GetUserSetting(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$value = $this->UsersService->GetUserSetting(GROCY_USER_ID, $args['settingKey']);
			return $this->ApiResponse(array('value' => $value));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function SetUserSetting(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$requestBody = $request->getParsedBody();

			$value = $this->UsersService->SetUserSetting(GROCY_USER_ID, $args['settingKey'], $requestBody['value']);
			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
