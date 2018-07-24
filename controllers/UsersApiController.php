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
			$success = $this->UsersService->DeleteUser($args['userId']);
			return $this->ApiResponse(array('success' => $success));
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
}
