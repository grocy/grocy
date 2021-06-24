<?php

namespace Grocy\Controllers;

use Grocy\Services\FilesService;
use Slim\Exception\HttpNotFoundException;

class FilesApiController extends BaseApiController
{
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

	public function ServeFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$fileName = $this->checkFileName($args['fileName']);

			$filePath = $this->getFilePath($args['group'], $fileName, $request->getQueryParams());

			if (file_exists($filePath))
			{
				$response->write(file_get_contents($filePath));
				$response = $response->withHeader('Cache-Control', 'max-age=2592000');
				$response = $response->withHeader('Content-Type', mime_content_type($filePath));
				return $response->withHeader('Content-Disposition', 'inline; filename="' . $fileName . '"');
			}
			else
			{
				throw new HttpNotFoundException($request, 'File not found');
			}
		}
		catch (\Exception $ex)
		{
			throw new HttpNotFoundException($request, $ex->getMessage(), $ex);
		}
	}

	public function ShowFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$fileInfo = explode('_', $args['fileName']);
			$fileName = $this->checkFileName($fileInfo[1]);

			$filePath = $this->getFilePath($args['group'], base64_decode($fileInfo[0]), $request->getQueryParams());

			if (file_exists($filePath))
			{
				// TODO: ACCEPT JSON
				$response->write(file_get_contents($filePath));
				$response = $response->withHeader('Cache-Control', 'max-age=2592000');
				$response = $response->withHeader('Content-Type', mime_content_type($filePath));
				return $response->withHeader('Content-Disposition', 'inline; filename="' . $fileName . '"');
			}
			else
			{
				throw new HttpNotFoundException($request, 'File not found');
			}
		}
		catch (\Exception $ex)
		{
			throw new HttpNotFoundException($request, $ex->getMessage(), $ex);
		}
	}

	public function UploadFile(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$fileName = $this->checkFileName($args['fileName']);

			$data = $request->getBody()->getContents();
			file_put_contents($this->getFilesService()->GetFilePath($args['group'], $fileName), $data);

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	/**
	 * @param string $fileName base64-encoded file-name
	 * @return false|string the decoded file-name
	 * @throws \Exception if the file-name is invalid.
	 */
	protected function checkFileName(string $fileName)
	{
		if (IsValidFileName(base64_decode($fileName)))
		{
			$fileName = base64_decode($fileName);
		}
		else
		{
			throw new \Exception('Invalid filename');
		}

		return $fileName;
	}

	/**
	 * @param string $group The group the requested files belongs to.
	 * @param string $fileName The name of the requested file.
	 * @param array $queryParams Parameter, e.g. for scaling. Optional.
	 * @return string
	 */
	protected function getFilePath(string $group, string $fileName, array $queryParams = [])
	{
		$forceServeAs = null;

		if (isset($queryParams['force_serve_as']) && !empty($queryParams['force_serve_as']))
		{
			$forceServeAs = $queryParams['force_serve_as'];
		}

		if ($forceServeAs == FilesService::FILE_SERVE_TYPE_PICTURE)
		{
			$bestFitHeight = null;

			if (isset($queryParams['best_fit_height']) && !empty($queryParams['best_fit_height']) && is_numeric($queryParams['best_fit_height']))
			{
				$bestFitHeight = $queryParams['best_fit_height'];
			}

			$bestFitWidth = null;

			if (isset($queryParams['best_fit_width']) && !empty($queryParams['best_fit_width']) && is_numeric($queryParams['best_fit_width']))
			{
				$bestFitWidth = $queryParams['best_fit_width'];
			}

			$filePath = $this->getFilesService()->DownscaleImage($group, $fileName, $bestFitHeight, $bestFitWidth);
		}
		else
		{
			$filePath = $this->getFilesService()->GetFilePath($group, $fileName);
		}

		return $filePath;
	}
}
