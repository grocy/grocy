CREATE TABLE user_permissions
(
    id            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    permission_id INTEGER NOT NULL,
    user_id       INTEGER NOT NULL,
    UNIQUE (user_id, permission_id)
);

CREATE TABLE permission_hierarchy
(
    id     INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    name   TEXT    NOT NULL UNIQUE,
    /* if the user has the parent permission,
 the user also has the child permission */
    parent INTEGER NULL
);

INSERT INTO permission_hierarchy(name, parent)
VALUES ('ADMIN', NULL);
INSERT INTO user_permissions(permission_id, user_id)
VALUES (last_insert_rowid(), (SELECT MIN(id) FROM users)); -- The first user (normally "admin") starts as ADMIN


DROP VIEW IF EXISTS permission_tree;
CREATE VIEW permission_tree
AS
WITH RECURSIVE perm AS (SELECT id AS root, id AS child, name, parent
                        FROM permission_hierarchy
                        UNION
                        SELECT perm.root, ph.id, ph.name, ph.id
                        FROM permission_hierarchy ph,
                             perm
                        WHERE ph.parent = perm.child
)
SELECT root AS id, name AS name
FROM perm;

DROP VIEW IF EXISTS permission_check;
CREATE VIEW permission_check
AS
SELECT u.id    AS id, -- dummy for LessQL
       u.id    AS user_id,
       pt.name AS permission_name
FROM permission_tree pt,
     users u
WHERE pt.id IN (SELECT permission_id FROM user_permissions sub_up WHERE sub_up.user_id = u.id);


DROP VIEW IF EXISTS uihelper_permission;
CREATE VIEW uihelper_permission
AS
SELECT ph.id     AS id,
       u.id      AS user_id,
       ph.name   AS permission_name,
       ph.id     AS permission_id,
       (ph.name IN
        (SELECT pc.permission_name FROM permission_check pc WHERE pc.user_id = u.id)
           )     AS has_permission,
       ph.parent AS parent
FROM users u,
     permission_hierarchy ph;