<?php

namespace Grocy\Controllers;

class BaseApiController extends BaseController
{

	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->OpenApiSpec = json_decode(file_get_contents(__DIR__ . '/../grocy.openapi.json'));
	}

	protected $OpenApiSpec;

	protected function ApiResponse($data)
	{
		return json_encode($data);
	}

	protected function VoidApiActionResponse($response, $success = true, $status = 200, $errorMessage = '')
	{
		return $response->withStatus($status)->withJson(array(
			'success' => $success,
			'error_message' => $errorMessage
		));
	}
}
