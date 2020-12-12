<?php

namespace Grocy\Services;

class DatabaseService
{
	private static $DbConnection = null;
	private static $DbConnectionRaw = null;
	private static $instance = null;

	/**
	 * @return boolean|\PDOStatement
	 */
	public function ExecuteDbQuery(string $sql)
	{
		$pdo = $this->GetDbConnectionRaw();

		if ($this->ExecuteDbStatement($sql) === true)
		{
			return $pdo->query($sql);
		}

		return false;
	}

	/**
	 * @return boolean
	 */
	public function ExecuteDbStatement(string $sql)
	{
		$pdo = $this->GetDbConnectionRaw();

		if ($pdo->exec($sql) === false)
		{
			throw new Exception($pdo->errorInfo());
		}

		return true;
	}

	public function GetDbChangedTime()
	{
		return date('Y-m-d H:i:s', filemtime($this->GetDbFilePath()));
	}

	/**
	 * @return \LessQL\Database
	 */
	public function GetDbConnection()
	{
		if (self::$DbConnection == null)
		{
			self::$DbConnection = new \LessQL\Database($this->GetDbConnectionRaw());
		}

		return self::$DbConnection;
	}

	/**
	 * @return \PDO
	 */
	public function GetDbConnectionRaw()
	{
		if (self::$DbConnectionRaw == null)
		{
			$pdo = new \PDO('sqlite:' . $this->GetDbFilePath());
			$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			$pdo->sqliteCreateFunction('regexp', function ($pattern, $value) {
				mb_regex_encoding('UTF-8');
				return (false !== mb_ereg($pattern, $value)) ? 1 : 0;
			});

			self::$DbConnectionRaw = $pdo;
		}

		return self::$DbConnectionRaw;
	}

	public function SetDbChangedTime($dateTime)
	{
		touch($this->GetDbFilePath(), strtotime($dateTime));
	}

	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function GetDbFilePath()
	{
		if (GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease')
		{
			$dbSuffix = GROCY_DEFAULT_LOCALE;
			if (defined('GROCY_DEMO_DB_SUFFIX'))
			{
				$dbSuffix = GROCY_DEMO_DB_SUFFIX;
			}

			return GROCY_DATAPATH . '/grocy_' . $dbSuffix . '.db';
		}

		return GROCY_DATAPATH . '/grocy.db';
	}
}
