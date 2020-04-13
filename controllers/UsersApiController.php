<?php

namespace Grocy\Controllers;

class UsersApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function GetUsers(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getUsersService()->GetUsersAsDto());
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function CreateUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			$this->getUsersService()->CreateUser($requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function DeleteUser(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
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
		$requestBody = $request->getParsedBody();

		try
		{
			$this->getUsersService()->EditUser($args['userId'], $requestBody['username'], $requestBody['first_name'], $requestBody['last_name'], $requestBody['password']);
			return $this->EmptyApiResponse($response);
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

	public function GetUserSetting(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$value = $this->getUsersService()->GetUserSetting(GROCY_USER_ID, $args['settingKey']);
			return $this->ApiResponse($response, array('value' => $value));
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
			$requestBody = $request->getParsedBody();

			$value = $this->getUsersService()->SetUserSetting(GROCY_USER_ID, $args['settingKey'], $requestBody['value']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
