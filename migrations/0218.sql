ALTER TABLE locations
ADD active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1));

ALTER TABLE shopping_locations
ADD active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1));

ALTER TABLE quantity_units
ADD active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1));

ALTER TABLE product_groups
ADD active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1));

ALTER TABLE task_categories
ADD active TINYINT NOT NULL DEFAULT 1 CHECK(active IN (0, 1));
