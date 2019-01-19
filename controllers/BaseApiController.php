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

	protected function EmptyApiResponse($response, $status = 204)
	{
		return $response->withStatus($status);
	}

	protected function GenericErrorResponse($response, $errorMessage, $status = 400)
	{
		return $response->withStatus($status)->withJson(array(
			'error_message' => $errorMessage
		));
	}
}
