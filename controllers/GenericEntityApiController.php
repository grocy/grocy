<?php

namespace Grocy\Controllers;

class GenericEntityApiController extends BaseApiController
{
	public function GetObjects($request, $response, $args)
	{
		return $this->ApiEncode($this->Database->{$args['entity']}());
	}

	public function GetObject($request, $response, $args)
	{
		return $this->ApiEncode($this->Database->{$args['entity']}($args['objectId']));
	}

	public function AddObject($request, $response, $args)
	{
		$newRow = $this->Database->{$args['entity']}()->createRow($request->getParsedBody());
		$newRow->save();
		$success = $newRow->isClean();
		return $this->ApiEncode(array('success' => $success));
	}

	public function EditObject($request, $response, $args)
	{
		$row = $this->Database->{$args['entity']}($args['objectId']);
		$row->update($request->getParsedBody());
		$success = $row->isClean();
		return $this->ApiEncode(array('success' => $success));
	}

	public function DeleteObject($request, $response, $args)
	{
		$row = $this->Database->{$args['entity']}($args['objectId']);
		$row->delete();
		$success = $row->isClean();
		return $this->ApiEncode(array('success' => $success));
	}
}
