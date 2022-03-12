<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use LessQL\Row;

class GenericEntityApiController extends BaseApiController
{
	public function AddObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidExposedEntity($args['entity']) && !$this->IsEntityWithNoEdit($args['entity'])) {
			if ($this->IsEntityWithEditRequiresAdmin($args['entity'])) {
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}

			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			try {
				if ($requestBody === null) {
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$newRow = $this->getDatabase()->{$args['entity']}()->createRow($requestBody);
				$newRow->save();

				return $this->ApiResponse($response, [
					'created_object_id' => $this->getDatabase()->lastInsertId()
				]);
			} catch (\Exception $ex) {
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		} else {
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function DeleteObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidExposedEntity($args['entity']) && !$this->IsEntityWithNoDelete($args['entity'])) {
			if ($this->IsEntityWithEditRequiresAdmin($args['entity'])) {
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}

			$row = $this->getDatabase()->{$args['entity']}($args['objectId']);
			if ($row == null) {
				return $this->GenericErrorResponse($response, 'Object not found', 400);
			}

			$row->delete();

			return $this->EmptyApiResponse($response);
		} else {
			return $this->GenericErrorResponse($response, 'Invalid entity');
		}
	}

	public function EditObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		if ($this->IsValidExposedEntity($args['entity']) && !$this->IsEntityWithNoEdit($args['entity'])) {
			if ($this->IsEntityWithEditRequiresAdmin($args['entity'])) {
				User::checkPermission($request, User::PERMISSION_ADMIN);
			}

			$requestBody = $this->GetParsedAndFilteredRequestBody($request);

			try {
				if ($requestBody === null) {
					throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
				}

				$row = $this->getDatabase()->{$args['entity']}($args['objectId']);
				if ($row == null) {
					return $this->GenericErrorResponse($response, 'Object not found', 400);
				}

				$row->update($requestBody);

				return $this->EmptyApiResponse($response);
			} catch (\Exception $ex) {
				return $this->GenericErrorResponse($response, $ex->getMessage());
			}
		} else {
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function GetObject(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($this->IsValidExposedEntity($args['entity']) && !$this->IsEntityWithNoListing($args['entity'])) {
			$object = $this->getDatabase()->{$args['entity']}($args['objectId']);
			if ($object == null) {
				return $this->GenericErrorResponse($response, 'Object not found', 404);
			}

			$this->addUserfieldsAndJoinsToRow($object, $args);

			return $this->ApiResponse($response, $object);
		} else {
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}
	}

	public function GetObjects(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (!$this->IsValidExposedEntity($args['entity']) || $this->IsEntityWithNoListing($args['entity'])) {
			return $this->GenericErrorResponse($response, 'Entity does not exist or is not exposed');
		}

		$query = $request->getQueryParams();

		// get result and total row count
		$objects = $this->getDatabase()->{$args['entity']}();
		$response = $response->withHeader('x-rowcount-total', $objects->count());

		// apply filter, get filtered row count
		$objects = $this->applyQuery($objects, $query);
		$response = $response->withHeader('x-rowcount-filtered', $objects->count());

		// apply limit/order
		$objects = $this->applyLimit($objects, $query);
		$objects = $this->applyOrder($objects, $query);

		// add entity-specific queries
		if ($args['entity'] === 'products' && isset($query['only_in_stock'])) {
			$objects = $objects->where('id IN (SELECT product_id from stock_current WHERE amount_aggregated > 0)');
		}

		// add userfields and joins to objects
		foreach ($objects as $object) {
			$this->addUserfieldsAndJoinsToRow($object, $args);
		}

		return $this->ApiResponse($response, $objects);
	}

	public function GetUserfields(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try {
			return $this->ApiResponse($response, $this->getUserfieldsService()->GetValues($args['entity'], $args['objectId']));
		} catch (\Exception $ex) {
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function SetUserfields(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_MASTER_DATA_EDIT);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try {
			if ($requestBody === null) {
				throw new \Exception('Request body could not be parsed (probably invalid JSON format or missing/wrong Content-Type header)');
			}

			$this->getUserfieldsService()->SetValues($args['entity'], $args['objectId'], $requestBody);
			return $this->EmptyApiResponse($response);
		} catch (\Exception $ex) {
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	private function addUserfieldsAndJoinsToRow(Row $object, array $args)
	{
		// add userfields
		$userfields = $this->getUserfieldsService()->GetValues($args['entity'], $object->id);
		if (count($userfields) === 0) {
			$userfields = null;
		}
		$object->userfields = $userfields;

		// add entity-specific joins
		if ($args['entity'] === 'products') {
			$object->product_group = $object->product_group_id !== null ? $this->getDatabase()->product_groups($object->product_group_id) : null;
			$object->location = $object->location_id !== null ? $this->getDatabase()->locations($object->location_id) : null;
			$object->shopping_location = $object->shopping_location_id !== null ? $this->getDatabase()->shopping_locations($object->shopping_location_id) : null;
			$object->qu_purchase = $object->qu_id_purchase !== null ? $this->getDatabase()->quantity_units($object->qu_id_purchase) : null;
			$object->qu_stock = $object->qu_id_stock !== null ? $this->getDatabase()->quantity_units($object->qu_id_stock) : null;
			$object->parent_product = $object->parent_product_id !== null ? $this->getDatabase()->products($object->parent_product_id) : null;
		}

		return $object;
	}

	private function IsEntityWithEditRequiresAdmin($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->schemas->ExposedEntityEditRequiresAdmin->enum);
	}

	private function IsEntityWithNoListing($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->schemas->ExposedEntityNoListing->enum);
	}

	private function IsEntityWithNoEdit($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->schemas->ExposedEntityNoEdit->enum);
	}

	private function IsEntityWithNoDelete($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->schemas->ExposedEntityNoDelete->enum);
	}

	private function IsValidExposedEntity($entity)
	{
		return in_array($entity, $this->getOpenApiSpec()->components->schemas->ExposedEntity->enum);
	}
}
