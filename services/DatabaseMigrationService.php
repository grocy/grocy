<?php

namespace Grocy\Services;

class DatabaseMigrationService extends BaseService
{
	public function MigrateDatabase()
	{
		$this->DatabaseService->ExecuteDbStatement("CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");

		$sqlMigrationFiles = array();
		foreach (new \FilesystemIterator(__DIR__ . '/../migrations') as $file)
		{
			if ($file->getExtension() === 'sql')
			{
				$sqlMigrationFiles[$file->getBasename('.sql')] = $file->getPathname();
			}
		}
		ksort($sqlMigrationFiles);
		foreach($sqlMigrationFiles as $migrationNumber => $migrationFile)
		{
			$migrationNumber = ltrim($migrationNumber, '0');
			$this->ExecuteSqlMigrationWhenNeeded($migrationNumber, file_get_contents($migrationFile));
		}

		$phpMigrationFiles = array();
		foreach (new \FilesystemIterator(__DIR__ . '/../migrations') as $file)
		{
			if ($file->getExtension() === 'php')
			{
				$phpMigrationFiles[$file->getBasename('.php')] = $file->getPathname();
			}
		}
		ksort($phpMigrationFiles);
		foreach($phpMigrationFiles as $migrationNumber => $migrationFile)
		{
			$migrationNumber = ltrim($migrationNumber, '0');
			$this->ExecutePhpMigrationWhenNeeded($migrationNumber, $migrationFile);
		}
	}

	private function ExecuteSqlMigrationWhenNeeded(int $migrationId, string $sql)
	{
		$rowCount = $this->DatabaseService->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$this->DatabaseService->ExecuteDbStatement($sql);
			$this->DatabaseService->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}

	private function ExecutePhpMigrationWhenNeeded(int $migrationId, string $phpFile)
	{
		$rowCount = $this->DatabaseService->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if (intval($rowCount) === 0)
		{
			include $phpFile;
			$this->DatabaseService->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}
}
