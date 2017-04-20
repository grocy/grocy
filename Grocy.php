<?php

class Grocy
{
	private static $DbConnectionRaw;
	/**
	 * @return PDO
	 */
	public static function GetDbConnectionRaw()
	{
		if (self::$DbConnectionRaw == null)
		{
			$newDb = !file_exists(__DIR__ . '/data/grocy.db');
			$pdo = new PDO('sqlite:' . __DIR__ . '/data/grocy.db');
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			if ($newDb)
			{
				$pdo->exec("CREATE TABLE migrations (migration INTEGER NOT NULL UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')), PRIMARY KEY(migration)) WITHOUT ROWID");
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
	public static function GetDbConnection()
	{
		if (self::$DbConnection == null)
		{
			self::$DbConnection = new LessQL\Database(self::GetDbConnectionRaw());
		}

		return self::$DbConnection;
	}

	public static function IsDemoInstallation()
	{
		return file_exists(__DIR__ . '/data/demo.txt');
	}

	private static $InstalledVersion;
	public static function GetInstalledVersion()
	{
		if (self::$InstalledVersion == null)
		{
			self::$InstalledVersion = file_get_contents(__DIR__ . '/version.txt');
		}

		return self::$InstalledVersion;
	}
}
