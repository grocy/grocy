<?php

namespace Grocy\Controllers;

use \Grocy\Services\ApplicationService;

class OpenApiController extends BaseApiController
{
	public function DocumentationUi(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'apidoc');
	}

	public function DocumentationSpec(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$applicationService = new ApplicationService();

		$specJson = json_decode(file_get_contents(__DIR__ . '/../helpers/grocy.openapi.json'));
		$specJson->info->version = $applicationService->GetInstalledVersion();

		return $this->ApiResponse($specJson);
	}
}
