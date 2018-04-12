<?php

namespace Grocy\Controllers;

class GenericEntityApiController extends BaseApiController
{
	public function GetObjects(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->Database->{$args['entity']}());
	}

	public function GetObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->Database->{$args['entity']}($args['objectId']));
	}

	public function AddObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$newRow = $this->Database->{$args['entity']}()->createRow($request->getParsedBody());
		$newRow->save();
		$success = $newRow->isClean();
		return $this->ApiResponse(array('success' => $success));
	}

	public function EditObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$row = $this->Database->{$args['entity']}($args['objectId']);
		$row->update($request->getParsedBody());
		$success = $row->isClean();
		return $this->ApiResponse(array('success' => $success));
	}

	public function DeleteObject(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$row = $this->Database->{$args['entity']}($args['objectId']);
		$row->delete();
		$success = $row->isClean();
		return $this->ApiResponse(array('success' => $success));
	}
}
