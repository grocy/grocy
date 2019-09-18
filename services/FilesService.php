<?php

namespace Grocy\Services;

use \Gumlet\ImageResize;

class FilesService extends BaseService
{
	const FILE_SERVE_TYPE_PICTURE = 'picture';

	public function __construct()
	{
		parent::__construct();
		
		$this->StoragePath = GROCY_DATAPATH . '/storage';
		
		if (!file_exists($this->StoragePath))
		{
			mkdir($this->StoragePath);
		}
	}

	private $StoragePath;

	public function GetFilePath($group, $fileName)
	{
		$groupFolderPath = $this->StoragePath . '/' . $group;
		if (!file_exists($groupFolderPath))
		{
			mkdir($groupFolderPath);
		}

		return  $groupFolderPath . '/' . $fileName;
	}

	public function DownscaleImage($group, $fileName, $bestFitHeight, $bestFitWidth)
	{
		$filePath = $this->GetFilePath($group, $fileName);
		$fileNameWithoutExtension = pathinfo($filePath, PATHINFO_FILENAME);
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

		$fileNameDownscaled = $fileNameWithoutExtension . '__downscaledto' . $bestFitHeight . 'x' . $bestFitWidth . '.' . $fileExtension;
		$filePathDownscaled = $this->GetFilePath($group, $fileNameDownscaled);

		try
		{
			if (!file_exists($filePathDownscaled))
			{
				$image = new ImageResize($filePath);
				$image->resizeToBestFit($bestFitHeight, $bestFitWidth);
				$image->save($filePathDownscaled);
			}
		}
		catch (ImageResizeException $ex)
		{
			return $filePath;
		}

		return $filePathDownscaled;
	}
}
