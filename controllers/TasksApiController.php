<?php

namespace Grocy\Controllers;

use \Grocy\Services\TasksService;

class TasksApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->TasksService = new TasksService();
	}

	protected $TasksService;

	public function Current(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->ApiResponse($this->TasksService->GetCurrent());
	}
}
