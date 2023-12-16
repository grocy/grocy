<?php

class ERequirementNotMet extends Exception
{
}

const REQUIRED_PHP_EXTENSIONS = ['fileinfo', 'pdo_sqlite', 'gd', 'ctype', 'intl', 'zlib', 'mbstring',

	// These are core extensions, so normally can't be missing, but seems to be the case, however, on FreeBSD
	'filter', 'iconv', 'tokenizer', 'json'
];

const REQUIRED_PHP_VERSION = '8.2.0';
const REQUIRED_SQLITE_VERSION = '3.34.0';

class PrerequisiteChecker
{
	public function checkRequirements()
	{
		self::checkForPhpVersion();
		self::checkForConfigFile();
		self::checkForConfigDistFile();
		self::checkForComposer();
		self::checkForPhpExtensions();
		self::checkForSqliteVersion();
	}

	private function checkForComposer()
	{
		if (!file_exists(__DIR__ . '/../packages/autoload.php'))
		{
			throw new ERequirementNotMet('/packages/autoload.php not found. Have you run Composer?');
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

	private function checkForPhpVersion()
	{
		$phpVersion = phpversion();
		if (version_compare($phpVersion, REQUIRED_PHP_VERSION, '<'))
		{
			throw new ERequirementNotMet('PHP ' . REQUIRED_PHP_VERSION . ' is required, however you are running ' . $phpVersion);
		}
	}

	private function getSqlVersionAsString()
	{
		$dbh = new PDO('sqlite::memory:');
		return $dbh->query('select sqlite_version()')->fetch()[0];
	}
}
