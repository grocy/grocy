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

INSERT INTO permission_hierarchy
    (name, parent)
VALUES
    ('ADMIN', NULL);

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

INSERT INTO permission_hierarchy
    (name, parent)
VALUES
    ('CREATE_USER', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

INSERT INTO permission_hierarchy
    (name, parent)
VALUES
    ('EDIT_USER', last_insert_rowid());

INSERT INTO permission_hierarchy
    (name, parent)
VALUES
    ('READ_USER', last_insert_rowid()),
    ('EDIT_SELF', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));

INSERT INTO permission_hierarchy
    (name, parent)
VALUES
    -- Batteries
    ('BATTERY_UNDO_TRACK_CHARGE_CYCLE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('BATTERY_TRACK_CHARGE_CYCLE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- Chores
    ('CHORE_TRACK', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('CHORE_TRACK_OTHERS', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('CHORE_EDIT', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('CHORE_UNDO', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- Files
    ('UPLOAD_FILE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('DELETE_FILE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- master data
    ('MASTER_DATA_EDIT', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- Tasks
    ('TASKS_UNDO', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('TASKS_MARK_COMPLETED', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- Stock / Products
    ('STOCK_EDIT', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('STOCK_TRANSFER', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('STOCK_CORRECTION', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('PRODUCT_PURCHASE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('PRODUCT_CONSUME', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('PRODUCT_OPEN', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    -- shopping list
    ('SHOPPINGLIST_ITEMS_ADD', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN')),
    ('SHOPPINGLIST_ITEMS_DELETE', (SELECT id FROM permission_hierarchy WHERE name = 'ADMIN'));
