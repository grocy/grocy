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
			if (IsValidFileName(base64_decode($args['fileName'])))
			{
				$fileName = base64_decode($args['fileName']);
			}
			else
			{
				throw new \Exception('Invalid filename');
			}

			$data = $request->getBody()->getContents();
			file_put_contents($this->FilesService->GetFilePath($args['group'], $fileName), $data);

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ServeFile(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			if (IsValidFileName(base64_decode($args['fileName'])))
			{
				$fileName = base64_decode($args['fileName']);
			}
			else
			{
				throw new \Exception('Invalid filename');
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
				return $this->GenericErrorResponse($response, 'File not found', 404);
			}
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function DeleteFile(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			if (IsValidFileName(base64_decode($args['fileName'])))
			{
				$fileName = base64_decode($args['fileName']);
			}
			else
			{
				throw new \Exception('Invalid filename');
			}

			$filePath = $this->FilesService->GetFilePath($args['group'], $fileName);
			if (file_exists($filePath))
			{
				unlink($filePath);
			}

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
