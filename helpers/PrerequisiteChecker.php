<?php

class ERequirementNotMet extends Exception
{
}

const REQUIRED_PHP_EXTENSIONS = ['fileinfo', 'pdo_sqlite', 'gd', 'ctype', 'json', 'intl', 'zlib'];
const REQUIRED_SQLITE_VERSION = '3.9.0';

class PrerequisiteChecker
{
	public function checkRequirements()
	{
		self::checkForConfigFile();
		self::checkForConfigDistFile();
		self::checkForComposer();
		self::checkForPhpExtensions();
		self::checkForSqliteVersion();
	}

	private function checkForComposer()
	{
		if (!file_exists(__DIR__ . '/../vendor/autoload.php'))
		{
			throw new ERequirementNotMet('/vendor/autoload.php not found. Have you run Composer?');
		}
	}

	private function checkForConfigDistFile()
	{
		if (!file_exists(__DIR__ . '/../config-dist.php'))
		{
			throw new ERequirementNotMet('config-dist.php not found. Please do not remove this file.');
		}
	}

	private function checkForConfigFile()
	{
		if (!file_exists(GROCY_DATAPATH . '/config.php'))
		{
			throw new ERequirementNotMet('config.php in data directory (' . GROCY_DATAPATH . ') not found. Have you copied config-dist.php to the data directory and renamed it to config.php?');
		}
	}

	private function checkForPhpExtensions()
	{
		$loadedExtensions = get_loaded_extensions();

		foreach (REQUIRED_PHP_EXTENSIONS as $extension)
		{
			if (!in_array($extension, $loadedExtensions))
			{
				throw new ERequirementNotMet("PHP module '{$extension}' not installed, but required.");
			}
		}
	}

	private function checkForSqliteVersion()
	{
		$sqliteVersion = self::getSqlVersionAsString();

		if (version_compare($sqliteVersion, REQUIRED_SQLITE_VERSION, '<'))
		{
			throw new ERequirementNotMet('SQLite ' . REQUIRED_SQLITE_VERSION . ' is required, however you are running ' . $sqliteVersion);
		}
	}

	private function getSqlVersionAsString()
	{
		$dbh = new PDO('sqlite::memory:');
		return $dbh->query('select sqlite_version()')->fetch()[0];
	}
}
