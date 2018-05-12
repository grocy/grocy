<?php

namespace Grocy\Services;

class ApplicationService extends BaseService
{
	/**
	 * @return boolean
	 */
	public function IsDemoInstallation()
	{
		return file_exists(__DIR__ . '/../data/demo.txt');
	}

	private $InstalledVersion;
	public function GetInstalledVersion()
	{
		if ($this->InstalledVersion == null)
		{
			$this->InstalledVersion = json_decode(file_get_contents(__DIR__ . '/../version.json'));
		}

		return $this->InstalledVersion;
	}
}
