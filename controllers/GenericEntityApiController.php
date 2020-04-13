<?php

namespace Grocy\Controllers;

class GenericEntityApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function GetObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$objects = $this->getDatabase()->{$args['entity']}();
		$allUserfields = $this->getUserfieldsService()->GetAllValues($args['entity']);

		foreach ($objects as $object)
		{
			$userfields = FindAllObjectsInArrayByPropertyValue($allUserfields, 'object_id', $object->id);
			$userfieldKeyValuePairs = null;
			if (count($userfields) > 0)
			{
				foreach ($userfields as $userfield)
				{
					$userfieldKeyValuePairs[$userfield->name] = $userfield->value;
				}
			}

			$object->userfields = $userfieldKeyValuePairs;
		}

		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			return $this->ApiResponse($response, $objects);
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
			$userfields = $this->getUserfieldsService()->GetValues($args['entity'], $args['objectId']);
			if (count($userfields) === 0)
			{
				$userfields = null;
			}

			$object = $this->getDatabase()->{$args['entity']}($args['objectId']);
			$object['userfields'] = $userfields;

			return $this->ApiResponse($response, $object);
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

				$newRow = $this->getDatabase()->{$args['entity']}()->createRow($requestBody);
				$newRow->save();
				$success = $newRow->isClean();
				return $this->ApiResponse($response, array(
					'created_object_id' => $this->getDatabase()->lastInsertId()
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

				$row = $this->getDatabase()->{$args['entity']}($args['objectId']);
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
			$row = $this->getDatabase()->{$args['entity']}($args['objectId']);
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
				return $this->ApiResponse($response, $this->getDatabase()->{$args['entity']}()->where('name LIKE ?', '%' . $args['searchString'] . '%'));
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
			return $this->ApiResponse($response, $this->getUserfieldsService()->GetValues($args['entity'], $args['objectId']));
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

			$this->getUserfieldsService()->SetValues($args['entity'], $args['objectId'], $requestBody);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	private function IsValidEntity($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->internalSchemas->ExposedEntity->enum);
	}

	private function IsEntityWithPreventedListing($entity)
	{
		return !in_array($entity, $this->getOpenApiSpec()->components->internalSchemas->ExposedEntityButNoListing->enum);
	}
}
