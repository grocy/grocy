<?php

namespace Grocy\Controllers;

class BaseApiController extends BaseController
{
	protected $OpenApiSpec = null;

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	protected function ApiResponse(\Psr\Http\Message\ResponseInterface $response, $data)
	{
		$response->getBody()->write(json_encode($data));
		return $response;
	}

	protected function EmptyApiResponse(\Psr\Http\Message\ResponseInterface $response, $status = 204)
	{
		return $response->withStatus($status);
	}

	protected function GenericErrorResponse(\Psr\Http\Message\ResponseInterface $response, $errorMessage, $status = 400)
	{
		return $response->withStatus($status)->withJson([
			'error_message' => $errorMessage
		]);
	}

	protected function getOpenApispec()
	{
		if ($this->OpenApiSpec == null)
		{
			$this->OpenApiSpec = json_decode(file_get_contents(__DIR__ . '/../grocy.openapi.json'));
		}

		return $this->OpenApiSpec;
	}

}
