<?php

class Grocy
{
	private static $DbConnectionRaw;
	/**
	 * @return PDO
	 */
	public static function GetDbConnectionRaw($doMigrations = false)
	{
		if ($doMigrations === true)
		{
			self::$DbConnectionRaw = null;
		}

		if (self::$DbConnectionRaw == null)
		{
			$pdo = new PDO('sqlite:' . __DIR__ . '/data/grocy.db');
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			if ($doMigrations === true)
			{
				Grocy::ExecuteDbStatement($pdo, "CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");
				GrocyDbMigrator::MigrateDb($pdo);

				if (self::IsDemoInstallation())
				{
					GrocyDemoDataGenerator::PopulateDemoData($pdo);
				}
			}

			self::$DbConnectionRaw = $pdo;
		}

		return self::$DbConnectionRaw;
	}

	private static $DbConnection;
	/**
	 * @return LessQL\Database
	 */
	public static function GetDbConnection($doMigrations = false)
	{
		if ($doMigrations === true)
		{
			self::$DbConnection = null;
		}

		if (self::$DbConnection == null)
		{
			self::$DbConnection = new LessQL\Database(self::GetDbConnectionRaw($doMigrations));
		}

		return self::$DbConnection;
	}

	/**
	 * @return boolean
	 */
	public static function ExecuteDbStatement(PDO $pdo, string $sql)
	{
		if ($pdo->exec($sql) === false)
		{
			throw new Exception($pdo->errorInfo());
		}

		return true;
	}

	/**
	 * @return boolean|PDOStatement
	 */
	public static function ExecuteDbQuery(PDO $pdo, string $sql)
	{
		if (self::ExecuteDbStatement($pdo, $sql) === true)
		{
			return $pdo->query($sql);
		}

		return false;
	}

	/**
	 * @return boolean
	 */
	public static function IsDemoInstallation()
	{
		return file_exists(__DIR__ . '/data/demo.txt');
	}

	private static $InstalledVersion;
	/**
	 * @return string
	 */
	public static function GetInstalledVersion()
	{
		if (self::$InstalledVersion == null)
		{
			self::$InstalledVersion = preg_replace("/\r|\n/", '', file_get_contents(__DIR__ . '/version.txt'));
		}

		return self::$InstalledVersion;
	}

	/**
	 * @return boolean
	 */
	public static function IsValidSession($sessionKey)
	{
		if ($sessionKey === null || empty($sessionKey))
		{
			return false;
		}
		else
		{
			return file_exists(__DIR__ . "/data/sessions/$sessionKey.txt");
		}
	}

	/**
	 * @return string
	 */
	public static function CreateSession()
	{
		if (!file_exists(__DIR__ . '/data/sessions'))
		{
			mkdir(__DIR__ . '/data/sessions');
		}

		$now = time();
		foreach (new FilesystemIterator(__DIR__ . '/data/sessions') as $file)
		{
			if ($now - $file->getCTime() >= 2678400) //31 days
			{
				unlink(__DIR__ . '/data/sessions/' . $file->getFilename());
			}
		}

		$newSessionKey = uniqid() . uniqid() . uniqid();
		file_put_contents(__DIR__ . "/data/sessions/$newSessionKey.txt", '');
		return $newSessionKey;
	}

	public static function RemoveSession($sessionKey)
	{
		unlink(__DIR__ . "/data/sessions/$sessionKey.txt");
	}
}
