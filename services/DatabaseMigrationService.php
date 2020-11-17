<?php

namespace Grocy\Services;

class DatabaseMigrationService extends BaseService
{
	public function MigrateDatabase()
	{
		$this->getDatabaseService()->ExecuteDbStatement("CREATE TABLE IF NOT EXISTS migrations (migration INTEGER NOT NULL PRIMARY KEY UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')))");

		$migrationFiles = [];

		foreach (new \FilesystemIterator(__DIR__ . '/../migrations') as $file)
		{
			$migrationFiles[$file->getBasename()] = $file;
		}

		ksort($migrationFiles);

		foreach ($migrationFiles as $migrationKey => $migrationFile)
		{
			if ($migrationFile->getExtension() === 'php')
			{
				$migrationNumber = ltrim($migrationFile->getBasename('.php'), '0');
				$this->ExecutePhpMigrationWhenNeeded($migrationNumber, $migrationFile->getPathname());
			}
			elseif ($migrationFile->getExtension() === 'sql')
			{
				$migrationNumber = ltrim($migrationFile->getBasename('.sql'), '0');
				$this->ExecuteSqlMigrationWhenNeeded($migrationNumber, file_get_contents($migrationFile->getPathname()));
			}
		}
	}

	private function ExecutePhpMigrationWhenNeeded(int $migrationId, string $phpFile)
	{
		$rowCount = $this->getDatabaseService()->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();

		if (intval($rowCount) === 0)
		{
			include $phpFile;
			$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}

	private function ExecuteSqlMigrationWhenNeeded(int $migrationId, string $sql)
	{
		$rowCount = $this->getDatabaseService()->ExecuteDbQuery('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();

		if (intval($rowCount) === 0)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();

			try
			{
				$this->getDatabaseService()->ExecuteDbStatement($sql);
				$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
			}
			catch (Exception $ex)
			{
				$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
				throw $ex;
			}

			$this->getDatabaseService()->GetDbConnectionRaw()->commit();
		}
	}
}
