<?php

namespace Grocy\Controllers\Api;

use Grocy\Services\FilesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Stream;

class FilesApiController extends BaseApiController
{
	public function DeleteFile(Request $request, Response $response, array $args)
	{
		try
		{
			if (!in_array($args['group'], $this->GetOpenApispec()->components->schemas->FileGroups->enum))
			{
				throw new \Exception('Invalid file group');
			}

			if (IsValidFileName(base64_decode($args['fileName'])))
			{
				$fileName = base64_decode($args['fileName']);
			}
			else
			{
				throw new \Exception('Invalid filename');
			}

			FilesService::GetInstance()->DeleteFile($args['group'], $fileName);

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function ServeFile(Request $request, Response $response, array $args)
	{
		try
		{
			if (!in_array($args['group'], $this->GetOpenApispec()->components->schemas->FileGroups->enum))
			{
				throw new \Exception('Invalid file group');
			}

			if (str_contains($args['fileName'], '_'))
			{
				$fileInfo = explode('_', $args['fileName']);
				$fileName = $this->CheckFileName($fileInfo[0]);
				$fileNameOutput = $this->CheckFileName($fileInfo[1]);
				$filePath = $this->GetFilePath($args['group'], $fileName, $request->getQueryParams());
			}
			else
			{
				$fileName = $this->CheckFileName($args['fileName']);
				$fileNameOutput = $fileName;
				$filePath = $this->GetFilePath($args['group'], $fileName, $request->getQueryParams());
			}

			if (file_exists($filePath))
			{
				$response = $response->withHeader('Cache-Control', 'max-age=2592000');
				$response = $response->withHeader('Content-Type', mime_content_type($filePath));
				$response = $response->withHeader('Content-Disposition', 'inline; filename="' . $fileNameOutput . '"');
				return $response->withBody(new Stream(fopen($filePath, 'rb')));
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

	public function UploadFile(Request $request, Response $response, array $args)
	{
		try
		{
			if (!in_array($args['group'], $this->GetOpenApispec()->components->schemas->FileGroups->enum))
			{
				throw new \Exception('Invalid file group');
			}

			$fileName = $this->CheckFileName($args['fileName']);

			$fileHandle = fopen(FilesService::GetInstance()->GetFilePath($args['group'], $fileName), 'xb');
			if ($fileHandle === false)
			{
				throw new \Exception("Error while creating file $fileName");
			}

			// Save the file to disk in chunks of 1 MB
			$requestBody = $request->getBody();
			while ($data = $requestBody->read(1048576))
			{
				if (fwrite($fileHandle, $data) === false)
				{
					throw new \Exception("Error while writing file $fileName");
				}
			}

			if (fclose($fileHandle) === false)
			{
				throw new \Exception("Error while closing file $fileName");
			}

			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	protected function CheckFileName(string $fileName)
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

	protected function GetFilePath(string $group, string $fileName, array $queryParams = [])
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

			$filePath = FilesService::GetInstance()->DownscaleImage($group, $fileName, $bestFitHeight, $bestFitWidth);
		}
		else
		{
			$filePath = FilesService::GetInstance()->GetFilePath($group, $fileName);
		}

		return $filePath;
	}
}
