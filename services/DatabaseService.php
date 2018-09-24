<?php

namespace Grocy\Services;

use \Grocy\Services\ApplicationService;

class DatabaseService
{
	private $DbConnectionRaw;
	/**
	 * @return \PDO
	 */
	public function GetDbConnectionRaw()
	{
		if ($this->DbConnectionRaw == null)
		{
			$pdo = new \PDO('sqlite:' . GROCY_DATAPATH . '/grocy.db');
			$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->DbConnectionRaw = $pdo;
		}

		return $this->DbConnectionRaw;
	}

	private $DbConnection;
	/**
	 * @return \LessQL\Database
	 */
	public function GetDbConnection()
	{
		if ($this->DbConnection == null)
		{
			$this->DbConnection = new \LessQL\Database($this->GetDbConnectionRaw());
		}

		return $this->DbConnection;
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
		return date('Y-m-d H:i:s', filemtime(GROCY_DATAPATH . '/grocy.db'));
	}
}
