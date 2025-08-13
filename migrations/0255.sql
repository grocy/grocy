-- Add common Chinese quantity units
INSERT INTO quantity_units (name, name_plural, description) VALUES ('克', '克', 'gram');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('斤', '斤', 'jin (500g)');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('公斤', '公斤', 'kilogram');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('个', '个', 'piece/item');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('包', '包', 'pack');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('瓶', '瓶', 'bottle');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('罐', '罐', 'can/jar');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('升', '升', 'liter');
INSERT INTO quantity_units (name, name_plural, description) VALUES ('毫升', '毫升', 'milliliter');

-- Add conversions between them
-- 1 jin = 500 gram
INSERT INTO quantity_unit_conversions (from_qu_id, to_qu_id, factor)
SELECT from_qu.id, to_qu.id, 500
FROM quantity_units from_qu, quantity_units to_qu
WHERE from_qu.name = '斤' AND to_qu.name = '克';

-- 1 kilogram = 1000 gram
INSERT INTO quantity_unit_conversions (from_qu_id, to_qu_id, factor)
SELECT from_qu.id, to_qu.id, 1000
FROM quantity_units from_qu, quantity_units to_qu
WHERE from_qu.name = '公斤' AND to_qu.name = '克';

-- 1 kilogram = 2 jin
INSERT INTO quantity_unit_conversions (from_qu_id, to_qu_id, factor)
SELECT from_qu.id, to_qu.id, 2
FROM quantity_units from_qu, quantity_units to_qu
WHERE from_qu.name = '公斤' AND to_qu.name = '斤';

-- 1 liter = 1000 milliliter
INSERT INTO quantity_unit_conversions (from_qu_id, to_qu_id, factor)
SELECT from_qu.id, to_qu.id, 1000
FROM quantity_units from_qu, quantity_units to_qu
WHERE from_qu.name = '升' AND to_qu.name = '毫升';
