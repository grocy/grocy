<?php

namespace Grocy\Controllers;

class GenericEntityApiController extends BaseApiController
{
	public function GetObjects(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			return $this->ApiResponse($this->Database->{$args['entity']}());
		}
		else
		{
			return $this->VoidApiActionResponse($response, false, 400, 'Entity does not exist or is not exposed');
		}
	}

	public function GetObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']) && !$this->IsEntityWithPreventedListing($args['entity']))
		{
			return $this->ApiResponse($this->Database->{$args['entity']}($args['objectId']));
		}
		else
		{
			return $this->VoidApiActionResponse($response, false, 400, 'Entity does not exist or is not exposed');
		}
	}

	public function AddObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$newRow = $this->Database->{$args['entity']}()->createRow($request->getParsedBody());
			$newRow->save();
			$success = $newRow->isClean();
			return $this->ApiResponse(array('success' => $success));
		}
		else
		{
			return $this->VoidApiActionResponse($response, false, 400, 'Entity does not exist or is not exposed');
		}
	}

	public function EditObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$row = $this->Database->{$args['entity']}($args['objectId']);
			$row->update($request->getParsedBody());
			$success = $row->isClean();
			return $this->ApiResponse(array('success' => $success));
		}
		else
		{
			return $this->VoidApiActionResponse($response, false, 400, 'Entity does not exist or is not exposed');
		}
	}

	public function DeleteObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($this->IsValidEntity($args['entity']))
		{
			$row = $this->Database->{$args['entity']}($args['objectId']);
			$row->delete();
			$success = $row->isClean();
			return $this->ApiResponse(array('success' => $success));
		}
		else
		{
			return $this->VoidApiActionResponse($response, false, 400, 'Entity does not exist or is not exposed');
		}
	}

	private function IsValidEntity($entity)
	{
		return in_array($entity, $this->OpenApiSpec->components->internalSchemas->ExposedEntity->enum);
	}

	private function IsEntityWithPreventedListing($entity)
	{
		return in_array($entity, $this->OpenApiSpec->components->internalSchemas->ExposedEntitiesPreventListing->enum);
	}
}
