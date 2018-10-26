ALTER TABLE stock_log
ADD undone TINYINT NOT NULL DEFAULT 0 CHECK(undone IN (0, 1));

ALTER TABLE stock_log
ADD undone_timestamp DATETIME;
