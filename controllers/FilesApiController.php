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

	public function UploadFile(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function ServeFile(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

			$filePath = $this->FilesService->GetFilePath($args['group'], $fileName);

			if (file_exists($filePath))
			{
				$response->write(file_get_contents($filePath));
				$response = $response->withHeader('Content-Type', mime_content_type($filePath));
				return $response->withHeader('Content-Disposition', 'inline; filename="' . $fileName . '"');
			}
			else
			{
				return $this->VoidApiActionResponse($response, false, 404, 'File not found');
			}
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}

	public function DeleteFile(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

			$filePath = $this->FilesService->GetFilePath($args['group'], $fileName);
			if (file_exists($filePath))
			{
				unlink($filePath);
			}

			return $this->ApiResponse(array('success' => true));
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
