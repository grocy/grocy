<?php

namespace Grocy\Services;

class DatabaseMigrationService extends BaseService
{
	public function MigrateDatabase()
	{
		$this->DatabaseService->ExecuteDbStatement("CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");

		$migrationFiles = array();
		foreach (new \FilesystemIterator(__DIR__ . '/../migrations') as $file)
		{
			$migrationFiles[$file->getBasename('.sql')] = $file->getPathname();
		}
		ksort($migrationFiles);

		foreach($migrationFiles as $migrationNumber => $migrationFile)
		{
			$migrationNumber = ltrim($migrationNumber, '0');
			$this->ExecuteMigrationWhenNeeded($migrationNumber, file_get_contents($migrationFile));
		}
	}

	private function ExecuteMigrationWhenNeeded(int $migrationId, string $sql)
	{
		$rowCount = $this->DatabaseService->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$this->DatabaseService->ExecuteDbStatement($sql);
			$this->DatabaseService->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}
}
