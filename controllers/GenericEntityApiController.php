<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Slim\Exception\HttpBadRequestException;

class GenericEntityApiController extends BaseApiController
{
	public function AddObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidEntity($args['entity']))
		{
			if ($this->IsEntityWithEditRequiresAdmin($args['entity']))
			{
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}

			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			try
			{
				if ($requestBody === null)
				{
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$newRow = $this->getDatabase()->{$args['entity']}
				()->createRow($requestBody);
				$newRow->save();
				$success = $newRow->isClean();
				return $this->ApiResponse($response, [
					'created_object_id' => $this->getDatabase()->lastInsertId()
				]);
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
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidEntity($args['entity']))
		{
			if ($this->IsEntityWithEditRequiresAdmin($args['entity']))
			{
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}
			$row = $this->getDatabase()->{$args['entity']}
			($args['objectId']);
			$row->delete();
			$success = $row->isClean();
			return $this->EmptyApiResponse($response);
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Invalid entity');
		}
	}

	public function EditObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidEntity($args['entity']))
		{
			if ($this->IsEntityWithEditRequiresAdmin($args['entity']))
			{
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}

			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			try
			{
				if ($requestBody === null)
				{
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$row = $this->getDatabase()->{$args['entity']}
				($args['objectId']);
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

	public function GetObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			$userfields = $this->getUserfieldsService()->GetValues($args['entity'], $args['objectId']);

			if (count($userfields) === 0)
			{
				$userfields = null;
			}

			$object = $this->getDatabase()->{$args['entity']}
			($args['objectId']);

			if ($object == null)
			{
				return $this->GenericErrorResponse($response, 'Object not found', 404);
			}

			$object['userfields'] = $userfields;

			return $this->ApiResponse($response, $object);
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function GetObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$objects = $this->getDatabase()->{$args['entity']}
		();
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

	public function SearchObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			try
			{
				return $this->FilteredApiResponse($response, $this->getDatabase()->{$args['entity']}
					()->where('name LIKE ?', '%' . $args['searchString'] . '%'), $request->getQueryParams());
			}
			catch (\PDOException $ex)
			{
				throw new HttpBadRequestException($request, $ex->getMessage(), $ex);
				//return $this->GenericErrorResponse($response, 'The given entity has no field "name"', $ex);
			}
		}
		else
		{
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function SetUserfields(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

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

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	private function IsEntityWithEditRequiresAdmin($entity)
	{
		return !in_array($entity, $this->getOpenApiSpec()->components->internalSchemas->EntityEditRequiresAdmin->enum);
	}

	private function IsEntityWithPreventedListing($entity)
	{
		return !in_array($entity, $this->getOpenApiSpec()->components->internalSchemas->ExposedEntityButNoListing->enum);
	}

	private function IsValidEntity($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->internalSchemas->ExposedEntity->enum);
	}
}
