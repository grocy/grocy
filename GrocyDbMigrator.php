<?php

class GrocyDbMigrator
{
	public static function MigrateDb(PDO $pdo)
	{
		self::ExecuteMigrationWhenNeeded($pdo, 1, "
			CREATE TABLE products (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				location_id INTEGER NOT NULL,
				qu_id_purchase INTEGER NOT NULL,
				qu_id_stock INTEGER NOT NULL,
				qu_factor_purchase_to_stock REAL NOT NULL,
				barcode TEXT UNIQUE,
				created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 2, "
			CREATE TABLE locations (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 3, "
			CREATE TABLE quantity_units (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 4, "
			CREATE TABLE stock (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL,
				amount INTEGER NOT NULL,
				best_before_date DATE,
				purchased_date DATE DEFAULT (datetime('now', 'localtime')),
				stock_id TEXT NOT NULL
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 5, "
			CREATE TABLE consumptions (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL,
				amount INTEGER NOT NULL,
				best_before_date DATE,
				purchased_date DATE,
				used_date DATE DEFAULT (datetime('now', 'localtime')),
				spoiled INTEGER NOT NULL DEFAULT 0,
				stock_id TEXT NOT NULL
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 6, "
			INSERT INTO locations (name, description) VALUES ('DefaultLocation', 'This is the first default location, edit or delete it');
			INSERT INTO quantity_units (name, description) VALUES ('DefaultQuantityUnit', 'This is the first default quantity unit, edit or delete it');
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct1', 'This is the first default product, edit or delete it', 1, 1, 1, 1);
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct2', 'This is the second default product, edit or delete it', 1, 1, 1, 1);"
		);
	}

	private static function ExecuteMigrationWhenNeeded(PDO $pdo, int $migrationId, string $sql)
	{
		if ($pdo->query("SELECT COUNT(*) FROM migrations WHERE migration = $migrationId")->fetchColumn() == 0)
		{
			if ($pdo->exec(utf8_encode($sql)) === false)
			{
				throw new Exception($pdo->errorInfo());
			}

			$pdo->exec('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}
}
