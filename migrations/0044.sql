ALTER TABLE stock_log
ADD undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1));

UPDATE stock_log
SET undone = 0;

ALTER TABLE stock_log
ADD undone_timestamp DATETIME;

ALTER TABLE chores_log
ADD undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1));

UPDATE chores_log
SET undone = 0;

ALTER TABLE chores_log
ADD undone_timestamp DATETIME;

ALTER TABLE battery_charge_cycles
ADD undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1));

UPDATE battery_charge_cycles
SET undone = 0;

ALTER TABLE battery_charge_cycles
ADD undone_timestamp DATETIME;
