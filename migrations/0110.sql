CREATE TABLE user_permissions
(
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	permission_id INTEGER NOT NULL,
	user_id INTEGER NOT NULL,

	UNIQUE (user_id, permission_id)
);

CREATE TABLE permission_hierarchy
(
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE,
	parent INTEGER NULL -- If the user has the parent permission, the user also has the child permission
);

-- The root/ADMIN permission
INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('ADMIN', NULL);

-- User add/edit/read permissions
INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('USERS', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('USERS_CREATE', (SELECT id FROM permission_hierarchy WHERE name = 'USERS'));

INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('USERS_EDIT', last_insert_rowid());

INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('USERS_READ', last_insert_rowid()),
	('USERS_EDIT_SELF', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

-- Base permissions per major feature
INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	('STOCK', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('SHOPPINGLIST', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('RECIPES', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('CHORES', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('BATTERIES', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('TASKS', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('EQUIPMENT', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
	('CALENDAR', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

-- Sub feature permissions
INSERT INTO permission_hierarchy
	(name, parent)
VALUES
	-- Stock
	('STOCK_PURCHASE', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),
	('STOCK_CONSUME', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),
	('STOCK_INVENTORY', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),
	('STOCK_TRANSFER', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),
	('STOCK_OPEN', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),
	('STOCK_EDIT', (SELECT id FROM permission_hierarchy WHERE name = 'STOCK')),

	-- Shopping list
	('SHOPPINGLIST_ITEMS_ADD', (SELECT id FROM permission_hierarchy WHERE name = 'SHOPPINGLIST')),
	('SHOPPINGLIST_ITEMS_DELETE', (SELECT id FROM permission_hierarchy WHERE name = 'SHOPPINGLIST')),

	-- Recipes
	('RECIPES_MEALPLAN', (SELECT id FROM permission_hierarchy WHERE name = 'RECIPES')),

	-- Chores
	('CHORE_TRACK_EXECUTION', (SELECT id FROM permission_hierarchy WHERE name = 'CHORES')),
	('CHORE_UNDO_EXECUTION', (SELECT id FROM permission_hierarchy WHERE name = 'CHORES')),

	-- Batteries
	('BATTERIES_TRACK_CHARGE_CYCLE', (SELECT id FROM permission_hierarchy WHERE name = 'BATTERIES')),
	('BATTERIES_UNDO_CHARGE_CYCLE', (SELECT id FROM permission_hierarchy WHERE name = 'BATTERIES')),

	-- Tasks
	('TASKS_UNDO_EXECUTION', (SELECT id FROM permission_hierarchy WHERE name = 'TASKS')),
	('TASKS_MARK_COMPLETED', (SELECT id FROM permission_hierarchy WHERE name = 'TASKS')),

	-- Others
	('MASTER_DATA_EDIT', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

-- All existing users get the ADMIN permission
INSERT INTO user_permissions
	(permission_id, user_id)
SELECT (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'), id
FROM users;

CREATE VIEW permission_tree
AS
WITH RECURSIVE perm AS (
	SELECT id AS root, id AS child, name, parent
	FROM permission_hierarchy
	UNION
	SELECT perm.root, ph.id, ph.name, ph.id
	FROM permission_hierarchy ph, perm
	WHERE ph.parent = perm.child
)
SELECT root AS id, name AS name
FROM perm;

CREATE VIEW user_permissions_resolved
AS
SELECT
	u.id AS id, -- Dummy for LessQL
	u.id AS user_id,
	pt.name AS permission_name
FROM permission_tree pt, users u
WHERE pt.id IN (SELECT permission_id FROM user_permissions sub_up WHERE sub_up.user_id = u.id);

CREATE VIEW uihelper_user_permissions
AS
SELECT
	ph.id AS id,
	u.id AS user_id,
	ph.name AS permission_name,
	ph.id AS permission_id,
	(ph.name IN (
			SELECT pc.permission_name
			FROM user_permissions_resolved pc
			WHERE pc.user_id = u.id
		)
	) AS has_permission,
	ph.parent AS parent
FROM users u, permission_hierarchy ph;
