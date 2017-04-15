<?php

class Grocy
{
	private static $DbConnection;
	private static $DbConnectionRaw;

	/**
	 * @return PDO
	 */
	private static function GetDbConnectionRaw()
	{
		if (self::$DbConnectionRaw == null)
		{
			$newDb = !file_exists('data/grocy.db');
			$pdo = new PDO('sqlite:data/grocy.db');

			if ($newDb)
			{
				$pdo->exec('PRAGMA encoding = "UTF-8"');
				$pdo->exec("CREATE TABLE migrations (migration INTEGER NOT NULL UNIQUE, execution_time_timestamp DATETIME DEFAULT (datetime('now', 'localtime')), PRIMARY KEY(migration)) WITHOUT ROWID");
				self::MigrateDb();

				if (self::IsDemoInstallation())
				{
					self::PopulateDemoData();
				}
			}

			self::$DbConnectionRaw = $pdo;
		}

		return self::$DbConnectionRaw;
	}

	/**
	 * @return LessQL\Database
	 */
	public static function GetDbConnection()
	{
		if (self::$DbConnection == null)
		{
			self::$DbConnection = new LessQL\Database(self::GetDbConnectionRaw());
		}

		return self::$DbConnection;
	}

	public static function MigrateDb()
	{
		$pdo = self::GetDbConnectionRaw();

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
				purchased_date DATE DEFAULT (datetime('now', 'localtime'))
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 5, "
			CREATE TABLE consumptions (
				id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				product_id INTEGER NOT NULL,
				amount INTEGER NOT NULL,
				used_date DATE DEFAULT (datetime('now', 'localtime')),
				best_before_date DATE,
				purchased_date DATE,
				spoiled INTEGER NOT NULL DEFAULT 0
			)"
		);

		self::ExecuteMigrationWhenNeeded($pdo, 6, "
			INSERT INTO locations (name, description) VALUES ('DefaultLocation', 'This is the first default location, edit or delete it');
			INSERT INTO quantity_units (name, description) VALUES ('DefaultQuantityUnit', 'This is the first default quantity unit, edit or delete it');
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct1', 'This is the first default product, edit or delete it', 1, 1, 1, 1);
			INSERT INTO products (name, description, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('DefaultProduct2', 'This is the second default product, edit or delete it', 1, 1, 1, 1);"
		);
	}

	public static function PopulateDemoData()
	{
		$pdo = self::GetDbConnectionRaw();

		self::ExecuteMigrationWhenNeeded($pdo, -1, utf8_encode("
			UPDATE locations SET name = 'Vorratskammer', description = '' WHERE id = 1;
			INSERT INTO locations (name) VALUES ('Süßigkeitenschrank');
			INSERT INTO locations (name) VALUES ('Konvervenschrank');

			UPDATE quantity_units SET name = 'Stück' WHERE id = 1;
			INSERT INTO quantity_units (name) VALUES ('Packung');

			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Gummibärchen', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Chips', 2, 2, 2, 1);
			INSERT INTO products (name, location_id, qu_id_purchase, qu_id_stock, qu_factor_purchase_to_stock) VALUES ('Eier', 1, 2, 1, 10);

			INSERT INTO stock (product_id, amount, best_before_date) VALUES (3, 5, date('now', '+180 day'));
			INSERT INTO stock (product_id, amount, best_before_date) VALUES (4, 5, date('now', '+180 day'));
			INSERT INTO stock (product_id, amount, best_before_date) VALUES (5, 5, date('now', '+25 day'));
		"));
	}

	private static function ExecuteMigrationWhenNeeded(PDO $pdo, int $migrationId, string $sql)
	{
		if ($pdo->query("SELECT COUNT(*) FROM migrations WHERE migration = $migrationId")->fetchColumn() == 0)
		{
			$pdo->exec($sql);
			$pdo->exec('INSERT INTO migrations (migration) VALUES (' . $migrationId . ')');
		}
	}

	public static function FindObjectInArrayByPropertyValue($array, $propertyName, $propertyValue)
	{
		foreach($array as $object)
		{
			if($object->{$propertyName} == $propertyValue)
			{
				return $object;
			}
		}

		return null;
	}

	public static function GetCurrentStock()
	{
		$db = self::GetDbConnectionRaw();
		return $db->query('SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date from stock GROUP BY product_id ORDER BY MIN(best_before_date) DESC')->fetchAll(PDO::FETCH_OBJ);
	}

	public static function IsDemoInstallation()
	{
		return file_exists('data/demo.txt');
	}
}
