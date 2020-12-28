<?php

namespace Grocy\Services;

class ApplicationService extends BaseService
{
	private $InstalledVersion;

	public function GetChangelog()
	{
		$changelogItems = [];

		foreach (glob(__DIR__ . '/../changelog/*.md') as $file)
		{
			$fileName = basename($file);
			$fileNameParts = explode('_', $fileName);

			$fileContent = file_get_contents($file);
			$version = $fileNameParts[1];
			$releaseDate = explode('.', $fileNameParts[2])[0];
			$releaseNumber = intval($fileNameParts[0]);

			$changelogItems[] = [
				'version' => $version,
				'release_date' => $releaseDate,
				'body' => $fileContent,
				'release_number' => $releaseNumber
			];
		}

		// Sort changelog items to have the changelog descending by newest version
		usort($changelogItems, function ($a, $b) {
			if ($a['release_number'] == $b['release_number'])
			{
				return 0;
			}

			return ($a['release_number'] < $b['release_number']) ? 1 : -1;
		});

		return [
			'changelog_items' => $changelogItems,
			'newest_release_number' => $changelogItems[0]['release_number']
		];
	}

	public function GetInstalledVersion()
	{
		if ($this->InstalledVersion == null)
		{
			$this->InstalledVersion = json_decode(file_get_contents(__DIR__ . '/../version.json'));

			if (GROCY_MODE === 'prerelease')
			{
				$commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
				$commitDate = trim(exec('git log --date=iso --pretty="%cd" -n1 HEAD'));

				$this->InstalledVersion->Version = "pre-release-$commitHash";
				$this->InstalledVersion->ReleaseDate = substr($commitDate, 0, 19);
			}
		}

		return $this->InstalledVersion;
	}

	public function GetSystemInfo()
	{
		$pdo = new \PDO('sqlite::memory:');
		$sqliteVersion = $pdo->query('SELECT sqlite_version()')->fetch()[0];
		$pdo = null;

		return [
			'grocy_version' => $this->GetInstalledVersion(),
			'php_version' => phpversion(),
			'sqlite_version' => $sqliteVersion
		];
	}

	private static function convertToUtc(int $timestamp): string
	{
		$dt = new \DateTime('now', new \DateTimeZone('UTC'));
		$dt->setTimestamp($timestamp);
		return $dt->format('Y-m-d H:i:s');
	}

	private static function getSqliteLocaltime(int $offset): string
	{
		$pdo = new \PDO('sqlite::memory:');
		if ($offset > 0)
		{
			return $pdo->query('SELECT datetime(\'now\', \'+' . $offset . ' seconds\', \'localtime\');')->fetch()[0];
		}
		else
		{
			return $pdo->query('SELECT datetime(\'now\', \'' . $offset . ' seconds\', \'localtime\');')->fetch()[0];
		}
	}

	/**
	 * Returns the response for the API call /system/time
	 * @param int $offset an offset in seconds to be applied
	 * @return array
	 */
	public function GetSystemTime(int $offset = 0): array
	{
		$timestamp = time() + $offset;
		$timeLocal = date('Y-m-d H:i:s', $timestamp);
		$timeUTC = self::convertToUtc($timestamp);
		return [
			'timezone' => date_default_timezone_get(),
			'time_local' => $timeLocal,
			'time_local_sqlite3' => self::getSqliteLocaltime($offset),
			'time_utc' => $timeUTC,
			'timestamp' => $timestamp,
			'offset' => $offset
		];
	}
}
