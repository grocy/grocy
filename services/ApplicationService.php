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
	/**
	 * @return string
	 */
	public function GetInstalledVersion()
	{
		if ($this->InstalledVersion == null)
		{
			$this->InstalledVersion = preg_replace("/\r|\n/", '', file_get_contents(__DIR__ . '/../version.txt'));
		}

		return $this->InstalledVersion;
	}
}
