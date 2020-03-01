<?php

namespace Grocy\Services;

#use \Grocy\Services\ApplicationService;

class DatabaseService
{
	private static $instance = null;

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
			return GROCY_DATAPATH . '/grocy_' . GROCY_CULTURE . '.db';
		}

		return GROCY_DATAPATH . '/grocy.db';
	}

    private static $DbConnectionRaw = null;
	/**
	 * @return \PDO
	 */
	public function GetDbConnectionRaw()
	{
		if (self::$DbConnectionRaw == null)
		{
			$pdo = new \PDO('sqlite:' . $this->GetDbFilePath());
			$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::$DbConnectionRaw = $pdo;
		}

		return self::$DbConnectionRaw;
	}

	private static $DbConnection = null;
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

	public function GetDbChangedTime()
	{
		return date('Y-m-d H:i:s', filemtime($this->GetDbFilePath()));
	}

	public function SetDbChangedTime($dateTime)
	{
		touch($this->GetDbFilePath(), strtotime($dateTime));
	}
}
