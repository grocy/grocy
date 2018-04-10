<?php

class DatabaseService
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
			$pdo = new PDO('sqlite:' . __DIR__ . '/../data/grocy.db');
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			if ($doMigrations === true)
			{
				self::ExecuteDbStatement($pdo, "CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");
				GrocyDbMigrator::MigrateDb($pdo);

				if (ApplicationService::IsDemoInstallation())
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
}
