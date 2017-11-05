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
				barcode TEXT,
				min_stock_amount INTEGER NOT NULL DEFAULT 0,
				default_best_before_days INTEGER NOT NULL DEFAULT 0,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 2, "
			CREATE TABLE locations (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 3, "
			CREATE TABLE quantity_units (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 4, "
			CREATE TABLE stock (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL,
				amount INTEGER NOT NULL,
				best_before_date DATE,
				purchased_date DATE DEFAULT (datetime('now', 'localtime')),
				stock_id TEXT NOT NULL,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 5, "
			CREATE TABLE stock_log (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL,
				amount INTEGER NOT NULL,
				best_before_date DATE,
				purchased_date DATE,
				used_date DATE,
				spoiled INTEGER NOT NULL DEFAULT 0,
				stock_id TEXT NOT NULL,
				transaction_type TEXT NOT NULL,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 6, "
			INSERT INTO locations (name, description) VALUES ('DefaultLocation', 'This is the first default location, edit or delete it');
			INSERT INTO quantity_units (name, description) VALUES ('DefaultQuantityUnit', 'This is the first default quantity unit, edit or delete it');
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct1', 'This is the first default product, edit or delete it', 1, 1, 1, 1);
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct2', 'This is the second default product, edit or delete it', 1, 1, 1, 1);"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 7, "
			CREATE VIEW stock_missing_products
			AS
			SELECT p.id, MAX(p.name) AS name, p.min_stock_amount - IFNULL(SUM(s.amount), 0) AS amount_missing
			FROM products p
			LEFT JOIN stock s
				ON p.id = s.product_id
			WHERE p.min_stock_amount != 0
			GROUP BY p.id
			HAVING IFNULL(SUM(s.amount), 0) < p.min_stock_amount;"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 8, "
			CREATE VIEW stock_current
			AS
			SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date
			FROM stock
			GROUP BY product_id
			ORDER BY MIN(best_before_date) ASC;"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 9, "
			CREATE TABLE shopping_list (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL UNIQUE,
				amount INTEGER NOT NULL DEFAULT 0,
				amount_autoadded INTEGER NOT NULL DEFAULT 0,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 10, "
			CREATE TABLE habits (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				period_type TEXT NOT NULL,
				period_days INTEGER,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 11, "
			CREATE TABLE habits_log (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				habit_id INTEGER NOT NULL,
				tracked_time DATETIME,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 12, "
			CREATE VIEW habits_current
			AS
			SELECT habit_id, MAX(tracked_time) AS last_tracked_time
			FROM habits_log
			GROUP BY habit_id
			ORDER BY MAX(tracked_time) DESC;"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 13, "
			CREATE TABLE batteries (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				name TEXT NOT NULL UNIQUE,
				description TEXT,
				used_in TEXT,
				charge_interval_days INTEGER NOT NULL DEFAULT 0,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 14, "
			CREATE TABLE battery_charge_cycles (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				battery_id TEXT NOT NULL,
				tracked_time DATETIME,
				row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 15, "
			CREATE VIEW batteries_current
			AS
			SELECT battery_id, MAX(tracked_time) AS last_tracked_time
			FROM battery_charge_cycles
			GROUP BY battery_id
			ORDER BY MAX(tracked_time) DESC;"
		);
	}

	private static function ExecuteMigrationWhenNeeded(PDO $pdo, int $migrationId, string $sql)
	{
		$rowCount = Grocy::ExecuteDbQuery($pdo, 'SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if (intval($rowCount) === 0)
		{
			Grocy::ExecuteDbStatement($pdo, $sql);
			Grocy::ExecuteDbStatement($pdo, 'INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}
}
