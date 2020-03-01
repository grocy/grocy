<?php

namespace Grocy\Controllers;

class TasksApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function Current(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->ApiResponse($response, $this->getTasksService()->GetCurrent());
	}

	public function MarkTaskAsCompleted(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$requestBody = $request->getParsedBody();

		try
		{
			$doneTime = date('Y-m-d H:i:s');
			if (array_key_exists('done_time', $requestBody) && IsIsoDateTime($requestBody['done_time']))
			{
				$doneTime = $requestBody['done_time'];
			}

			$this->getTasksService()->MarkTaskAsCompleted($args['taskId'], $doneTime);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoTask(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$this->getTasksService()->UndoTask($args['taskId']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
