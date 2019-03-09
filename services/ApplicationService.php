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

	public function GetChangelog()
	{
		$changelogItems = array();
		foreach(glob(__DIR__ . '/../changelog/*.md') as $file)
		{
			$fileName = basename($file);
			$fileNameParts = explode('_', $fileName);

			$fileContent = file_get_contents($file);
			$version = $fileNameParts[1];
			$releaseDate = explode('.', $fileNameParts[2])[0];
			$releaseNumber = intval($fileNameParts[0]);

			$changelogItems[] = array(
				'version' => $version,
				'release_date' => $releaseDate,
				'body' => $fileContent,
				'release_number' => $releaseNumber
			);
		}

		// Sort changelog items to have the changelog descending by newest version
		usort($changelogItems, function($a, $b)
		{
			if ($a['release_number'] == $b['release_number'])
			{
				return 0;
			}

			return ($a['release_number'] < $b['release_number']) ? 1 : -1;
		});

		return array(
			'changelog_items' => $changelogItems,
			'newest_release_number' => $changelogItems[0]['release_number']
		);
	}
}
