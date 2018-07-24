<?php

namespace Grocy\Services;

class ApplicationService extends BaseService
{
	/**
	 * @return boolean
	 */
	public function IsDemoInstallation()
	{
		return file_exists(GROCY_DATAPATH . '/demo.txt');
	}

	/**
	 * @return boolean
	 */
	public function IsEmbeddedInstallation()
	{
		return file_exists(__DIR__ . '/../embedded.txt');
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
