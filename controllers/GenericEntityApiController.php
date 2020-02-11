<?php

namespace Grocy\Controllers;

use \Grocy\Services\UserfieldsService;

class GenericEntityApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $UserfieldsService;

	public function GetObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			return $this->ApiResponse($response, $this->Database->{$args['entity']}());
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function GetObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			return $this->ApiResponse($response, $this->Database->{$args['entity']}($args['objectId']));
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function AddObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$requestBody = $request->getParsedBody();

			try
			{
				if ($requestBody === null)
				{
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$newRow = $this->Database->{$args['entity']}()->createRow($requestBody);
				$newRow->save();
				$success = $newRow->isClean();
				return $this->ApiResponse($response, array(
					'created_object_id' => $this->Database->lastInsertId()
				));
			}
			catch (\Exception $ex)
			{
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function EditObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$requestBody = $request->getParsedBody();

			try
			{
				if ($requestBody === null)
				{
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$row = $this->Database->{$args['entity']}($args['objectId']);
				$row->update($requestBody);
				$success = $row->isClean();
				return $this->EmptyApiResponse($response);
			}
			catch (\Exception $ex)
			{
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function DeleteObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$row = $this->Database->{$args['entity']}($args['objectId']);
			$row->delete();
			$success = $row->isClean();
			return $this->EmptyApiResponse($response);
		}
		else
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function SearchObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			try
			{
				return $this->ApiResponse($response, $this->Database->{$args['entity']}()->where('name LIKE ?', '%' . $args['searchString'] . '%'));
			}
			catch (\PDOException $ex)
			{
				return $this->GenericErrorResponse($response, 'The given entity has no field "name"');
			}
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function GetUserfields(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->UserfieldsService->GetValues($args['entity'], $args['objectId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function SetUserfields(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			if ($requestBody === null)
			{
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			$this->UserfieldsService->SetValues($args['entity'], $args['objectId'], $requestBody);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	private function IsValidEntity($entity)
	{
		return in_array($entity, $this->OpenApiSpec->components->internalSchemas->ExposedEntity->enum);
	}

	private function IsEntityWithPreventedListing($entity)
	{
		return !in_array($entity, $this->OpenApiSpec->components->internalSchemas->ExposedEntityButNoListing->enum);
	}
}
