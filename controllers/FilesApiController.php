<?php

namespace Grocy\Controllers;

use \Grocy\Services\FilesService;

class FilesApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->FilesService = new FilesService();
	}

	protected $FilesService;

	public function Upload(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			if (isset($request->getQueryParams()['file_name']) && !empty($request->getQueryParams()['file_name']) && IsValidFileName($request->getQueryParams()['file_name']))
			{
				$fileName = $request->getQueryParams()['file_name'];
			}
			else
			{
				throw new \Exception('file_name query parameter missing or contains an invalid filename');
			}

			$data = $request->getBody()->getContents();
			file_put_contents($this->FilesService->GetFilePath($args['group'], $fileName), $data);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
