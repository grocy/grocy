<?php

namespace Grocy\Controllers;

use \Grocy\Services\FilesService;

class FilesApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function UploadFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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
			file_put_contents($this->getFilesService()->GetFilePath($args['group'], $fileName), $data);

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ServeFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

			$forceServeAs = null;
			if (isset($request->getQueryParams()['force_serve_as']) && !empty($request->getQueryParams()['force_serve_as']))
			{
				$forceServeAs = $request->getQueryParams()['force_serve_as'];
			}

			if ($forceServeAs == FilesService::FILE_SERVE_TYPE_PICTURE)
			{
				$bestFitHeight = null;
				if (isset($request->getQueryParams()['best_fit_height']) && !empty($request->getQueryParams()['best_fit_height']) && is_numeric($request->getQueryParams()['best_fit_height']))
				{
					$bestFitHeight = $request->getQueryParams()['best_fit_height'];
				}

				$bestFitWidth = null;
				if (isset($request->getQueryParams()['best_fit_width']) && !empty($request->getQueryParams()['best_fit_width']) && is_numeric($request->getQueryParams()['best_fit_width']))
				{
					$bestFitWidth = $request->getQueryParams()['best_fit_width'];
				}

				$filePath = $this->getFilesService()->DownscaleImage($args['group'], $fileName, $bestFitHeight, $bestFitWidth);
			}
			else
			{
				$filePath = $this->getFilesService()->GetFilePath($args['group'], $fileName);
			}

			if (file_exists($filePath))
			{
				$response->write(file_get_contents($filePath));
				$response = $response->withHeader('Cache-Control', 'max-age=2592000');
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

	public function DeleteFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

			$filePath = $this->getFilesService()->GetFilePath($args['group'], $fileName);
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
