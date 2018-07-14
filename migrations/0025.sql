CREATE TABLE recipes (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE TABLE recipes_pos (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	recipe_id INTEGER NOT NULL,
	product_id INTEGER NOT NULL,
	amount INTEGER NOT NULL DEFAULT 0,
	note TEXT,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

CREATE VIEW recipes_fulfillment
AS
SELECT
	r.id AS recipe_id,
	rp.id AS recipe_pos_id,
	rp.product_id,
	rp.amount AS recipe_amount,
	sc.amount AS stock_amount,
	CASE WHEN sc.amount >= rp.amount THEN 1 ELSE 0 END AS need_fullfiled
FROM recipes r
LEFT JOIN recipes_pos rp
	ON r.id = rp.recipe_id
LEFT JOIN stock_current sc
	ON rp.product_id = sc.product_id;

CREATE VIEW recipes_fulfillment_sum
AS
SELECT
	rf.recipe_id,
	MIN(rf.need_fullfiled) AS need_fullfiled
FROM recipes_fulfillment rf
GROUP BY rf.recipe_id;
