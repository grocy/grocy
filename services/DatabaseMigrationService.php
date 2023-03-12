<?php

namespace Grocy\Services;

class DatabaseMigrationService extends BaseService
{
	// This migration will be always executed, can be used to fix things manually (will never be shipped)
	const EMERGENCY_MIGRATION_ID = 9999;

	// This migration will be always executed, is used for things which need to be checked always
	const DOALWAYS_MIGRATION_ID = 8888;

	public function MigrateDatabase()
	{
		$this->getDatabaseService()->ExecuteDbStatement("CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");

		$migrationFiles = [];
		foreach (new \FilesystemIterator(__DIR__ . '/../migrations') as $file)
		{
			$migrationFiles[$file->getBasename()] = $file;
		}
		ksort($migrationFiles);

		$migrationCounter = 0;
		foreach ($migrationFiles as $migrationKey => $migrationFile)
		{
			if ($migrationFile->getExtension() === 'php')
			{
				$migrationNumber = ltrim($migrationFile->getBasename('.php'), '0');
				$this->ExecutePhpMigrationWhenNeeded($migrationNumber, $migrationFile->getPathname(), $migrationCounter);
			}
			elseif ($migrationFile->getExtension() === 'sql')
			{
				$migrationNumber = ltrim($migrationFile->getBasename('.sql'), '0');
				$this->ExecuteSqlMigrationWhenNeeded($migrationNumber, file_get_contents($migrationFile->getPathname()), $migrationCounter);
			}
		}

		if ($migrationCounter > 0)
		{
			$this->getDatabaseService()->ExecuteDbStatement('VACUUM');
		}
	}

	private function ExecutePhpMigrationWhenNeeded(int $migrationId, string $phpFile, int &$migrationCounter)
	{
		$rowCount = $this->getDatabaseService()->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if ($rowCount == 0 || $migrationId == self::EMERGENCY_MIGRATION_ID || $migrationId == self::DOALWAYS_MIGRATION_ID)
		{
			include $phpFile;

			if ($migrationId != self::EMERGENCY_MIGRATION_ID && $migrationId != self::DOALWAYS_MIGRATION_ID)
			{
				$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
				$migrationCounter++;
			}
		}
	}

	private function ExecuteSqlMigrationWhenNeeded(int $migrationId, string $sql, int &$migrationCounter)
	{
		$rowCount = $this->getDatabaseService()->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if ($rowCount == 0 || $migrationId == self::EMERGENCY_MIGRATION_ID || $migrationId == self::DOALWAYS_MIGRATION_ID)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();

			try
			{
				$this->getDatabaseService()->ExecuteDbStatement($sql);

				if ($migrationId != self::EMERGENCY_MIGRATION_ID && $migrationId != self::DOALWAYS_MIGRATION_ID)
				{
					$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
					$migrationCounter++;
				}
			}
			catch (\Exception $ex)
			{
				$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
				throw $ex;
			}

			$this->getDatabaseService()->GetDbConnectionRaw()->commit();
		}
	}
}
