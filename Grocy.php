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
				Grocy::ExecuteDbStatement($pdo, "CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')), PRIMARY KEY(migration)) WITHOUT ROWID");
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
		if ($pdo->exec(utf8_encode($sql)) === false)
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
			return $pdo->query(utf8_encode($sql));
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
			self::$InstalledVersion = file_get_contents(__DIR__ . '/version.txt');
		}

		return self::$InstalledVersion;
	}
}
