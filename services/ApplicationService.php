<?php

namespace Grocy\Services;

class ApplicationService extends BaseService
{
	private $InstalledVersion;
	public function GetInstalledVersion()
	{
		if ($this->InstalledVersion == null)
		{
			$this->InstalledVersion = json_decode(file_get_contents(__DIR__ . '/../version.json'));
		}

		return $this->InstalledVersion;
	}

	public function GetSystemInfo()
	{
		$pdo = new \PDO('sqlite::memory:');
		$sqliteVersion = $pdo->query('SELECT sqlite_version()')->fetch()[0];
		$pdo = null;

		return array(
			'grocy_version' => $this->GetInstalledVersion(),
			'php_version' => phpversion(),
			'sqlite_version' =>  $sqliteVersion
		);
	}
}
