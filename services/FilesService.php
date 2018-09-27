<?php

namespace Grocy\Services;

class FilesService extends BaseService
{
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
}
